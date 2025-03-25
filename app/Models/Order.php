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
        'approved_by'
    ];
    protected $appends = ['requires_approval'];

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

    public function getRequiresApprovalAttribute()
    {
        return $this->status === 'pending_approval';
    }
}
