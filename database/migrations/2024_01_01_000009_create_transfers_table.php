<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('from_store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity_requested');
            $table->integer('quantity_shipped')->nullable();
            $table->integer('quantity_received')->nullable();
            $table->enum('status', ['requested', 'approved', 'shipped', 'received', 'cancelled'])->default('requested');
            $table->date('requested_date');
            $table->date('shipped_date')->nullable();
            $table->date('received_date')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('shipped_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};