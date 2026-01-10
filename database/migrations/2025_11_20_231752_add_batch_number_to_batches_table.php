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
        Schema::table('batches', function (Blueprint $table) {
            // Add batch_number column after batch_id
            // String with length 20 to accommodate format DDMMYY-N with large counters
            $table->string('batch_number', 20)->unique()->after('batch_id');
            
            // Add index for performance when querying for batch number generation
            $table->index('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Drop index first, then column
            $table->dropIndex(['batch_number']);
            $table->dropColumn('batch_number');
        });
    }
};
