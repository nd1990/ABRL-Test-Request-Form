<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function generateNumber(): string
    {
        return DB::transaction(function () {
            $prefix = Setting::get('invoice', 'prefix', 'INV') ?: 'INV';
            $year = now()->format('Y');
            $pattern = $prefix . '/' . $year . '/%';

            $last = Invoice::where('invoice_number', 'like', $pattern)
                ->orderByDesc('invoice_number')
                ->lockForUpdate()
                ->first();

            $seq = 1;
            if ($last) {
                $parts = explode('/', $last->invoice_number);
                $seq = ((int) end($parts)) + 1;
            }

            return sprintf('%s/%s/%04d', $prefix, $year, $seq);
        });
    }

    /**
     * Items on the quotation that can still be selected for invoicing
     * (anything not already marked as invoiced).
     */
    public function eligibleItems(Quotation $quotation)
    {
        $quotation->loadMissing('items');

        return $quotation->items->filter(fn ($item) => $item->invoice_status !== 'invoiced')->values();
    }

    public function buildInvoice($selectedItems): array
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;

        foreach ($selectedItems as $item) {
            $subtotal += (float) $item->unit_price_snapshot * (int) $item->quantity;
            $totalDiscount += (float) $item->discount_snapshot;
            $totalTax += (float) $item->tax_amount;
        }

        $grandTotal = $subtotal - $totalDiscount + $totalTax;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($totalDiscount, 2),
            'tax' => round($totalTax, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    public function create(Quotation $quotation, array $itemIds, ?Admin $admin = null): Invoice
    {
        if ($quotation->status !== 'accepted') {
            throw ValidationException::withMessages([
                'quotation' => 'Only an accepted quotation can be used to generate a final invoice.',
            ]);
        }

        return DB::transaction(function () use ($quotation, $itemIds, $admin) {
            $quotation->loadMissing('items');

            $itemIds = array_values(array_filter(array_map('intval', $itemIds)));

            $selected = $quotation->items->filter(fn ($item) => in_array($item->id, $itemIds))->values();
            $eligibleIds = $quotation->items->filter(fn ($item) => $item->invoice_status !== 'invoiced')->pluck('id')->all();

            if ($selected->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Select at least one parameter to include in the invoice.',
                ]);
            }

            if ($selected->pluck('id')->diff($eligibleIds)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected parameters have already been invoiced.',
                ]);
            }

            $calc = $this->buildInvoice($selected);

            $invoice = Invoice::create([
                'invoice_number' => $this->generateNumber(),
                'quotation_id' => $quotation->id,
                'client_name' => $quotation->client_name,
                'company_name' => $quotation->company_name,
                'email' => $quotation->email,
                'phone' => $quotation->phone,
                'address' => $quotation->address,
                'address_line2' => $quotation->address_line2,
                'city' => $quotation->city,
                'state' => $quotation->state,
                'country' => $quotation->country,
                'postal_code' => $quotation->postal_code,
                'gst_number' => $quotation->gst_number,
                'pan_number' => $quotation->pan_number,
                'courier_address' => $quotation->courier_address,
                'courier_address_line2' => $quotation->courier_address_line2,
                'courier_city' => $quotation->courier_city,
                'courier_state' => $quotation->courier_state,
                'courier_postal_code' => $quotation->courier_postal_code,
                'courier_country' => $quotation->courier_country,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $calc['subtotal'],
                'discount' => $calc['discount'],
                'tax' => $calc['tax'],
                'grand_total' => $calc['grand_total'],
                'status' => 'issued',
                'notes' => $quotation->notes,
                'created_by' => $admin?->id,
            ]);

            foreach ($selected as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'quotation_item_id' => $item->id,
                    'service_id' => $item->service_id,
                    'lab_test_id' => $item->lab_test_id,
                    'service_name_snapshot' => $item->service_name_snapshot,
                    'description_snapshot' => $item->description_snapshot,
                    'unit_snapshot' => $item->unit_snapshot,
                    'quantity' => $item->quantity,
                    'unit_price_snapshot' => $item->unit_price_snapshot,
                    'discount_snapshot' => $item->discount_snapshot,
                    'tax_percentage_snapshot' => $item->tax_percentage_snapshot,
                    'tax_amount' => $item->tax_amount,
                    'total' => $item->total,
                    'notes' => $item->notes,
                ]);

                $item->markInvoiced($invoice);
            }

            // Parameters not executed/cancelled stay linked to the original
            // quotation for record purposes but are excluded from this invoice.
            $quotation->items->each(function (QuotationItem $item) use ($selected) {
                if ($selected->contains('id', $item->id)) {
                    return;
                }

                if ($item->invoice_status === 'pending') {
                    $item->update(['invoice_status' => 'excluded']);
                }
            });

            return $invoice->load('items');
        });
    }

    public function delete(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            QuotationItem::where('invoice_id', $invoice->id)->update([
                'invoice_status' => 'pending',
                'invoice_id' => null,
                'invoiced_at' => null,
            ]);

            if ($invoice->pdf_path) {
                Storage::disk('local')->delete($invoice->pdf_path);
            }

            $invoice->delete();
        });
    }
}