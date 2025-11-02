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
        Schema::create('municities', function (Blueprint $table) {
            $table->id('municity_id');
            $table->string('municity_code');
            $table->string('municity_name');
            $table->foreignId('province_id')->constrained('provinces', 'province_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municities');
    }
};
