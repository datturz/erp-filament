<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_planned');
            $table->integer('quantity_produced')->default(0);
            $table->integer('quantity_quality_passed')->default(0);
            $table->integer('quantity_defective')->default(0);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->datetime('actual_start_date')->nullable();
            $table->datetime('actual_end_date')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};