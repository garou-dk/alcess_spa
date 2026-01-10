<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('payment_method');
            $table->text('customer_address')->nullable()->after('customer_name');
            $table->foreignId('prepared_by')->nullable()->after('customer_address')->constrained('users', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['prepared_by']);
            $table->dropColumn(['customer_name', 'customer_address', 'prepared_by']);
        });
    }
};
