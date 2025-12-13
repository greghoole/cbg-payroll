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
        Schema::table('refunds', function (Blueprint $table) {
            // Drop unique constraint on stripe_refund_id first
            $table->dropUnique(['stripe_refund_id']);
        });
        
        Schema::table('refunds', function (Blueprint $table) {
            // Rename program to reason
            $table->renameColumn('program', 'reason');
            
            // Make stripe_refund_id nullable
            $table->string('stripe_refund_id')->nullable()->change();
            
            // Make stripe_transaction_id required and unique (primary identifier)
            $table->string('stripe_transaction_id')->nullable(false)->unique()->change();
            
            // Add initial_amount_charged field
            $table->decimal('initial_amount_charged', 10, 2)->nullable()->after('amount');
            
            // Add index on stripe_transaction_id
            $table->index('stripe_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Remove index on stripe_transaction_id
            $table->dropIndex(['stripe_transaction_id']);
            
            // Remove initial_amount_charged field
            $table->dropColumn('initial_amount_charged');
            
            // Make stripe_transaction_id nullable again
            $table->string('stripe_transaction_id')->nullable()->change();
            
            // Make stripe_refund_id required again
            $table->string('stripe_refund_id')->nullable(false)->change();
        });
        
        Schema::table('refunds', function (Blueprint $table) {
            // Restore unique constraint on stripe_refund_id
            $table->unique('stripe_refund_id');
            
            // Rename reason back to program
            $table->renameColumn('reason', 'program');
        });
    }
};
