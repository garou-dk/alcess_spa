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
        Schema::create('rate_likes', function (Blueprint $table) {
            $table->id('rate_like_id');
            $table->unsignedBigInteger('rate_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('rate_id')->references('rate_id')->on('rates')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['rate_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_likes');
    }
};
