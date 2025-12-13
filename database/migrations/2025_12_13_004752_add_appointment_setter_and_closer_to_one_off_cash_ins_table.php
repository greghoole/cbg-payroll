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
        Schema::table('one_off_cash_ins', function (Blueprint $table) {
            $table->foreignId('appointment_setter_id')->nullable()->after('coach_id')->constrained()->onDelete('cascade');
            $table->foreignId('closer_id')->nullable()->after('appointment_setter_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_off_cash_ins', function (Blueprint $table) {
            $table->dropForeign(['appointment_setter_id']);
            $table->dropForeign(['closer_id']);
            $table->dropColumn(['appointment_setter_id', 'closer_id']);
        });
    }
};
