<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        $shops = Shop::where('status', Shop::STATUS_PENDING)
            ->with(['owner'])
            ->paginate(15);

        return response()->json($shops);
    }

    public function approve(Request $request, Shop $shop): JsonResponse
    {
        if ($shop->status !== Shop::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending shops can be approved'
            ], 400);
        }

        $shop->update([
            'status' => Shop::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json([
            'message' => 'Shop approved successfully',
            'shop' => $shop->fresh(['owner', 'approver']),
        ]);
    }

    public function reject(Request $request, Shop $shop): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($shop->status !== Shop::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending shops can be rejected'
            ], 400);
        }

        $shop->update([
            'status' => Shop::STATUS_REJECTED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'message' => 'Shop rejected successfully',
            'shop' => $shop->fresh(['owner', 'approver']),
        ]);
    }
}