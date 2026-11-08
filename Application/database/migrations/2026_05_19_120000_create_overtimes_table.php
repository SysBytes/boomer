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
        if (!Schema::hasTable('overtimes')) {
            Schema::create('overtimes', function (Blueprint $table) {
                $table->increments('overtime_id');
                $table->bigInteger('user_id');
                $table->bigInteger('work_id');
                $table->integer('time_log_id')->unsigned(); // Relation to time_logs.time_log_id
                $table->date('date');
                $table->decimal('hours', 8, 2);
                $table->enum('type', ['daily_excess', 'sunday', 'holiday']);
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->bigInteger('approved_by')->nullable();
                $table->bigInteger('supervisor_id'); // Snapshot of who should approve
                $table->timestamps();

                // Optionally add foreign keys if needed
                // $table->foreign('time_log_id')->references('time_log_id')->on('time_logs')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
