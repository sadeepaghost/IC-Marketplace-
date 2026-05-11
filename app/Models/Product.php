<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_number', 'manufacturer', 'description', 'category', 'series',
        'stock_status', 'quantity_available', 'minimum_order_quantity',
        'unit_price_usd', 'bulk_price_usd', 'bulk_qty_threshold',
        'package_type', 'supply_voltage_min', 'supply_voltage_max',
        'operating_temp_min', 'operating_temp_max', 'frequency',
        'output_current', 'input_offset_voltage', 'bandwidth', 'pin_count',
        'interface', 'flash_memory', 'ram', 'additional_specs',
        'rohs_compliant', 'reach_compliant', 'datasheet_url', 'image_url',
        'lead_time', 'origin_country', 'eccn', 'search_tags',
    ];

    protected $casts = [
        'additional_specs'  => 'array',
        'rohs_compliant'    => 'boolean',
        'reach_compliant'   => 'boolean',
        'unit_price_usd'    => 'decimal:4',
        'bulk_price_usd'    => 'decimal:4',
        'quantity_available'=> 'integer',
        'pin_count'         => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────
    public function sourcingRequests()
    {
        return $this->hasMany(SourcingRequest::class, 'part_number', 'part_number');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('stock_status', ['in_stock', 'low_stock']);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('part_number',  'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('manufacturer','LIKE', "%{$term}%");
        });
    }

    // ── Accessors ──────────────────────────────────────────────────────────
    public function getIsInStockAttribute(): bool
    {
        return $this->stock_status === 'in_stock';
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->unit_price_usd
            ? '$' . number_format($this->unit_price_usd, 4)
            : 'Price on request';
    }
}
