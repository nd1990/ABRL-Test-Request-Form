<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        's_no', 'nabl_type', 'discipline', 'material', 'parameter', 'method',
        'sample_quantity', 'lead_time', 'charges_per_sample', 'gst_amount', 'total_amount',
        'protocol_no',
        'nabl_range', 'limit_of_quantification', 'remarks', 'protocol_link', 'is_active',
    ];

    protected $casts = [
        's_no' => 'integer',
        'charges_per_sample' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query) { return $query->where('is_active', true); }
}
