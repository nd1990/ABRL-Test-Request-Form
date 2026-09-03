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
        'tax_percentage_snapshot', 'tax_amount', 'total', 'notes'
    ];

    protected $casts = [
        'unit_price_snapshot' => 'decimal:2',
        'discount_snapshot' => 'decimal:2',
        'tax_percentage_snapshot' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quotation() { return $this->belongsTo(Quotation::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function labTest() { return $this->belongsTo(LabTest::class, 'lab_test_id'); }
}
