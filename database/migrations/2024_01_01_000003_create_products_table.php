<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('type', ['jeans', 'chinos', 'dress_pants', 'shorts', 'other']);
            $table->text('description')->nullable();
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36', '38', '40', '42']);
            $table->string('color');
            $table->enum('fit', ['slim', 'regular', 'relaxed', 'bootcut', 'straight']);
            $table->decimal('retail_price', 10, 2);
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->string('barcode')->nullable()->unique();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};