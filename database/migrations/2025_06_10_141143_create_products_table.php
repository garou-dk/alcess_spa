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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name')->unique();
            $table->text('description');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->foreignId('unit_id')->constrained('units', 'unit_id');
            $table->string('product_image');
            $table->double('product_price');
            $table->unsignedInteger('product_quantity');
            $table->unsignedInteger('low_stock_threshold');
            $table->boolean('is_active');
            $table->string('sku')->nullable()->unique();
            $table->softDeletesDatetime();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
