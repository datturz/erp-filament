<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CycleCount;
use App\Models\CycleCountItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CycleCountController extends Controller
{
    // Get cycle counts for current user's store
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;
        
        if (!$store) {
            return response()->json(['error' => 'No store assigned'], 400);
        }
        
        $cycleCounts = CycleCount::with(['assignedTo:id,name', 'items'])
            ->forStore($store->id)
            ->orderBy('scheduled_date', 'desc')
            ->get();
            
        return response()->json($cycleCounts);
    }
    
    // Create new cycle count
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:full,partial,abc,random',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'location_id' => 'nullable|exists:inventory_locations,id',
            'notes' => 'nullable|string',
        ]);
        
        $user = $request->user();
        $store = $user->store;
        
        if (!$store || !$user->hasRole(['warehouse_manager', 'store_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $cycleCount = CycleCount::create([
            'store_id' => $store->id,
            'type' => $request->type,
            'scheduled_date' => $request->scheduled_date,
            'location_id' => $request->location_id,
            'notes' => $request->notes,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ]);
        
        // Generate count items
        $cycleCount->generateItems();
        
        return response()->json([
            'success' => true,
            'cycle_count' => $cycleCount->load(['items', 'assignedTo'])
        ], 201);
    }
    
    // Start cycle count
    public function start(Request $request, CycleCount $cycleCount): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'warehouse_worker', 'store_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($cycleCount->status !== 'planned') {
            return response()->json(['error' => 'Cycle count already started'], 400);
        }
        
        $cycleCount->start();
        
        return response()->json([
            'success' => true,
            'cycle_count' => $cycleCount->fresh()
        ]);
    }
    
    // Get cycle count items for counting
    public function items(Request $request, CycleCount $cycleCount): JsonResponse
    {
        $items = $cycleCount->items()
            ->with(['item', 'location'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'item_sku' => $item->item_sku,
                    'location' => $item->location->name,
                    'expected_quantity' => $item->expected_quantity,
                    'counted_quantity' => $item->counted_quantity,
                    'variance' => $item->variance,
                    'status' => $item->status,
                    'notes' => $item->notes,
                ];
            });
            
        return response()->json($items);
    }
    
    // Record count for item
    public function recordCount(Request $request, CycleCountItem $item): JsonResponse
    {
        $request->validate([
            'counted_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'warehouse_worker', 'store_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $item->recordCount(
            $request->counted_quantity,
            $user,
            $request->notes
        );
        
        return response()->json([
            'success' => true,
            'item' => $item->fresh(),
            'has_variance' => $item->hasVariance()
        ]);
    }
    
    // Bulk record counts
    public function bulkRecord(Request $request, CycleCount $cycleCount): JsonResponse
    {
        $request->validate([
            'counts' => 'required|array',
            'counts.*.item_id' => 'required|exists:cycle_count_items,id',
            'counts.*.counted_quantity' => 'required|numeric|min:0',
            'counts.*.notes' => 'nullable|string',
        ]);
        
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'warehouse_worker', 'store_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $processed = 0;
        $errors = [];
        
        foreach ($request->counts as $countData) {
            try {
                $item = CycleCountItem::findOrFail($countData['item_id']);
                
                if ($item->cycle_count_id !== $cycleCount->id) {
                    $errors[] = "Item {$countData['item_id']} does not belong to this cycle count";
                    continue;
                }
                
                $item->recordCount(
                    $countData['counted_quantity'],
                    $user,
                    $countData['notes'] ?? null
                );
                
                $processed++;
                
            } catch (\Exception $e) {
                $errors[] = "Error processing item {$countData['item_id']}: {$e->getMessage()}";
            }
        }
        
        return response()->json([
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ]);
    }
    
    // Apply adjustments
    public function applyAdjustments(Request $request, CycleCount $cycleCount): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->hasRole(['warehouse_manager', 'store_manager'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($cycleCount->status !== 'in_progress') {
            return response()->json(['error' => 'Cycle count not in progress'], 400);
        }
        
        // Apply adjustments for items with variance
        $itemsWithVariance = $cycleCount->items()
            ->where('status', 'counted')
            ->whereRaw('ABS(variance) > 0.01')
            ->get();
            
        $adjustedCount = 0;
        
        foreach ($itemsWithVariance as $item) {
            $item->adjustInventory($user);
            $adjustedCount++;
        }
        
        // Complete the cycle count
        $cycleCount->complete();
        
        return response()->json([
            'success' => true,
            'adjusted_items' => $adjustedCount,
            'cycle_count' => $cycleCount->fresh()
        ]);
    }
    
    // Get cycle count summary/report
    public function summary(Request $request, CycleCount $cycleCount): JsonResponse
    {
        $summary = [
            'cycle_count' => [
                'count_number' => $cycleCount->count_number,
                'type' => $cycleCount->type,
                'status' => $cycleCount->status,
                'scheduled_date' => $cycleCount->scheduled_date,
                'completion_percentage' => $cycleCount->completion_percentage,
            ],
            'statistics' => [
                'total_items' => $cycleCount->items()->count(),
                'counted_items' => $cycleCount->items()->counted()->count(),
                'pending_items' => $cycleCount->items()->pending()->count(),
                'items_with_variance' => $cycleCount->variance_count,
                'total_variance_value' => $cycleCount->total_variance_value,
            ],
            'variances' => $cycleCount->items()
                ->withVariance()
                ->with(['item', 'location'])
                ->get()
                ->map(function ($item) {
                    return [
                        'item_name' => $item->item_name,
                        'item_sku' => $item->item_sku,
                        'location' => $item->location->name,
                        'expected_quantity' => $item->expected_quantity,
                        'counted_quantity' => $item->counted_quantity,
                        'variance' => $item->variance,
                        'variance_percentage' => $item->variance_percentage,
                        'variance_value' => $item->variance_value,
                    ];
                })
        ];
        
        return response()->json($summary);
    }
    
    // Get cycle count analytics
    public function analytics(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;
        
        if (!$store) {
            return response()->json(['error' => 'No store assigned'], 400);
        }
        
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $cycleCounts = CycleCount::forStore($store->id)
            ->whereBetween('scheduled_date', [$dateFrom, $dateTo]);
        
        $analytics = [
            'summary' => [
                'total_counts' => $cycleCounts->count(),
                'completed_counts' => $cycleCounts->where('status', 'completed')->count(),
                'pending_counts' => $cycleCounts->where('status', 'planned')->count(),
                'in_progress_counts' => $cycleCounts->where('status', 'in_progress')->count(),
            ],
            'accuracy_metrics' => [
                'average_accuracy' => $this->calculateAverageAccuracy($store->id, $dateFrom, $dateTo),
                'total_adjustments' => $this->getTotalAdjustments($store->id, $dateFrom, $dateTo),
                'adjustment_value' => $this->getAdjustmentValue($store->id, $dateFrom, $dateTo),
            ],
            'productivity' => [
                'average_completion_time' => $this->getAverageCompletionTime($store->id, $dateFrom, $dateTo),
                'items_per_count' => $this->getAverageItemsPerCount($store->id, $dateFrom, $dateTo),
            ]
        ];
        
        return response()->json($analytics);
    }
    
    private function calculateAverageAccuracy($storeId, $dateFrom, $dateTo)
    {
        $items = CycleCountItem::whereHas('cycleCount', function ($q) use ($storeId, $dateFrom, $dateTo) {
            $q->where('store_id', $storeId)
              ->whereBetween('scheduled_date', [$dateFrom, $dateTo])
              ->where('status', 'completed');
        })->where('status', '!=', 'pending')->get();
        
        if ($items->isEmpty()) return 0;
        
        return $items->avg('accuracy_rate');
    }
    
    private function getTotalAdjustments($storeId, $dateFrom, $dateTo)
    {
        return CycleCountItem::whereHas('cycleCount', function ($q) use ($storeId, $dateFrom, $dateTo) {
            $q->where('store_id', $storeId)
              ->whereBetween('scheduled_date', [$dateFrom, $dateTo])
              ->where('status', 'completed');
        })->where('status', 'adjusted')->count();
    }
    
    private function getAdjustmentValue($storeId, $dateFrom, $dateTo)
    {
        return CycleCountItem::whereHas('cycleCount', function ($q) use ($storeId, $dateFrom, $dateTo) {
            $q->where('store_id', $storeId)
              ->whereBetween('scheduled_date', [$dateFrom, $dateTo])
              ->where('status', 'completed');
        })->sum('variance_value') ?: 0;
    }
    
    private function getAverageCompletionTime($storeId, $dateFrom, $dateTo)
    {
        $completedCounts = CycleCount::where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('scheduled_date', [$dateFrom, $dateTo])
            ->whereNotNull('started_date')
            ->whereNotNull('completed_date')
            ->get();
            
        if ($completedCounts->isEmpty()) return 0;
        
        return $completedCounts->avg(function ($count) {
            return $count->started_date->diffInHours($count->completed_date);
        });
    }
    
    private function getAverageItemsPerCount($storeId, $dateFrom, $dateTo)
    {
        return CycleCount::where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('scheduled_date', [$dateFrom, $dateTo])
            ->withCount('items')
            ->get()
            ->avg('items_count') ?: 0;
    }
}