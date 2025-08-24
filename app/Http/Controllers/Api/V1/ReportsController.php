<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Material;
use App\Models\Batch;
use App\Models\Store;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // Sales dashboard analytics
    public function salesAnalytics(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $storeId = $request->get('store_id');
        
        $query = Sale::whereBetween('sale_date', [$dateFrom, $dateTo]);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $analytics = [
            'summary' => [
                'total_sales' => $query->sum('final_amount'),
                'total_transactions' => $query->count(),
                'total_items_sold' => $query->sum('quantity'),
                'average_transaction' => $query->avg('final_amount'),
            ],
            'daily_sales' => $this->getDailySales($dateFrom, $dateTo, $storeId),
            'top_products' => $this->getTopProducts($dateFrom, $dateTo, $storeId),
            'payment_methods' => $this->getPaymentMethodBreakdown($dateFrom, $dateTo, $storeId),
            'store_comparison' => $this->getStoreComparison($dateFrom, $dateTo),
        ];
        
        return response()->json($analytics);
    }
    
    // Inventory analytics
    public function inventoryAnalytics(Request $request): JsonResponse
    {
        $analytics = [
            'summary' => [
                'total_products' => Product::active()->count(),
                'total_materials' => Material::active()->count(),
                'low_stock_products' => Product::lowStock()->count(),
                'low_stock_materials' => Material::lowStock()->count(),
                'total_inventory_value' => $this->getTotalInventoryValue(),
            ],
            'stock_levels' => [
                'products' => $this->getStockLevels('products'),
                'materials' => $this->getStockLevels('materials'),
            ],
            'stock_movements' => $this->getStockMovementAnalytics(),
            'abc_analysis' => $this->getABCAnalysis(),
            'turnover_rates' => $this->getInventoryTurnover(),
        ];
        
        return response()->json($analytics);
    }
    
    // Production analytics
    public function productionAnalytics(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $analytics = [
            'summary' => [
                'active_batches' => Batch::where('status', 'in_progress')->count(),
                'completed_batches' => Batch::where('status', 'completed')
                    ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
                    ->count(),
                'total_production' => Batch::where('status', 'completed')
                    ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
                    ->sum('quantity_quality_passed'),
                'average_yield' => Batch::where('status', 'completed')
                    ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
                    ->avg('quality_pass_rate'),
            ],
            'efficiency_metrics' => $this->getProductionEfficiency($dateFrom, $dateTo),
            'cost_analysis' => $this->getProductionCosts($dateFrom, $dateTo),
            'quality_metrics' => $this->getQualityMetrics($dateFrom, $dateTo),
            'batch_performance' => $this->getBatchPerformance($dateFrom, $dateTo),
        ];
        
        return response()->json($analytics);
    }
    
    // Financial analytics
    public function financialAnalytics(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $analytics = [
            'profitability' => $this->getProfitabilityAnalysis($dateFrom, $dateTo),
            'cost_breakdown' => $this->getCostBreakdown($dateFrom, $dateTo),
            'margin_analysis' => $this->getMarginAnalysis($dateFrom, $dateTo),
            'kpi_dashboard' => $this->getKPIDashboard($dateFrom, $dateTo),
        ];
        
        return response()->json($analytics);
    }
    
    // Export report data
    public function exportReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:sales,inventory,production,financial',
            'format' => 'required|in:csv,pdf,excel',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);
        
        // Generate export job
        $exportJob = [
            'id' => uniqid(),
            'type' => $request->report_type,
            'format' => $request->format,
            'status' => 'processing',
            'created_at' => now(),
            'download_url' => null,
        ];
        
        // In real implementation, dispatch job to queue
        // For now, return job info
        
        return response()->json($exportJob);
    }
    
    // Private helper methods
    private function getDailySales($dateFrom, $dateTo, $storeId = null)
    {
        $query = Sale::selectRaw('DATE(sale_date) as date, SUM(final_amount) as amount, COUNT(*) as transactions')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date');
            
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        return $query->get();
    }
    
    private function getTopProducts($dateFrom, $dateTo, $storeId = null)
    {
        $query = Sale::select('products.name', DB::raw('SUM(sales.quantity) as total_sold'), DB::raw('SUM(sales.final_amount) as total_revenue'))
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10);
            
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        return $query->get();
    }
    
    private function getPaymentMethodBreakdown($dateFrom, $dateTo, $storeId = null)
    {
        $query = Sale::selectRaw('payment_method, COUNT(*) as count, SUM(final_amount) as amount')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method');
            
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        return $query->get();
    }
    
    private function getStoreComparison($dateFrom, $dateTo)
    {
        return Sale::select('stores.name', DB::raw('SUM(sales.final_amount) as revenue'), DB::raw('COUNT(sales.id) as transactions'))
            ->join('stores', 'sales.store_id', '=', 'stores.id')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('revenue')
            ->get();
    }
    
    private function getTotalInventoryValue()
    {
        $productValue = Product::selectRaw('SUM(current_stock * COALESCE(cost_price, retail_price)) as value')->value('value') ?: 0;
        $materialValue = Material::selectRaw('SUM(current_stock * unit_cost) as value')->value('value') ?: 0;
        
        return $productValue + $materialValue;
    }
    
    private function getStockLevels($type)
    {
        if ($type === 'products') {
            return [
                'normal' => Product::whereRaw('current_stock > minimum_stock')->count(),
                'low' => Product::whereRaw('current_stock <= minimum_stock AND current_stock > 0')->count(),
                'out_of_stock' => Product::where('current_stock', 0)->count(),
            ];
        }
        
        return [
            'normal' => Material::whereRaw('current_stock > minimum_stock')->count(),
            'low' => Material::whereRaw('current_stock <= minimum_stock AND current_stock > 0')->count(),
            'out_of_stock' => Material::where('current_stock', 0)->count(),
        ];
    }
    
    private function getStockMovementAnalytics()
    {
        return StockMovement::selectRaw('type, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
            ->where('movement_date', '>=', now()->subMonth())
            ->groupBy('type')
            ->get();
    }
    
    private function getABCAnalysis()
    {
        // Simplified ABC analysis based on sales volume
        $products = Sale::select('product_id', DB::raw('SUM(quantity * unit_price) as revenue'))
            ->where('sale_date', '>=', now()->subYear())
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->get();
            
        $total = $products->sum('revenue');
        $cumulative = 0;
        
        return $products->map(function ($product, $index) use ($total, &$cumulative) {
            $cumulative += $product->revenue;
            $percentage = ($cumulative / $total) * 100;
            
            $category = 'C';
            if ($percentage <= 80) $category = 'A';
            elseif ($percentage <= 95) $category = 'B';
            
            return [
                'product_id' => $product->product_id,
                'revenue' => $product->revenue,
                'category' => $category,
            ];
        });
    }
    
    private function getInventoryTurnover()
    {
        // Simplified turnover calculation
        return Product::select('id', 'name', 'current_stock')
            ->with(['sales' => function($query) {
                $query->where('sale_date', '>=', now()->subYear());
            }])
            ->get()
            ->map(function ($product) {
                $soldQuantity = $product->sales->sum('quantity');
                $avgStock = $product->current_stock;
                
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'turnover_rate' => $avgStock > 0 ? $soldQuantity / $avgStock : 0,
                ];
            });
    }
    
    private function getProductionEfficiency($dateFrom, $dateTo)
    {
        return Batch::where('status', 'completed')
            ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
            ->selectRaw('
                AVG(DATEDIFF(actual_end_date, actual_start_date)) as avg_duration,
                AVG((quantity_produced / quantity_planned) * 100) as avg_efficiency,
                AVG((quantity_quality_passed / quantity_produced) * 100) as avg_quality_rate
            ')
            ->first();
    }
    
    private function getProductionCosts($dateFrom, $dateTo)
    {
        return Batch::where('status', 'completed')
            ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
            ->selectRaw('
                SUM(actual_cost) as total_cost,
                AVG(actual_cost / quantity_produced) as avg_unit_cost,
                SUM(actual_cost) / SUM(quantity_produced) as weighted_avg_cost
            ')
            ->first();
    }
    
    private function getQualityMetrics($dateFrom, $dateTo)
    {
        return Batch::where('status', 'completed')
            ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
            ->selectRaw('
                AVG((quantity_quality_passed / quantity_produced) * 100) as pass_rate,
                AVG((quantity_defective / quantity_produced) * 100) as defect_rate,
                SUM(quantity_defective) as total_defects
            ')
            ->first();
    }
    
    private function getBatchPerformance($dateFrom, $dateTo)
    {
        return Batch::select('batch_number', 'quantity_planned', 'quantity_produced', 'quality_pass_rate', 'actual_cost')
            ->where('status', 'completed')
            ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
            ->orderBy('actual_end_date', 'desc')
            ->limit(20)
            ->get();
    }
    
    private function getProfitabilityAnalysis($dateFrom, $dateTo)
    {
        $sales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->selectRaw('SUM(final_amount) as revenue')
            ->first();
            
        $costs = Batch::where('status', 'completed')
            ->whereBetween('actual_end_date', [$dateFrom, $dateTo])
            ->selectRaw('SUM(actual_cost) as production_cost')
            ->first();
            
        $revenue = $sales->revenue ?: 0;
        $productionCost = $costs->production_cost ?: 0;
        
        return [
            'revenue' => $revenue,
            'production_cost' => $productionCost,
            'gross_profit' => $revenue - $productionCost,
            'gross_margin' => $revenue > 0 ? (($revenue - $productionCost) / $revenue) * 100 : 0,
        ];
    }
    
    private function getCostBreakdown($dateFrom, $dateTo)
    {
        // Material costs from batch materials
        $materialCosts = DB::table('batch_materials')
            ->join('batches', 'batch_materials.batch_id', '=', 'batches.id')
            ->whereBetween('batches.actual_end_date', [$dateFrom, $dateTo])
            ->sum('batch_materials.total_cost');
            
        // Labor costs from production stages
        $laborCosts = DB::table('batch_production_stages')
            ->join('batches', 'batch_production_stages.batch_id', '=', 'batches.id')
            ->whereBetween('batches.actual_end_date', [$dateFrom, $dateTo])
            ->sum('batch_production_stages.labor_cost');
            
        return [
            'material_costs' => $materialCosts,
            'labor_costs' => $laborCosts,
            'total_costs' => $materialCosts + $laborCosts,
        ];
    }
    
    private function getMarginAnalysis($dateFrom, $dateTo)
    {
        return Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereNotNull('products.cost_price')
            ->selectRaw('
                products.name,
                AVG(sales.unit_price) as avg_selling_price,
                AVG(products.cost_price) as avg_cost,
                AVG((sales.unit_price - products.cost_price) / sales.unit_price * 100) as avg_margin
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('avg_margin')
            ->get();
    }
    
    private function getKPIDashboard($dateFrom, $dateTo)
    {
        return [
            'revenue_growth' => $this->calculateGrowthRate('revenue', $dateFrom, $dateTo),
            'production_efficiency' => $this->getProductionEfficiency($dateFrom, $dateTo),
            'inventory_turnover' => $this->calculateInventoryTurnover($dateFrom, $dateTo),
            'customer_satisfaction' => 95, // Placeholder - would integrate with survey data
        ];
    }
    
    private function calculateGrowthRate($metric, $dateFrom, $dateTo)
    {
        // Simplified growth rate calculation
        $currentPeriod = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->sum('final_amount');
        $previousPeriod = Sale::whereBetween('sale_date', [
            now()->parse($dateFrom)->subDays(now()->parse($dateTo)->diffInDays($dateFrom))->toDateString(),
            $dateFrom
        ])->sum('final_amount');
        
        return $previousPeriod > 0 ? (($currentPeriod - $previousPeriod) / $previousPeriod) * 100 : 0;
    }
    
    private function calculateInventoryTurnover($dateFrom, $dateTo)
    {
        $cogs = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('SUM(sales.quantity * COALESCE(products.cost_price, products.retail_price))')
            ->value('sum') ?: 0;
            
        $avgInventory = $this->getTotalInventoryValue();
        
        return $avgInventory > 0 ? $cogs / $avgInventory : 0;
    }
}