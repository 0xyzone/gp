<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OrderFulfillmentController extends Controller
{
    /**
     * Display the public live order fulfillment screen.
     */
    public function index(): View
    {
        $activeOrders = Order::query()
            ->whereIn('status', ['completed', 'pending'])
            ->with(['items.product'])
            ->orderBy('created_at', 'asc')
            ->get();

        $recentServedOrders = Order::query()
            ->where('status', 'served')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        $rawSettings = Setting::allKeyed();
        $stallName = $rawSettings['stall_name'] ?? 'घासा • Ghasa Products';

        return view('orders-fulfillment', [
            'activeOrders' => $activeOrders,
            'recentServedOrders' => $recentServedOrders,
            'stallName' => $stallName,
            'isStaff' => auth()->check(),
        ]);
    }

    /**
     * Mark an order as paid / settle payment (staff only).
     */
    public function markPaid(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,qr',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        $method = $validated['payment_method'];
        $received = isset($validated['cash_received']) ? (float) $validated['cash_received'] : (float) $order->grand_total;
        $change = max(0, $received - (float) $order->grand_total);

        $order->markAsPaid($method, $received, $change);

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} marked as paid ({$method}).",
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => 'paid',
                'payment_method' => $method,
            ],
        ]);
    }

    /**
     * Mark an order as served (staff only).
     */
    public function markServed(Order $order): JsonResponse
    {
        $order->markAsServed();

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} marked as served.",
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => 'served',
            ],
        ]);
    }

    /**
     * Cancel an order and automatically restock items (staff only).
     */
    public function cancel(Order $order): JsonResponse
    {
        $order->cancelAndRestock();

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} cancelled and items returned to stock.",
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => 'cancelled',
            ],
        ]);
    }

    /**
     * Fetch active orders as JSON for polling / initial load.
     */
    public function activeOrders(): JsonResponse
    {
        $orders = Order::query()
            ->whereIn('status', ['completed', 'pending'])
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
