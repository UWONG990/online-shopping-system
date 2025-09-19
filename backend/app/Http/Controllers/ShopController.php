<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            $shops = Shop::with(['owner', 'approver'])->paginate(15);
        } elseif ($user->isShopOwner()) {
            $shops = $user->shops()->with(['owner', 'approver'])->paginate(15);
        } else {
            // Clients can only see approved shops
            $shops = Shop::where('status', Shop::STATUS_APPROVED)
                ->with(['owner'])
                ->paginate(15);
        }

        return response()->json($shops);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'business_license' => 'nullable|string',
        ]);

        $user = $request->user();

        // Update user role to shop_owner if they're creating their first shop
        if ($user->role === 'client') {
            $user->update(['role' => 'shop_owner']);
        }

        $shop = Shop::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $user->id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'business_license' => $request->business_license,
            'status' => Shop::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Shop created successfully. Awaiting admin approval.',
            'shop' => $shop->load('owner'),
        ], 201);
    }

    public function show(Shop $shop): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can view this shop
        if (!$shop->isApproved() && !$user->isAdmin() && $shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        return response()->json($shop->load(['owner', 'approver', 'products']));
    }

    public function update(Request $request, Shop $shop): JsonResponse
    {
        $user = $request->user();

        // Only shop owner or admin can update
        if (!$user->isAdmin() && $shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'business_license' => 'nullable|string',
        ]);

        $shop->update($request->only([
            'name', 'description', 'address', 'phone', 'email', 'business_license'
        ]));

        return response()->json([
            'message' => 'Shop updated successfully',
            'shop' => $shop->fresh(['owner', 'approver']),
        ]);
    }

    public function destroy(Shop $shop): JsonResponse
    {
        $user = Auth::user();

        // Only shop owner or admin can delete
        if (!$user->isAdmin() && $shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $shop->delete();

        return response()->json(['message' => 'Shop deleted successfully']);
    }

    public function requestApproval(Shop $shop): JsonResponse
    {
        $user = Auth::user();

        if ($shop->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($shop->status !== Shop::STATUS_REJECTED) {
            return response()->json([
                'message' => 'Shop approval can only be requested for rejected shops'
            ], 400);
        }

        $shop->update([
            'status' => Shop::STATUS_PENDING,
            'rejection_reason' => null,
        ]);

        return response()->json([
            'message' => 'Approval request submitted successfully',
            'shop' => $shop->fresh(),
        ]);
    }
}