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
        if (!Schema::hasTable('backup_settings')) {
            Schema::create('backup_settings', function (Blueprint $table) {
                $table->id();
                $table->enum('backup_type', ['files', 'database']);
                $table->boolean('enabled')->default(false);
                $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('weekly');
                $table->string('time', 5)->default('02:00');
                $table->tinyInteger('day_of_week')->nullable(); // 0=Domingo..6=Sábado
                $table->tinyInteger('day_of_month')->nullable(); // 1-28
                $table->unsignedInteger('retention_days')->default(30); // Días máximos a conservar
                $table->timestamp('last_run_at')->nullable();
                $table->timestamps();
            });

            // Insertar configuración inicial para ambos tipos
            DB::table('backup_settings')->insert([
                [
                    'backup_type' => 'files',
                    'enabled' => false,
                    'frequency' => 'weekly',
                    'time' => '02:00',
                    'day_of_week' => 0,
                    'day_of_month' => 1,
                    'retention_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'backup_type' => 'database',
                    'enabled' => false,
                    'frequency' => 'weekly',
                    'time' => '02:00',
                    'day_of_week' => 0,
                    'day_of_month' => 1,
                    'retention_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
