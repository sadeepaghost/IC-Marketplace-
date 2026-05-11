<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourcingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number', 'part_number', 'manufacturer', 'description',
        'quantity_required', 'target_price_usd', 'required_by_date',
        'customer_name', 'customer_email', 'customer_phone',
        'company_name', 'country', 'customer_notes',
        'suggested_alternatives', 'ai_confidence_score', 'ai_reasoning',
        'status', 'admin_notes', 'assigned_to',
        'admin_notified', 'admin_notified_at',
        'customer_quote_sent', 'customer_quote_sent_at',
        'ip_address', 'user_agent', 'session_id', 'user_id',
    ];

    protected $casts = [
        'suggested_alternatives'    => 'array',
        'admin_notified'            => 'boolean',
        'customer_quote_sent'       => 'boolean',
        'admin_notified_at'         => 'datetime',
        'customer_quote_sent_at'    => 'datetime',
        'required_by_date'          => 'date',
    ];

    protected $hidden = ['user_agent', 'session_id', 'ip_address'];

    // ── Relationships ──────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['fulfilled', 'rejected', 'expired']);
    }
}
