<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id', 'service_id', 'lab_test_id', 'service_name_snapshot', 'description_snapshot',
        'unit_snapshot', 'quantity', 'unit_price_snapshot', 'discount_snapshot',
        'tax_percentage_snapshot', 'tax_amount', 'total', 'notes',
        'invoice_status', 'invoice_id', 'invoiced_at',
    ];

    protected $casts = [
        'unit_price_snapshot' => 'decimal:2',
        'discount_snapshot' => 'decimal:2',
        'tax_percentage_snapshot' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'invoiced_at' => 'datetime',
    ];

    public function quotation() { return $this->belongsTo(Quotation::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function labTest() { return $this->belongsTo(LabTest::class, 'lab_test_id'); }
    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function scopeIneligibleForInvoice($query)
    {
        return $query->where('invoice_status', 'invoiced');
    }

    public function scopeEligibleForInvoice($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('invoice_status')->orWhere('invoice_status', '!=', 'invoiced');
        });
    }

    public function markInvoiced(Invoice $invoice): self
    {
        $this->update([
            'invoice_status' => 'invoiced',
            'invoice_id' => $invoice->id,
            'invoiced_at' => now(),
        ]);

        return $this;
    }
}
