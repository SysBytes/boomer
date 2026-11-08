<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('corporate_content_role')) {
            Schema::create('corporate_content_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('corporate_content_id')
                    ->constrained('corporate_contents')
                    ->cascadeOnDelete();
                $table->foreignId('role_id')
                    ->constrained('roles')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['corporate_content_id', 'role_id']);
            });

            // Migrar asignaciones previas de target_role_id si existieran
            if (Schema::hasColumn('corporate_contents', 'target_role_id')) {
                $existing = DB::table('corporate_contents')
                    ->whereNotNull('target_role_id')
                    ->get(['id', 'target_role_id']);

                foreach ($existing as $row) {
                    DB::table('corporate_content_role')->insertOrIgnore([
                        'corporate_content_id' => $row->id,
                        'role_id' => $row->target_role_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_content_role');
    }
};

