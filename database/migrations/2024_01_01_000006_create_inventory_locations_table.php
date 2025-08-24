<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "A1-01", "Front Display", "Backroom Shelf 3"
            $table->string('type')->default('shelf'); // shelf, rack, display, floor, bin
            $table->string('zone')->nullable(); // A, B, C or Front, Back, etc.
            $table->string('aisle')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('capacity', 8, 2)->nullable();
            $table->enum('location_type', ['raw_material', 'wip', 'finished_goods', 'general']);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['store_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};