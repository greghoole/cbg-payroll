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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('net', 10, 2); // Amount after fees
            $table->decimal('amount_charged', 10, 2); // Gross amount
            $table->string('program')->nullable();
            $table->string('stripe_url')->nullable();
            $table->string('stripe_transaction_id')->unique();
            $table->string('stripe_charge_id')->unique();
            $table->boolean('billing_information_included')->default(false);
            $table->string('country')->nullable();
            $table->timestamps();
            
            $table->index('date');
            $table->index('stripe_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
