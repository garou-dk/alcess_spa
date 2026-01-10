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
        // First, update existing 'Debit' and 'Credit' records to 'Home Credit'
        \DB::table('sales')
            ->whereIn('payment_method', ['Debit', 'Credit'])
            ->update(['payment_method' => 'Cash']); // Temporarily set to Cash
        
        // Then modify the enum
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Home Credit') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Debit', 'Credit') NOT NULL");
    }
};
