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
            if (!Schema::hasColumn('corporate_contents', 'url')) {
                $table->string('url', 500)->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('corporate_contents', 'is_mandatory')) {
                $table->boolean('is_mandatory')->default(true)->after('target_role_id');
            }
            if (!Schema::hasColumn('corporate_contents', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_mandatory');
            }
        });

        // Los enlaces externos (YouTube/Vimeo) no tienen archivo físico,
        // por lo que file_path debe admitir valores nulos.
        Schema::table('corporate_contents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_contents', function (Blueprint $table) {
            $columns = array_filter(
                ['url', 'is_mandatory', 'is_active'],
                fn ($col) => Schema::hasColumn('corporate_contents', $col)
            );

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

