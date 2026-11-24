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
        if (Schema::hasTable('user_induction_evidences') && !Schema::hasColumn('user_induction_evidences', 'content_id')) {
            Schema::table('user_induction_evidences', function (Blueprint $table) {
                $table->foreignId('content_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('corporate_contents')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_induction_evidences') && Schema::hasColumn('user_induction_evidences', 'content_id')) {
            Schema::table('user_induction_evidences', function (Blueprint $table) {
                $table->dropConstrainedForeignId('content_id');
            });
        }
    }
};
