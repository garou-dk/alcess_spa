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
        Schema::table('users', function (Blueprint $table) {
            $table->string('recovery_key')->nullable()->after('is_active');
            $table->string('security_question')->nullable()->after('recovery_key');
            $table->text('security_answer')->nullable()->after('security_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['recovery_key', 'security_question', 'security_answer']);
        });
    }
};
