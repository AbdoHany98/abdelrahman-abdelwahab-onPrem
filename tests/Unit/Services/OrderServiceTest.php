<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_generates_unique_sequential_order_numbers()
    {
        // Create first order
        $order1 = $this->orderService->createOrder([
            'items' => [
                ['name' => 'Product 1', 'price' => 100, 'quantity' => 1]
            ]
        ], $this->user);

        // Create second order
        $order2 = $this->orderService->createOrder([
            'items' => [
                ['name' => 'Product 2', 'price' => 200, 'quantity' => 1]
            ]
        ], $this->user);

        // Assert order numbers are sequential
        $this->assertEquals('SO000001', $order1->order_number);
        $this->assertEquals('SO000002', $order2->order_number);
    }

    /** @test */
    public function it_calculates_order_total_correctly()
    {
        $orderData = [
            'items' => [
                ['name' => 'Product A', 'price' => 50.00, 'quantity' => 2],
                ['name' => 'Product B', 'price' => 75.50, 'quantity' => 3]
            ]
        ];

        $order = $this->orderService->createOrder($orderData, $this->user);

        // Expected total: (50 * 2) + (75.50 * 3) = 100 + 226.50 = 326.50
        $this->assertEquals(326.50, $order->total_amount);
    }

    /** @test */
    public function it_sets_pending_approval_for_orders_over_1000()
    {
        $orderData = [
            'items' => [
                ['name' => 'Expensive Product', 'price' => 1200, 'quantity' => 1]
            ]
        ];

        $order = $this->orderService->createOrder($orderData, $this->user);

        $this->assertEquals('pending_approval', $order->status);
    }

    /** @test */
    public function it_prevents_modification_of_processed_orders()
    {
        $order = $this->orderService->createOrder([
            'items' => [
                ['name' => 'Product', 'price' => 100, 'quantity' => 1]
            ]
        ], $this->user);

        // Approve the order
        $this->orderService->processApproval($order, $this->user, true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot modify an order that has been processed.');

        // Try to update an approved order
        $this->orderService->updateOrder($order, [
            'items' => [
                ['name' => 'Updated Product', 'price' => 200, 'quantity' => 1]
            ]
        ], $this->user);
    }

    /** @test */
    public function it_can_process_order_approval()
    {
        // Create an admin user
        $adminUser = User::factory()->create(['is_admin' => true]);

        $order = $this->orderService->createOrder([
            'items' => [
                ['name' => 'High Value Product', 'price' => 1200, 'quantity' => 1]
            ]
        ], $this->user);

        // Process approval
        $updatedOrder = $this->orderService->processApproval($order, $adminUser, true);

        $this->assertEquals('approved', $updatedOrder->status);
        $this->assertEquals($adminUser->id, $updatedOrder->approved_by);
    }

    /** @test */
    public function it_prevents_non_admin_approval_of_high_value_orders()
    {
        $order = $this->orderService->createOrder([
            'items' => [
                ['name' => 'High Value Product', 'price' => 1200, 'quantity' => 1]
            ]
        ], $this->user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only administrators can approve orders over $1,000.');

        // Try to approve with a non-admin user
        $this->orderService->processApproval($order, $this->user, true);
    }
}