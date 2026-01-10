<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Remove duplicate rates, keeping only the first (oldest) review per product_order_id
        DB::statement("
            DELETE r1 FROM rates r1
            INNER JOIN rates r2 
            WHERE r1.product_order_id = r2.product_order_id
            AND r1.rate_id > r2.rate_id
        ");
        
        // Step 2: Add unique constraint on product_order_id
        Schema::table('rates', function (Blueprint $table) {
            $table->unique('product_order_id', 'rates_product_order_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropUnique('rates_product_order_id_unique');
        });
    }
};
