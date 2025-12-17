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
        Schema::table('charges', function (Blueprint $table) {
            // Make columns nullable (MySQL allows multiple NULLs in unique columns)
            $table->string('stripe_transaction_id')->nullable()->change();
            $table->string('stripe_charge_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            // Make columns not nullable
            // Note: This will fail if there are any NULL values in the database
            $table->string('stripe_transaction_id')->nullable(false)->change();
            $table->string('stripe_charge_id')->nullable(false)->change();
        });
    }
};
