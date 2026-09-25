<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number', 'client_name', 'company_name', 'sample_name', 'sample_batch_no',
        'sample_physical_form', 'sample_storage_condition',
        'email', 'phone',
        'address', 'address_line2', 'city', 'state', 'country', 'gst_number', 'pan_number',
        'courier_address', 'courier_address_line2', 'courier_city', 'courier_state', 'courier_postal_code', 'courier_country',
        'quotation_date', 'valid_until', 'subtotal', 'discount', 'tax', 'grand_total',
        'status', 'pdf_path', 'email_status', 'email_sent_at',
        'created_by', 'project_description', 'additional_requirements',
        'notes', 'expected_timeline', 'preferred_contact_method',
        'msds_report_path', 'msds_report_name', 'other_documents'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'email_sent_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'other_documents' => 'array',
    ];

    public function items() { return $this->hasMany(QuotationItem::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function creator() { return $this->belongsTo(Admin::class, 'created_by'); }
}
