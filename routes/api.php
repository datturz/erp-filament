<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MobileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mobile API v1
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [MobileController::class, 'dashboard']);
    
    // Product operations
    Route::get('/products/search', [MobileController::class, 'productLookup']);
    Route::get('/stock/check', [MobileController::class, 'stockCheck']);
    
    // Sales operations (store staff only)
    Route::middleware('role:store_manager|store_associate')->group(function () {
        Route::post('/sales/process', [MobileController::class, 'processSale']);
    });
    
    // Production operations (warehouse staff only)
    Route::middleware('role:warehouse_manager|warehouse_worker|production_supervisor')->group(function () {
        Route::get('/batches', [MobileController::class, 'productionBatches']);
        Route::put('/batches/{batch}/progress', [MobileController::class, 'updateBatchProgress']);
    });
    
    // Inventory operations
    Route::middleware('role:warehouse_manager|warehouse_worker|store_manager')->group(function () {
        Route::get('/inventory/locations', [MobileController::class, 'inventoryLocations']);
        Route::post('/inventory/adjust', [MobileController::class, 'adjustInventory']);
    });
    
    // Transfer operations
    Route::apiResource('transfers', App\Http\Controllers\Api\V1\TransferController::class);
    Route::post('/transfers/{transfer}/approve', [App\Http\Controllers\Api\V1\TransferController::class, 'approve']);
    Route::post('/transfers/{transfer}/ship', [App\Http\Controllers\Api\V1\TransferController::class, 'ship']);
    Route::post('/transfers/{transfer}/receive', [App\Http\Controllers\Api\V1\TransferController::class, 'receive']);
    Route::post('/transfers/{transfer}/cancel', [App\Http\Controllers\Api\V1\TransferController::class, 'cancel']);
    Route::get('/transfers/pending/approvals', [App\Http\Controllers\Api\V1\TransferController::class, 'pendingApprovals']);
    Route::get('/analytics/transfers', [App\Http\Controllers\Api\V1\TransferController::class, 'analytics']);
    
    // Reports and analytics
    Route::get('/reports/sales', [App\Http\Controllers\Api\V1\ReportsController::class, 'salesAnalytics']);
    Route::get('/reports/inventory', [App\Http\Controllers\Api\V1\ReportsController::class, 'inventoryAnalytics']);
    Route::get('/reports/production', [App\Http\Controllers\Api\V1\ReportsController::class, 'productionAnalytics']);
    Route::get('/reports/financial', [App\Http\Controllers\Api\V1\ReportsController::class, 'financialAnalytics']);
    Route::post('/reports/export', [App\Http\Controllers\Api\V1\ReportsController::class, 'exportReport']);
    
    // Cycle counting
    Route::apiResource('cycle-counts', App\Http\Controllers\Api\V1\CycleCountController::class);
    Route::post('/cycle-counts/{cycleCount}/start', [App\Http\Controllers\Api\V1\CycleCountController::class, 'start']);
    Route::get('/cycle-counts/{cycleCount}/items', [App\Http\Controllers\Api\V1\CycleCountController::class, 'items']);
    Route::post('/cycle-count-items/{item}/record', [App\Http\Controllers\Api\V1\CycleCountController::class, 'recordCount']);
    Route::post('/cycle-counts/{cycleCount}/bulk-record', [App\Http\Controllers\Api\V1\CycleCountController::class, 'bulkRecord']);
    Route::post('/cycle-counts/{cycleCount}/apply-adjustments', [App\Http\Controllers\Api\V1\CycleCountController::class, 'applyAdjustments']);
    Route::get('/cycle-counts/{cycleCount}/summary', [App\Http\Controllers\Api\V1\CycleCountController::class, 'summary']);
    Route::get('/analytics/cycle-counts', [App\Http\Controllers\Api\V1\CycleCountController::class, 'analytics']);
    
});