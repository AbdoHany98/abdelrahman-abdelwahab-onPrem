<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $totalAmount = $this->faker->randomFloat(2, 50, 2000);
        
        return [
            'order_number' => 'SO' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT),
            'status' => $totalAmount > 1000 ? 'pending_approval' : 'draft',
            'total_amount' => $totalAmount
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $order->items()->createMany(
                collect(range(1, rand(1, 3)))->map(function () {
                    return [
                        'product_name' => $this->faker->word(),
                        'price' => $this->faker->randomFloat(2, 10, 500),
                        'quantity' => $this->faker->numberBetween(1, 5),
                        // Remove 'total' as it should be calculated
                    ];
                })->toArray()
            );
            
            // Recalculate total after items are created
            $order->update([
                'total' => $order->items->sum(fn($item) => $item->price * $item->quantity)
            ]);
        });
    }
}