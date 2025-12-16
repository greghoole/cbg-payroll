<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add coach_id column to clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('coach_id')->nullable()->after('country')->constrained()->onDelete('set null');
        });

        // Migrate existing data from pivot table to clients table
        // If a client has multiple coaches, we'll take the first one
        if (Schema::hasTable('client_coach')) {
            DB::statement('
                UPDATE clients 
                SET coach_id = (
                    SELECT coach_id 
                    FROM client_coach 
                    WHERE client_coach.client_id = clients.id 
                    ORDER BY client_coach.created_at ASC 
                    LIMIT 1
                )
                WHERE EXISTS (
                    SELECT 1 
                    FROM client_coach 
                    WHERE client_coach.client_id = clients.id
                )
            ');
        }

        // Drop the pivot table
        Schema::dropIfExists('client_coach');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the pivot table
        Schema::create('client_coach', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('coach_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['client_id', 'coach_id']);
        });

        // Migrate data back from clients to pivot table
        DB::statement('
            INSERT INTO client_coach (client_id, coach_id, commission_rate, created_at, updated_at)
            SELECT id, coach_id, 0, NOW(), NOW()
            FROM clients
            WHERE coach_id IS NOT NULL
        ');

        // Remove coach_id column from clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn('coach_id');
        });
    }
};
