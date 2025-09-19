<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            $orders = Order::with(['user', 'items.product', 'payment'])->paginate(15);
        } else {
            $orders = $user->orders()->with(['items.product', 'payment'])->paginate(15);
        }

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:card,cod',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $totalAmount = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Check if product is active and shop is approved
                if (!$product->is_active || !$product->shop->isApproved()) {
                    throw new \Exception("Product is not available: {$product->name}");
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal,
                ];

                // Reserve stock
                $product->decreaseStock($item['quantity']);
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($orderItems as $orderItem) {
                $order->items()->create($orderItem);
            }

            // Handle payment method
            if ($request->payment_method === 'cod') {
                $order->update(['status' => Order::STATUS_CONFIRMED]);
            }

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items.product', 'user']),
            ], 201);
        });
    }

    public function show(Order $order): JsonResponse
    {
        $user = auth()->user();

        // Check if user can view this order
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order->load(['items.product.shop', 'payment', 'user']));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Only admin can update orders
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $order->update($request->only('status'));

        if ($request->status === 'shipped') {
            $order->update(['shipped_at' => now()]);
        }

        if ($request->status === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->fresh(['items.product', 'payment']),
        ]);
    }

    public function processPayment(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Check if user owns this order
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if order can be paid
        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Order already paid'], 400);
        }

        if ($order->payment_method === 'cod') {
            return response()->json(['message' => 'Cash on delivery orders cannot be paid online'], 400);
        }

        $request->validate([
            'card_number' => 'required|string',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'cvv' => 'required|string|min:3|max:4',
            'cardholder_name' => 'required|string',
        ]);

        // Simulate payment processing
        $paymentSuccess = $this->simulatePaymentProcessing($request->all());

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'payment_method' => 'card',
            'status' => $paymentSuccess ? Payment::STATUS_COMPLETED : Payment::STATUS_FAILED,
            'transaction_id' => 'TXN_' . time() . '_' . rand(1000, 9999),
            'gateway_response' => $paymentSuccess ? 
                ['status' => 'success', 'message' => 'Payment processed successfully'] :
                ['status' => 'failed', 'message' => 'Payment failed'],
            'processed_at' => now(),
        ]);

        if ($paymentSuccess) {
            $order->update([
                'payment_status' => 'paid',
                'status' => Order::STATUS_CONFIRMED,
            ]);

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $payment,
                'order' => $order->fresh(),
            ]);
        } else {
            return response()->json([
                'message' => 'Payment failed',
                'payment' => $payment,
            ], 400);
        }
    }

    private function simulatePaymentProcessing(array $paymentData): bool
    {
        // Simulate payment processing with 90% success rate
        return rand(1, 10) <= 9;
    }
}