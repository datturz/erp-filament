<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    // Get transfers for current user's store
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;
        
        if (!$store) {
            return response()->json(['error' => 'No store assigned'], 400);
        }
        
        $transfers = Transfer::with(['product:id,name,sku', 'fromStore:id,name', 'toStore:id,name'])
            ->forStore($store->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        return response()->json($transfers);
    }
    
    // Create new transfer request
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'to_store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity_requested' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);
        
        $user = $request->user();
        $fromStore = $user->store;
        
        if (!$fromStore || !$user->hasRole(['store_manager', 'warehouse_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $product = Product::findOrFail($request->product_id);
        
        // Check if sufficient stock available
        if ($product->current_stock < $request->quantity_requested) {
            return response()->json([
                'error' => 'Insufficient stock',
                'available' => $product->current_stock,
                'requested' => $request->quantity_requested
            ], 400);
        }
        
        $transfer = Transfer::create([
            'from_store_id' => $fromStore->id,
            'to_store_id' => $request->to_store_id,
            'product_id' => $request->product_id,
            'quantity_requested' => $request->quantity_requested,
            'reason' => $request->reason,
            'requested_by' => $user->id,
        ]);
        
        return response()->json([
            'success' => true,
            'transfer' => $transfer->load(['product', 'fromStore', 'toStore'])
        ], 201);
    }
    
    // Approve transfer (warehouse manager only)
    public function approve(Request $request, Transfer $transfer): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($transfer->status !== 'requested') {
            return response()->json(['error' => 'Transfer cannot be approved'], 400);
        }
        
        $transfer->approve($user);
        
        return response()->json([
            'success' => true,
            'transfer' => $transfer->fresh()
        ]);
    }
    
    // Ship transfer
    public function ship(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'quantity_shipped' => 'required|integer|min:1',
        ]);
        
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'warehouse_worker'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($transfer->status !== 'approved') {
            return response()->json(['error' => 'Transfer not approved'], 400);
        }
        
        $quantityShipped = $request->quantity_shipped;
        
        if ($quantityShipped > $transfer->quantity_requested) {
            return response()->json(['error' => 'Cannot ship more than requested'], 400);
        }
        
        // Check current stock
        if ($transfer->product->current_stock < $quantityShipped) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }
        
        $transfer->ship($user, $quantityShipped);
        
        return response()->json([
            'success' => true,
            'transfer' => $transfer->fresh()
        ]);
    }
    
    // Receive transfer
    public function receive(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'quantity_received' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $user = $request->user();
        $userStore = $user->store;
        
        if (!$userStore || $userStore->id !== $transfer->to_store_id) {
            return response()->json(['error' => 'Can only receive at destination store'], 403);
        }
        
        if ($transfer->status !== 'shipped') {
            return response()->json(['error' => 'Transfer not shipped'], 400);
        }
        
        $quantityReceived = $request->quantity_received;
        
        if ($quantityReceived > $transfer->quantity_shipped) {
            return response()->json(['error' => 'Cannot receive more than shipped'], 400);
        }
        
        $transfer->receive($user, $quantityReceived);
        
        if ($request->notes) {
            $transfer->update(['notes' => $request->notes]);
        }
        
        return response()->json([
            'success' => true,
            'transfer' => $transfer->fresh(),
            'variance' => $transfer->variance
        ]);
    }
    
    // Cancel transfer
    public function cancel(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string',
        ]);
        
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'store_manager', 'admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if (in_array($transfer->status, ['received', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel completed or cancelled transfer'], 400);
        }
        
        $transfer->cancel($request->reason);
        
        return response()->json([
            'success' => true,
            'transfer' => $transfer->fresh()
        ]);
    }
    
    // Get pending approvals (for managers)
    public function pendingApprovals(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $transfers = Transfer::with(['product:id,name', 'fromStore:id,name', 'toStore:id,name', 'requestedBy:id,name'])
            ->where('status', 'requested')
            ->orderBy('requested_date')
            ->get();
            
        return response()->json($transfers);
    }
    
    // Get transfer analytics
    public function analytics(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;
        
        if (!$store) {
            return response()->json(['error' => 'No store assigned'], 400);
        }
        
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $query = Transfer::forStore($store->id)
            ->whereBetween('requested_date', [$dateFrom, $dateTo]);
            
        $analytics = [
            'total_transfers' => $query->count(),
            'completed_transfers' => $query->where('status', 'received')->count(),
            'pending_transfers' => $query->where('status', 'requested')->count(),
            'in_transit' => $query->where('status', 'shipped')->count(),
            'cancelled_transfers' => $query->where('status', 'cancelled')->count(),
            'average_transit_days' => Transfer::forStore($store->id)
                ->where('status', 'received')
                ->whereBetween('requested_date', [$dateFrom, $dateTo])
                ->get()
                ->avg('days_in_transit'),
        ];
        
        return response()->json($analytics);
    }
}