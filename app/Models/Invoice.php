<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'quotation_id',
        'client_name', 'company_name', 'email', 'phone',
        'address', 'address_line2', 'city', 'state', 'country', 'postal_code',
        'gst_number', 'pan_number',
        'courier_address', 'courier_address_line2', 'courier_city', 'courier_state', 'courier_postal_code', 'courier_country',
        'invoice_date', 'subtotal', 'discount', 'tax', 'grand_total',
        'status', 'notes', 'pdf_path', 'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}