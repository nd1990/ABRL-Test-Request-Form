<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'description', 'price', 'tax_percentage', 'unit', 'icon', 'is_active', 'sort_order'];
    protected $casts = ['price' => 'decimal:2', 'tax_percentage' => 'decimal:2', 'is_active' => 'boolean'];

    public function quotationItems() { return $this->hasMany(QuotationItem::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}
