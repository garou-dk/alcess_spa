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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_id')->nullable()->after('order_id');
            $table->foreign('sale_id')->references('sale_id')->on('sales')->onDelete('set null');
            $table->timestamp('invoice_generated_at')->nullable()->after('date_payment_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn(['sale_id', 'invoice_generated_at']);
        });
    }
};
