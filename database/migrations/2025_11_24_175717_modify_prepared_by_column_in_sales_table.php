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
        Schema::table('sales', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['prepared_by']);
            
            // Modify the column to be a string instead of foreign key
            $table->string('prepared_by', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Revert back to foreign key
            $table->foreignId('prepared_by')->nullable()->change();
            $table->foreign('prepared_by')->references('user_id')->on('users');
        });
    }
};
