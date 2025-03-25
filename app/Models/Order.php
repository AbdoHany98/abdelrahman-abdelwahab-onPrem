<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number', 
        'status', 
        'total_amount', 
        'requires_approval',
        'approved_by'
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'total_amount' => 'float'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function calculateTotal(): float
    {
        return $this->items()->sum('total');
    }

    public function requiresApproval(): bool
    {
        return $this->total_amount > 1000;
    }
}
