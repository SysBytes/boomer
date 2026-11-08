<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('time_logs', 'check_in_observation')) {
                $table->text('check_in_observation')->nullable()->after('check_in_lng');
            }
            if (!Schema::hasColumn('time_logs', 'check_in_registered_by')) {
                $table->bigInteger('check_in_registered_by')->nullable()->after('check_in_observation');
            }
            if (!Schema::hasColumn('time_logs', 'check_out_observation')) {
                $table->text('check_out_observation')->nullable()->after('check_out_lng');
            }
            if (!Schema::hasColumn('time_logs', 'check_out_registered_by')) {
                $table->bigInteger('check_out_registered_by')->nullable()->after('check_out_observation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_logs', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                'check_in_observation',
                'check_in_registered_by',
                'check_out_observation',
                'check_out_registered_by',
            ], fn($col) => Schema::hasColumn('time_logs', $col));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
