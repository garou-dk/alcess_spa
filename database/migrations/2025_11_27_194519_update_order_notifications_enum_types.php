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
        DB::statement("ALTER TABLE order_notifications MODIFY COLUMN notification_type ENUM(
            'Pending Order',
            'Confirmed Order',
            'To Ship',
            'Paid',
            'Cancelled Order',
            'Rejected Order',
            'New Order',
            'Order Accepted',
            'Order Declined',
            'Payment Confirmed',
            'Payment Uploaded'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE order_notifications MODIFY COLUMN notification_type ENUM(
            'Pending Order',
            'Confirmed Order',
            'To Ship',
            'Paid',
            'Cancelled Order',
            'Rejected Order'
        )");
    }
};
