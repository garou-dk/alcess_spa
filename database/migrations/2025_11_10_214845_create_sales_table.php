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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');
            $table->uuid('sale_code')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->double('total_amount');
            $table->enum('payment_method', ['Cash', 'E-wallet', 'Debit', 'Credit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
