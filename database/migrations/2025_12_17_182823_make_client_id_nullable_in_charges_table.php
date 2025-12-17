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
            // Drop the foreign key constraint first
            $table->dropForeign(['client_id']);
            // Make the column nullable
            $table->unsignedBigInteger('client_id')->nullable()->change();
            // Re-add the foreign key constraint (it will now allow nulls)
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['client_id']);
            // Make the column not nullable
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            // Re-add the foreign key constraint
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
