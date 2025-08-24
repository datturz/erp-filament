<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_production_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('production_stage_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_input')->nullable();
            $table->integer('quantity_output')->nullable();
            $table->integer('quantity_defective')->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->decimal('labor_cost', 10, 2)->nullable();
            $table->text('quality_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->unique(['batch_id', 'production_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_production_stages');
    }
};