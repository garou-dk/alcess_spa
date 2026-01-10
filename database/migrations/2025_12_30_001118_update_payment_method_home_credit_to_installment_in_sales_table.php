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
        // First, temporarily expand the enum to include both 'Home Credit' and 'Installment'
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Home Credit', 'Installment') NOT NULL");
        
        // Then, update existing 'Home Credit' records to 'Installment'
        \DB::table('sales')
            ->where('payment_method', 'Home Credit')
            ->update(['payment_method' => 'Installment']);
        
        // Finally, remove 'Home Credit' from the enum
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Installment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, temporarily expand the enum to include both 'Home Credit' and 'Installment'
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Home Credit', 'Installment') NOT NULL");
        
        // Then, update existing 'Installment' records back to 'Home Credit'
        \DB::table('sales')
            ->where('payment_method', 'Installment')
            ->update(['payment_method' => 'Home Credit']);
            
        // Finally, remove 'Installment' from the enum
        \DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('Cash', 'E-wallet', 'Home Credit') NOT NULL");
    }
};