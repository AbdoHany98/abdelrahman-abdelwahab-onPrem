<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_name', 
        'price', 
        'quantity', 
        'total'
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'total' => 'float'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
