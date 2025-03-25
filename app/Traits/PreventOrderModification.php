<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait PreventOrderModification
{
    /**
     * Prevent modifications to approved or processed orders
     *
     * @param \App\Models\Order $order
     * @throws \Exception
     */
    protected function guardOrderModification($order)
    {
        // Prevent modifications to approved or processed orders
        if (in_array($order->status, ['approved', 'rejected', 'completed'])) {
            throw new \Exception('Cannot modify an order that has been processed.');
        }
    }
}