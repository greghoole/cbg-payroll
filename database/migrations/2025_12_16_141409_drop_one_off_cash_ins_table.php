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
        Schema::dropIfExists('one_off_cash_ins');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration drops the one_off_cash_ins table
        // If you need to recreate it, you would need to run the original create migration
        // We don't recreate it here to avoid conflicts
    }
};
