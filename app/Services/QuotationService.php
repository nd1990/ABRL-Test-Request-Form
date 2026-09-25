<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\LabTest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuotationService
{
    public function __construct(protected SettingsService $settings) {}

    public function generateNumber(): string
    {
        return DB::transaction(function () {
            $prefix = Setting::get('quotation', 'prefix', 'ABRL/QTN') ?: 'ABRL/QTN';
            $year = now()->format('Y');
            $pattern = $prefix . '/' . $year . '/%';

            $last = Quotation::where('quotation_number', 'like', $pattern)
                ->orderByDesc('quotation_number')
                ->lockForUpdate()
                ->first();

            $seq = 83;
            if ($last) {
                $parts = explode('/', $last->quotation_number);
                $seq = ((int) end($parts)) + 1;
            }

            return sprintf('%s/%s/%04d', $prefix, $year, $seq);
        });
    }

    public function buildQuotation(array $data): array
    {
        $taxPercent = (float) ($this->settings->quotation()['tax_percentage'] ?? 0);
        $items = [];

        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;

        foreach (($data['services'] ?? []) as $item) {
            $service = Service::find($item['service_id']);

            if (!$service || !$service->is_active) {
                throw ValidationException::withMessages([
                    'services' => 'Service "' . ($item['service_id'] ?? 'unknown') . '" is invalid or inactive.',
                ]);
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = (float) $service->price;
            $lineDiscount = max(0, (float) ($item['discount'] ?? 0));

            if ($lineDiscount > $quantity * $unitPrice) {
                $lineDiscount = $quantity * $unitPrice;
            }

            $taxableValue = max(0, ($quantity * $unitPrice) - $lineDiscount);
            $tax = $taxableValue * $taxPercent / 100;
            $lineTotal = $taxableValue + $tax;

            $lineSubtotal = $quantity * $unitPrice;

            $subtotal += $lineSubtotal;
            $totalDiscount += $lineDiscount;
            $totalTax += $tax;

            $items[] = [
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $lineDiscount,
                'tax_percentage' => $taxPercent,
                'tax_amount' => round($tax, 2),
                'total' => round($lineTotal, 2),
                'notes' => $item['notes'] ?? '',
                '_snapshot_name' => $service->name,
                '_snapshot_description' => $service->description,
                '_snapshot_unit' => $service->unit,
            ];
        }

        if (!empty($data['lab_tests'])) {
            foreach ($data['lab_tests'] as $lt) {
                $test = LabTest::find($lt['lab_test_id'] ?? null);

                if (!$test || !$test->is_active) {
                    throw ValidationException::withMessages([
                        'lab_tests' => 'Selected test is invalid or unavailable.',
                    ]);
                }

                $quantity = max(1, (int) ($lt['no_of_samples'] ?? 1));

                $description = collect([
                    $test->discipline,
                    $test->material,
                    $test->method ? 'Method: ' . $test->method : null,
                    $test->protocol_no ? 'Protocol: ' . $test->protocol_no : null,
                    $test->sample_quantity ? 'Sample qty: ' . $test->sample_quantity : null,
                ])->filter()->implode("\n");

                $unitPrice = (float) $test->charges_per_sample;
                $taxPerUnit = (float) $test->gst_amount;
                $totalPerUnit = (float) $test->total_amount;
                $itemTaxPercent = $unitPrice > 0 ? round(($taxPerUnit / $unitPrice) * 100, 2) : 0;

                $subtotal += $unitPrice * $quantity;
                $totalTax += $taxPerUnit * $quantity;

                $items[] = [
                    'service_id' => null,
                    'lab_test_id' => $test->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => 0,
                    'tax_percentage' => $itemTaxPercent,
                    'tax_amount' => round($taxPerUnit * $quantity, 2),
                    'total' => round($totalPerUnit * $quantity, 2),
                    'notes' => $lt['notes'] ?? '',
                    '_snapshot_name' => $test->parameter,
                    '_snapshot_description' => $description,
                    '_snapshot_unit' => 'sample',
                ];
            }
        }

        $grandTotal = $subtotal - $totalDiscount + $totalTax;

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'discount' => round($totalDiscount, 2),
            'tax_percentage' => $taxPercent,
            'tax_amount' => round($totalTax, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    public function create(array $data, ?Admin $admin = null): Quotation
    {
        return DB::transaction(function () use ($data, $admin) {
            $quotationNumber = $this->generateNumber();
            $calc = $this->buildQuotation($data);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'client_name' => $data['client_name'],
                'company_name' => $data['company_name'] ?? null,
                'sample_name' => $data['sample_name'] ?? null,
                'sample_batch_no' => $data['sample_batch_no'] ?? null,
                'sample_physical_form' => $data['sample_physical_form'] ?? null,
                'sample_storage_condition' => $data['sample_storage_condition'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'gst_number' => $data['gst_number'] ?? null,
                'pan_number' => $data['pan_number'] ?? null,
                'courier_address' => $data['different_courier_address'] ?? false ? ($data['courier_address'] ?? null) : null,
                'courier_address_line2' => $data['different_courier_address'] ?? false ? ($data['courier_address_line2'] ?? null) : null,
                'courier_city' => $data['different_courier_address'] ?? false ? ($data['courier_city'] ?? null) : null,
                'courier_state' => $data['different_courier_address'] ?? false ? ($data['courier_state'] ?? null) : null,
                'courier_postal_code' => $data['different_courier_address'] ?? false ? ($data['courier_postal_code'] ?? null) : null,
                'courier_country' => $data['different_courier_address'] ?? false ? ($data['courier_country'] ?? null) : null,
                'quotation_date' => now()->toDateString(),
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => $calc['subtotal'],
                'discount' => $calc['discount'],
                'tax' => $calc['tax_amount'],
                'grand_total' => $calc['grand_total'],
                'status' => 'sent',
                'email_status' => 'pending',
                'project_description' => $data['project_description'] ?? null,
                'additional_requirements' => $data['additional_requirements'] ?? null,
                'notes' => $data['notes'] ?? null,
                'expected_timeline' => $data['expected_timeline'] ?? null,
                'preferred_contact_method' => $data['preferred_contact_method'] ?? null,
                'created_by' => $admin?->id,
            ]);

            foreach ($calc['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'service_id' => $item['service_id'],
                    'lab_test_id' => $item['lab_test_id'] ?? null,
                    'service_name_snapshot' => $item['_snapshot_name'],
                    'description_snapshot' => $item['_snapshot_description'],
                    'unit_snapshot' => $item['_snapshot_unit'],
                    'quantity' => $item['quantity'],
                    'unit_price_snapshot' => $item['unit_price'],
                    'discount_snapshot' => $item['discount'],
                    'tax_percentage_snapshot' => $item['tax_percentage'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'notes' => $item['notes'],
                ]);
            }

            return $quotation->load('items');
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            $calc = $this->buildQuotation($data);

            $invoicingState = $quotation->items->mapWithKeys(function ($item) {
                $key = $item->lab_test_id ?: 's' . $item->service_id;

                return [$key => [
                    'invoice_status' => $item->invoice_status,
                    'invoice_id' => $item->invoice_id,
                    'invoiced_at' => $item->invoiced_at,
                ]];
            })->all();

            $quotation->update([
                'client_name' => $data['client_name'] ?? $quotation->client_name,
                'company_name' => $data['company_name'] ?? $quotation->company_name,
                'sample_name' => $data['sample_name'] ?? $quotation->sample_name,
                'sample_batch_no' => $data['sample_batch_no'] ?? $quotation->sample_batch_no,
                'sample_physical_form' => $data['sample_physical_form'] ?? $quotation->sample_physical_form,
                'sample_storage_condition' => $data['sample_storage_condition'] ?? $quotation->sample_storage_condition,
                'email' => $data['email'] ?? $quotation->email,
                'phone' => $data['phone'] ?? $quotation->phone,
                'address' => $data['address'] ?? $quotation->address,
                'address_line2' => $data['address_line2'] ?? $quotation->address_line2,
                'city' => $data['city'] ?? $quotation->city,
                'state' => $data['state'] ?? $quotation->state,
                'country' => $data['country'] ?? $quotation->country,
                'postal_code' => $data['postal_code'] ?? $quotation->postal_code,
                'gst_number' => $data['gst_number'] ?? $quotation->gst_number,
                'pan_number' => $data['pan_number'] ?? $quotation->pan_number,
                'courier_address' => ($data['different_courier_address'] ?? false) ? ($data['courier_address'] ?? $quotation->courier_address) : null,
                'courier_address_line2' => ($data['different_courier_address'] ?? false) ? ($data['courier_address_line2'] ?? $quotation->courier_address_line2) : null,
                'courier_city' => ($data['different_courier_address'] ?? false) ? ($data['courier_city'] ?? $quotation->courier_city) : null,
                'courier_state' => ($data['different_courier_address'] ?? false) ? ($data['courier_state'] ?? $quotation->courier_state) : null,
                'courier_postal_code' => ($data['different_courier_address'] ?? false) ? ($data['courier_postal_code'] ?? $quotation->courier_postal_code) : null,
                'courier_country' => ($data['different_courier_address'] ?? false) ? ($data['courier_country'] ?? $quotation->courier_country) : null,
                'quotation_date' => $data['quotation_date'] ?? $quotation->quotation_date,
                'valid_until' => $data['valid_until'] ?? $quotation->valid_until,
                'subtotal' => $calc['subtotal'],
                'discount' => $calc['discount'],
                'tax' => $calc['tax_amount'],
                'grand_total' => $calc['grand_total'],
                'project_description' => $data['project_description'] ?? $quotation->project_description,
                'additional_requirements' => $data['additional_requirements'] ?? $quotation->additional_requirements,
                'notes' => $data['notes'] ?? $quotation->notes,
                'expected_timeline' => $data['expected_timeline'] ?? $quotation->expected_timeline,
                'preferred_contact_method' => $data['preferred_contact_method'] ?? $quotation->preferred_contact_method,
            ]);

            $quotation->items()->delete();

            $created = [];
            foreach ($calc['items'] as $item) {
                $created[] = QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'service_id' => $item['service_id'],
                    'lab_test_id' => $item['lab_test_id'] ?? null,
                    'service_name_snapshot' => $item['_snapshot_name'],
                    'description_snapshot' => $item['_snapshot_description'],
                    'unit_snapshot' => $item['_snapshot_unit'],
                    'quantity' => $item['quantity'],
                    'unit_price_snapshot' => $item['unit_price'],
                    'discount_snapshot' => $item['discount'],
                    'tax_percentage_snapshot' => $item['tax_percentage'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'notes' => $item['notes'],
                ]);
            }

            foreach ($created as $item) {
                $key = $item->lab_test_id ?: 's' . $item->service_id;

                if (isset($invoicingState[$key]) && $invoicingState[$key]['invoice_status'] !== 'pending') {
                    $item->forceFill([
                        'invoice_status' => $invoicingState[$key]['invoice_status'],
                        'invoice_id' => $invoicingState[$key]['invoice_id'],
                        'invoiced_at' => $invoicingState[$key]['invoiced_at'],
                    ])->save();
                }
            }

            return $quotation->load('items');
        });
    }

    public function duplicate(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation) {
            $newNumber = $this->generateNumber();

            $newQuotation = Quotation::create([
                'quotation_number' => $newNumber,
                'client_name' => $quotation->client_name,
                'company_name' => $quotation->company_name,
                'sample_name' => $quotation->sample_name,
                'sample_batch_no' => $quotation->sample_batch_no,
                'sample_physical_form' => $quotation->sample_physical_form,
                'sample_storage_condition' => $quotation->sample_storage_condition,
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
                'quotation_date' => now()->toDateString(),
                'valid_until' => $quotation->valid_until,
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'tax' => $quotation->tax,
                'grand_total' => $quotation->grand_total,
                'status' => 'sent',
                'email_status' => 'pending',
                'project_description' => $quotation->project_description,
                'additional_requirements' => $quotation->additional_requirements,
                'notes' => $quotation->notes,
                'expected_timeline' => $quotation->expected_timeline,
                'preferred_contact_method' => $quotation->preferred_contact_method,
                'created_by' => $quotation->created_by,
            ]);

            foreach ($quotation->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $newQuotation->id,
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
            }

            return $newQuotation->load('items');
        });
    }

    public function calculateItemSubtotal(float $quantity, float $unitPrice, float $discount, float $taxPercent): array
    {
        $lineSubtotal = $quantity * $unitPrice;
        $taxableValue = max(0, $lineSubtotal - $discount);
        $tax = $taxableValue * $taxPercent / 100;
        $total = $taxableValue + $tax;

        return [
            'subtotal' => round($lineSubtotal, 2),
            'taxable_value' => round($taxableValue, 2),
            'tax_amount' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }

    public function formatMoney($amount, ?SettingsService $settings = null): string
    {
        $settings ??= $this->settings;
        $symbol = $settings->currencySymbol();

        return $symbol . number_format((float) $amount, 2);
    }
}