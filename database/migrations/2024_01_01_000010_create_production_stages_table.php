<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Cutting", "Sewing", "Finishing", "Quality Control"
            $table->text('description')->nullable();
            $table->integer('sequence_order');
            $table->decimal('estimated_hours_per_unit', 5, 2)->nullable();
            $table->decimal('labor_rate_per_hour', 8, 2)->nullable();
            $table->boolean('quality_checkpoint')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_stages');
    }
};