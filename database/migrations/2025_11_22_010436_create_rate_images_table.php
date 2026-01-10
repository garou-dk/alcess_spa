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
        Schema::create('rate_images', function (Blueprint $table) {
            $table->id('rate_image_id');
            $table->unsignedBigInteger('rate_id');
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('rate_id')->references('rate_id')->on('rates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_images');
    }
};
