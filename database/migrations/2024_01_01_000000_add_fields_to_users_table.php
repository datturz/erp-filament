<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('employee_id', 20)->unique()->nullable()->after('phone');
            $table->foreignId('store_id')->nullable()->constrained()->after('employee_id');
            $table->boolean('is_active')->default(true)->after('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
            $table->dropColumn(['phone', 'employee_id', 'is_active']);
        });
    }
};