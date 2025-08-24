<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MobileController extends Controller
{
    // Dashboard for mobile users
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;
        
        $data = [
            'user' => [
                'name' => $user->name,
                'role' => $user->getRoleNames()->first(),
                'store' => $store?->name,
            ],
            'stats' => $this->getDashboardStats($user, $store),
            'alerts' => $this->getAlerts($user, $store),
        ];
        
        return response()->json($data);
    }
    
    // Quick product lookup for POS
    public function productLookup(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', $query);
            })
            ->limit(20)
            ->get(['id', 'name', 'sku', 'barcode', 'retail_price', 'current_stock']);
            
        return response()->json($products);
    }
    
    // Process POS sale
    public function processSale(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,credit_card,debit_card,check,store_credit',
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
        ]);
        
        $user = $request->user();
        $store = $user->store;
        
        if (!$store || $store->type !== 'retail') {
            return response()->json(['error' => 'Invalid store for sales'], 400);
        }
        
        $totalAmount = 0;
        $sales = [];
        
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            if ($product->current_stock < $item['quantity']) {
                return response()->json(['error' => "Insufficient stock for {$product->name}"], 400);
            }
            
            $sale = Sale::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'store_id' => $store->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->retail_price,
                'total_price' => $product->retail_price * $item['quantity'],
                'discount_amount' => $item['discount'] ?? 0,
                'tax_amount' => ($product->retail_price * $item['quantity']) * 0.08, // 8% tax
                'final_amount' => ($product->retail_price * $item['quantity']) * 1.08,
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'processed_by' => $user->id,
                'sale_date' => now(),
            ]);
            
            // Update stock
            $product->decrement('current_stock', $item['quantity']);
            
            // Create stock movement
            StockMovement::create([
                'item_type' => Product::class,
                'item_id' => $product->id,
                'quantity' => -$item['quantity'],
                'type' => 'sale',
                'movement_date' => now(),
                'created_by' => $user->id,
            ]);
            
            $sales[] = $sale;
            $totalAmount += $sale->final_amount;
        }
        
        return response()->json([
            'success' => true,
            'transaction_number' => $sales[0]->transaction_number,
            'total_amount' => $totalAmount,
            'sales' => $sales,
        ]);
    }
    
    // Stock check for warehouse
    public function stockCheck(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        $items = collect();
        
        // Search products
        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', $query);
            })
            ->get(['id', 'name', 'sku', 'current_stock', 'minimum_stock']);
            
        foreach ($products as $product) {
            $items->push([
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->current_stock,
                'minimum_stock' => $product->minimum_stock,
                'status' => $product->stock_status,
            ]);
        }
        
        return response()->json($items);
    }
    
    // Get production batches for warehouse
    public function productionBatches(Request $request): JsonResponse
    {
        $batches = Batch::with('product:id,name')
            ->where('status', '!=', 'completed')
            ->orderBy('planned_end_date')
            ->limit(20)
            ->get();
            
        return response()->json($batches);
    }
    
    // Update batch progress
    public function updateBatchProgress(Request $request, Batch $batch): JsonResponse
    {
        $request->validate([
            'quantity_produced' => 'required|integer|min:0',
            'quantity_quality_passed' => 'required|integer|min:0',
            'quantity_defective' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $batch->update($request->only([
            'quantity_produced', 
            'quantity_quality_passed', 
            'quantity_defective', 
            'notes'
        ]));
        
        return response()->json(['success' => true, 'batch' => $batch]);
    }
    
    private function getDashboardStats($user, $store): array
    {
        if ($user->hasRole('store_associate') && $store) {
            return [
                'today_sales' => Sale::where('store_id', $store->id)
                    ->whereDate('sale_date', today())
                    ->sum('final_amount'),
                'today_transactions' => Sale::where('store_id', $store->id)
                    ->whereDate('sale_date', today())
                    ->count(),
                'low_stock_items' => Product::lowStock()->count(),
            ];
        }
        
        if ($user->hasRole('warehouse_worker')) {
            return [
                'active_batches' => Batch::where('status', 'in_progress')->count(),
                'pending_transfers' => 0, // TODO: implement transfers
                'low_stock_materials' => \App\Models\Material::lowStock()->count(),
            ];
        }
        
        return [];
    }
    
    private function getAlerts($user, $store): array
    {
        $alerts = [];
        
        if ($user->hasRole(['store_manager', 'store_associate']) && $store) {
            $lowStock = Product::lowStock()->count();
            if ($lowStock > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "{$lowStock} products are low on stock",
                ];
            }
        }
        
        return $alerts;
    }
    
    private function generateTransactionNumber(): string
    {
        return 'TXN-' . now()->format('Ymd') . '-' . str_pad(
            Sale::whereDate('created_at', today())->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}