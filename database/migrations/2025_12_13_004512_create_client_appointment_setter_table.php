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
        Schema::create('client_appointment_setter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_setter_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['client_id', 'appointment_setter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_appointment_setter');
    }
};
