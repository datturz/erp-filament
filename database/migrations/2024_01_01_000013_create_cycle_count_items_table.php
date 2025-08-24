<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycle_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_count_id')->constrained()->onDelete('cascade');
            $table->morphs('item'); // product_id or material_id
            $table->foreignId('location_id')->constrained('inventory_locations');
            $table->decimal('expected_quantity', 10, 4);
            $table->decimal('counted_quantity', 10, 4)->nullable();
            $table->decimal('variance', 10, 4)->nullable();
            $table->decimal('variance_value', 10, 2)->nullable();
            $table->enum('status', ['pending', 'counted', 'adjusted', 'verified'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('counted_by')->nullable()->constrained('users');
            $table->datetime('counted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycle_count_items');
    }
};