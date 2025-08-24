<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'manage_users',
            'view_users',
            
            // Store Management
            'manage_stores',
            'view_stores',
            'access_own_store_only',
            
            // Inventory Management
            'manage_inventory',
            'view_inventory',
            'adjust_stock',
            'transfer_stock',
            'receive_stock',
            
            // Product Management
            'manage_products',
            'view_products',
            'update_product_prices',
            
            // Material Management
            'manage_materials',
            'view_materials',
            'purchase_materials',
            
            // Production Management
            'manage_batches',
            'view_batches',
            'create_production_batches',
            'update_batch_status',
            'track_production_stages',
            
            // Sales Management
            'process_sales',
            'view_sales',
            'issue_refunds',
            'view_sales_reports',
            
            // Financial Management
            'view_costs',
            'view_profitability',
            'manage_pricing',
            'view_financial_reports',
            
            // Quality Control
            'manage_quality',
            'record_defects',
            'approve_quality_checkpoints',
            
            // Reporting
            'view_basic_reports',
            'view_advanced_reports',
            'export_reports',
            
            // Mobile Access
            'mobile_pos_access',
            'mobile_warehouse_access',
            'mobile_inventory_access',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - Full access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Warehouse Manager - Full warehouse operations
        $warehouseManager = Role::create(['name' => 'warehouse_manager']);
        $warehouseManager->givePermissionTo([
            'view_users',
            'manage_stores',
            'view_stores',
            'manage_inventory',
            'view_inventory',
            'adjust_stock',
            'transfer_stock',
            'receive_stock',
            'manage_products',
            'view_products',
            'manage_materials',
            'view_materials',
            'purchase_materials',
            'manage_batches',
            'view_batches',
            'create_production_batches',
            'update_batch_status',
            'track_production_stages',
            'view_costs',
            'view_profitability',
            'manage_quality',
            'record_defects',
            'approve_quality_checkpoints',
            'view_advanced_reports',
            'export_reports',
            'mobile_warehouse_access',
            'mobile_inventory_access',
        ]);

        // Store Manager - Store operations and sales
        $storeManager = Role::create(['name' => 'store_manager']);
        $storeManager->givePermissionTo([
            'view_stores',
            'access_own_store_only',
            'view_inventory',
            'transfer_stock',
            'view_products',
            'update_product_prices',
            'process_sales',
            'view_sales',
            'issue_refunds',
            'view_sales_reports',
            'view_basic_reports',
            'mobile_pos_access',
            'mobile_inventory_access',
        ]);

        // Production Supervisor - Production and quality control
        $productionSupervisor = Role::create(['name' => 'production_supervisor']);
        $productionSupervisor->givePermissionTo([
            'view_inventory',
            'view_products',
            'view_materials',
            'manage_batches',
            'view_batches',
            'update_batch_status',
            'track_production_stages',
            'manage_quality',
            'record_defects',
            'approve_quality_checkpoints',
            'view_costs',
            'view_basic_reports',
            'mobile_warehouse_access',
        ]);

        // Store Associate - Basic store operations
        $storeAssociate = Role::create(['name' => 'store_associate']);
        $storeAssociate->givePermissionTo([
            'access_own_store_only',
            'view_inventory',
            'view_products',
            'process_sales',
            'view_sales',
            'mobile_pos_access',
            'mobile_inventory_access',
        ]);

        // Warehouse Worker - Warehouse operations
        $warehouseWorker = Role::create(['name' => 'warehouse_worker']);
        $warehouseWorker->givePermissionTo([
            'view_inventory',
            'receive_stock',
            'transfer_stock',
            'view_products',
            'view_materials',
            'view_batches',
            'track_production_stages',
            'record_defects',
            'mobile_warehouse_access',
            'mobile_inventory_access',
        ]);

        // Accountant - Financial and reporting access
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view_stores',
            'view_inventory',
            'view_products',
            'view_materials',
            'view_batches',
            'view_sales',
            'view_costs',
            'view_profitability',
            'manage_pricing',
            'view_financial_reports',
            'view_advanced_reports',
            'export_reports',
        ]);
    }
}