<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@pantsrp.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Create store manager
        $storeManager = User::firstOrCreate(
            ['email' => 'store@pantsrp.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('store123'),
                'email_verified_at' => now(),
            ]
        );

        // Create warehouse worker
        $warehouseWorker = User::firstOrCreate(
            ['email' => 'warehouse@pantsrp.com'],
            [
                'name' => 'Warehouse Worker',
                'password' => Hash::make('warehouse123'),
                'email_verified_at' => now(),
            ]
        );

        echo "Default users created:\n";
        echo "Admin: admin@pantsrp.com / admin123\n";
        echo "Store: store@pantsrp.com / store123\n";
        echo "Warehouse: warehouse@pantsrp.com / warehouse123\n";
    }
}