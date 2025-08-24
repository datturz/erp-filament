<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('type', ['fabric', 'button', 'zipper', 'thread', 'label', 'other']);
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->enum('unit_of_measure', ['yards', 'pieces', 'meters', 'rolls', 'boxes']);
            $table->decimal('unit_cost', 10, 4);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_sku')->nullable();
            $table->integer('lead_time_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};