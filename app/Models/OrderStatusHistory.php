<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_history';

    protected $fillable = [
        'order_id', 
        'status', 
        'user_id', 
        'notes'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
