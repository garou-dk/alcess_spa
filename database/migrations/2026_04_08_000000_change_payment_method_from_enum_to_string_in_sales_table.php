<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes payment_method from ENUM('Cash', 'E-wallet', 'Installment') to VARCHAR(255)
     * to support compound payment method values like 'E-wallet - Gcash', 'Installment - Home Credit', etc.
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First reset any compound values back to base values
        \DB::table('sales')
            ->where('payment_method', 'LIKE', 'E-wallet%')
            ->update(['payment_method' => 'E-wallet']);

        \DB::table('sales')
            ->where('payment_method', 'LIKE', 'Installment%')
            ->update(['payment_method' => 'Installment']);

        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Installment') NOT NULL");
    }
};
