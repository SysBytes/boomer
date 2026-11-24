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
        if (Schema::hasTable('user_induction_evidences') && !Schema::hasColumn('user_induction_evidences', 'signature_path')) {
            Schema::table('user_induction_evidences', function (Blueprint $table) {
                $table->string('signature_path')->nullable()->after('evidence_photo_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_induction_evidences') && Schema::hasColumn('user_induction_evidences', 'signature_path')) {
            Schema::table('user_induction_evidences', function (Blueprint $table) {
                $table->dropColumn('signature_path');
            });
        }
    }
};
