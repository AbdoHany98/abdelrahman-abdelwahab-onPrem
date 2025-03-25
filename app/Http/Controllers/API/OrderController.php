<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\ApproveOrderRequest;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function create(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated(),
            Auth::user() instanceof \App\Models\User ? Auth::user() : null
        );

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('items'),
            'requires_approval' => $order->status === 'pending_approval'
        ], 201);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        try {
            $updatedOrder = $this->orderService->updateOrder(
                $order,
                $request->validated(),
                Auth::user() instanceof \App\Models\User ? Auth::user() : null
            );

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $updatedOrder->load('items'),
                'requires_approval' => $updatedOrder->status === 'pending_approval'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }
    }
    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'order' => $order->load('items', 'statusHistory', 'approver')
        ]);
    }

    public function processApproval(ApproveOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        try {
            $updatedOrder = $this->orderService->processApproval(
                $order,
                Auth::user() instanceof \App\Models\User ? Auth::user() : null,
                $validated['approved'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'message' => 'Order ' . ($validated['approved'] ? 'approved' : 'rejected'),
                'order' => $updatedOrder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function orderHistory(): JsonResponse
    {
        $orders = Order::with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($orders);
    }
}
