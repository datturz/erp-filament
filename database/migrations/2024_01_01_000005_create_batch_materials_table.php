<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_required', 10, 4);
            $table->decimal('quantity_used', 10, 4)->default(0);
            $table->decimal('unit_cost', 10, 4);
            $table->decimal('total_cost', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['batch_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_materials');
    }
};