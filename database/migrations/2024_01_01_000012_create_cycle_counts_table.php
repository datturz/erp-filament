<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycle_counts', function (Blueprint $table) {
            $table->id();
            $table->string('count_number')->unique();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations');
            $table->enum('type', ['full', 'partial', 'abc', 'random'])->default('partial');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->date('scheduled_date');
            $table->date('started_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycle_counts');
    }
};