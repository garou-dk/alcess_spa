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
        // First, update existing orders to new status values
        DB::table('orders')
            ->where('status', 'Pending')
            ->update(['status' => 'Processing']);
        
        // Keep Confirmed as is
        // Keep Processing as is (though it will mean something different now)
        // Keep Cancelled as is
        
        // Update Delivered to Completed
        DB::table('orders')
            ->where('status', 'Delivered')
            ->update(['status' => 'Completed']);
        
        // Update Rejected to Cancelled
        DB::table('orders')
            ->where('status', 'Rejected')
            ->update(['status' => 'Cancelled']);
        
        // Update the enum to only allow new statuses
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'Processing',
            'Confirmed',
            'Shipped',
            'Completed',
            'Cancelled'
        ) DEFAULT 'Processing'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original enum values
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'Pending',
            'Confirmed',
            'Processing',
            'Shipped',
            'For delivery',
            'Delivered',
            'Completed',
            'Released',
            'Cancelled',
            'Refunded',
            'Rejected'
        ) DEFAULT 'Pending'");
        
        // Restore original status values
        DB::table('orders')
            ->where('status', 'Processing')
            ->update(['status' => 'Pending']);
        
        DB::table('orders')
            ->where('status', 'Completed')
            ->update(['status' => 'Delivered']);
    }
};
