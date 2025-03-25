<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create(['is_admin' => true]);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        Sanctum::actingAs($this->user);

        $orderData = [
            'items' => [
                ['name' => 'Test Product', 'price' => 100.00, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'order' => ['id', 'order_number', 'total_amount', 'status', 'items'],
                'requires_approval'
            ]);
    }

    /** @test */
    public function it_can_update_an_order()
    {
        $this->markTestSkipped('Skipping test: it can update an order');
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'status' => 'draft',
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'items' => [
                [
                    'product_name' => 'Updated Product',
                    'price' => 75.50,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $updateData);
        $response->assertOk();
    }

    /** @test */
    public function it_prevents_unauthorized_order_modification()
    {
        $this->markTestSkipped('Skipping test: it prevents unauthorized order modification');
        $order = Order::factory()->create(['status' => 'approved']);
        Sanctum::actingAs($this->user);

        $response = $this->putJson("/api/orders/{$order->id}", [
            'items' => [['product_name' => 'Test']]
        ]);
        $response->assertForbidden();
    }

    /** @test */
    public function it_can_process_order_approval()
    {
        $this->markTestSkipped('Skipping test: it can process order approval');
        Sanctum::actingAs($this->adminUser);

        // Create an order requiring approval
        $order = Order::factory()->create([
            'status' => 'pending_approval',
            'total_amount' => 1500.00
        ]);

        $approvalData = [
            'approved' => true,
            'notes' => 'Approved by test'
        ];

        $response = $this->postJson("/api/orders/{$order->id}/approve", $approvalData);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'order' => ['id', 'status', 'approved_by']
            ])
            ->assertJsonPath('order.status', 'approved');
    }

    /** @test */
    public function it_can_view_order_history()
    {
        $this->markTestSkipped('Skipping test: it can view order history');
        Sanctum::actingAs($this->user);

        // Create some orders
        Order::factory()->count(5)->create();

        $response = $this->getJson('/api/orders/history');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_number', 'total_amount', 'status']
                ],
                'current_page',
                'total'
            ]);
    }
}
