<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\PreventOrderModification;

class OrderService
{
    use PreventOrderModification;

    public function createOrder(array $data, User $user): Order
    {
        return DB::transaction(function () use ($data, $user) {
            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'status' => 'draft',
                'total_amount' => 0
            ]);

            // Add items
            $totalAmount = 0;
            foreach ($data['items'] as $itemData) {
                $itemTotal = $itemData['price'] * $itemData['quantity'];
                $order->items()->create([
                    'product_name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'total' => $itemTotal
                ]);
                $totalAmount += $itemTotal;
            }

            // Update order total
            $order->update([
                'total_amount' => $totalAmount,
                'status' => $totalAmount > 1000 ? 'pending_approval' : 'approved'
            ]);

            // Create initial status history
            $this->createStatusHistory($order, $order->status, $user);

            return $order;
        });
    }

    public function updateOrder(Order $order, array $data, User $user): Order
    {
        // Prevent modifications to processed orders
        $this->guardOrderModification($order);

        return DB::transaction(function () use ($order, $data, $user) {
            // Clear existing items
            $order->items()->delete();

            // Add new items
            $totalAmount = 0;
            foreach ($data['items'] as $itemData) {
                $itemTotal = $itemData['price'] * $itemData['quantity'];
                $order->items()->create([
                    'product_name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'total' => $itemTotal
                ]);
                $totalAmount += $itemTotal;
            }

            // Update order
            $order->update([
                'total_amount' => $totalAmount,
                'status' => $totalAmount > 1000 ? 'pending_approval' : 'draft'
            ]);

            // Create status history entry
            $this->createStatusHistory($order, $order->status, $user);

            return $order;
        });
    }

    public function processApproval(Order $order, User $approver, bool $approved, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $approver, $approved, $notes) {
            // Only admins can approve orders over $1000
            if ($order->total_amount > 1000 && !$approver->is_admin) {
                throw new \Exception("Only administrators can approve orders over $1,000.");
            }

            $newStatus = $approved ? 'approved' : 'rejected';

            $order->update([
                'status' => $newStatus,
                'approved_by' => $approved ? $approver->id : null
            ]);

            $this->createStatusHistory($order, $newStatus, $approver, $notes);

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        // Implement a thread-safe, sequential order number generation
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $sequence = $lastOrder ? ((int) substr($lastOrder->order_number, 2)) + 1 : 1;

        return 'SO' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    private function createStatusHistory(Order $order, string $status, ?User $user = null, ?string $notes = null): void
    {
        $order->statusHistory()->create([
            'status' => $status,
            'user_id' => $user ? $user->id : null,
            'notes' => $notes
        ]);
    }
}