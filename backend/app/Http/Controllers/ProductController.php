<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['shop', 'category']);

        // Filter by shop if specified
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // Filter by category if specified
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by active status for non-owners
        $user = $request->user();
        if (!$user || (!$user->isAdmin() && !$this->isShopOwner($user, $request->shop_id))) {
            $query->where('is_active', true)
                  ->whereHas('shop', function($q) {
                      $q->where('status', Shop::STATUS_APPROVED);
                  });
        }

        $products = $query->paginate(15);

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'shop_id' => 'required|exists:shops,id',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
        ]);

        $user = $request->user();
        $shop = Shop::findOrFail($request->shop_id);

        // Check if user owns the shop or is admin
        if (!$user->isAdmin() && $shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if shop is approved
        if (!$shop->isApproved()) {
            return response()->json([
                'message' => 'Cannot add products to non-approved shop'
            ], 400);
        }

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load(['shop', 'category']),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $user = Auth::user();

        // Check if user can view this product
        if (!$product->is_active && $user && !$user->isAdmin() && $product->shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product->load(['shop', 'category']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        // Check if user owns the shop or is admin
        if (!$user->isAdmin() && $product->shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->fresh(['shop', 'category']),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $user = Auth::user();

        // Check if user owns the shop or is admin
        if (!$user->isAdmin() && $product->shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    private function isShopOwner($user, $shopId): bool
    {
        if (!$shopId) {
            return false;
        }

        return $user->shops()->where('id', $shopId)->exists();
    }
}