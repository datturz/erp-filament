<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->enum('type', ['warehouse', 'retail'])->default('retail');
            $table->text('address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 10);
            $table->string('phone', 20)->nullable();
            $table->string('manager_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('business_hours')->nullable();
            $table->decimal('square_footage', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};