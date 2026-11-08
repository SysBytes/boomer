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
        Schema::table('corporate_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('corporate_contents', 'target_user_id')) {
                $table->foreignId('target_user_id')
                    ->nullable()
                    ->after('target_role_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_contents', function (Blueprint $table) {
            if (Schema::hasColumn('corporate_contents', 'target_user_id')) {
                $table->dropForeign(['target_user_id']);
                $table->dropColumn('target_user_id');
            }
        });
    }
};

