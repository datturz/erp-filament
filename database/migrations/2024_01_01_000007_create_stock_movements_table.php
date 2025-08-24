<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->morphs('item'); // product_id or material_id
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('from_location_id')->nullable()->constrained('inventory_locations');
            $table->foreignId('to_location_id')->nullable()->constrained('inventory_locations');
            $table->decimal('quantity', 10, 4);
            $table->decimal('unit_cost', 10, 4)->nullable();
            $table->enum('type', [
                'receipt', 'shipment', 'transfer', 'adjustment', 
                'production_input', 'production_output', 'sale', 'return'
            ]);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->datetime('movement_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};