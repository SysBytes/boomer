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
            if (!Schema::hasColumn('corporate_contents', 'has_watermark')) {
                $table->boolean('has_watermark')->default(false)->after('is_mandatory');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_contents', function (Blueprint $table) {
            if (Schema::hasColumn('corporate_contents', 'has_watermark')) {
                $table->dropColumn('has_watermark');
            }
        });
    }
};
