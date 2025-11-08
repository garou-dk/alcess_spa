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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->uuid('order_public_id')->unique()->index();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->enum('order_type', ['Pickup', 'Delivery'])->default('delivery');
            $table->foreignId('barangay_id')
                ->nullable()
                ->constrained('barangays', 'barangay_id');
            $table->string('other_details')->nullable();
            $table->string('contact_number');
            $table->enum('status', [
                'Pending',
                'Confirmed',
                'Processing',
                'Shipped',
                'For delivery',
                'Delivered',
                'Released',
                'Cancelled',
                'Refunded',
            ])->default('Pending');
            $table->enum('payment_method', [
                'Cash on Delivery',
                'Online Payment',
                'Pickup Payment',
            ]);
            $table->string('bank_name')->nullable();
            $table->string('transaction_number')->nullable();
            $table->dateTime('date_paid_confirmed')->nullable();
            $table->date('estimated_delivery_date_start')->nullable();
            $table->date('estimated_delivery_date_end')->nullable();
            $table->date('customer_received_date' )->nullable();
            $table->double('shipping_fee')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('delivery_courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->softDeletesDatetime();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
