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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('charge_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->string('stripe_refund_id')->unique();
            $table->string('stripe_transaction_id')->nullable();
            $table->string('program')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('date');
            $table->index('stripe_refund_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
