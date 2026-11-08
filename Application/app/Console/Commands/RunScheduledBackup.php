<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Support\Carbon;

class RunScheduledBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar respaldos programados según la configuración del sistema';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupService = new BackupService();
        $settings = BackupSetting::all();
        $now = Carbon::now();

        foreach ($settings as $setting) {
            if (!$setting->enabled) {
                continue;
            }

            if (!$this->shouldRunNow($setting, $now)) {
                continue;
            }

            $this->info("Ejecutando respaldo de {$setting->backup_type}...");

            if ($setting->backup_type === 'files') {
                $result = $backupService->createFilesBackup();
            } else {
                $result = $backupService->createDatabaseBackup();
            }

            if ($result['success']) {
                $this->info("✓ {$result['message']} ({$result['filename']})");

                // Actualizar última ejecución
                $setting->update(['last_run_at' => $now]);

                // Limpiar respaldos antiguos según retención
                if ($setting->retention_days > 0) {
                    $deleted = $backupService->cleanOldBackups($setting->backup_type, $setting->retention_days);
                    if ($deleted > 0) {
                        $this->info("  → Se eliminaron {$deleted} respaldo(s) antiguo(s).");
                    }
                }
            } else {
                $this->error("✗ {$result['message']}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Determinar si el respaldo debe ejecutarse ahora según la configuración.
     */
    protected function shouldRunNow(BackupSetting $setting, Carbon $now): bool
    {
        $configuredHour = (int) substr($setting->time, 0, 2);
        $configuredMinute = (int) substr($setting->time, 3, 2);

        // Validar que sea exactamente la hora y minuto configurados
        if ($now->hour !== $configuredHour || $now->minute !== $configuredMinute) {
            return false;
        }

        // Evitar que por algún reinicio del schedule se ejecute dos veces en el mismo minuto
        if ($setting->last_run_at && $setting->last_run_at->format('Y-m-d H:i') === $now->format('Y-m-d H:i')) {
            return false;
        }

        // Verificar día de la semana (para frecuencia semanal)
        if ($setting->frequency === 'weekly' && $setting->day_of_week !== null) {
            if ($now->dayOfWeek !== $setting->day_of_week) {
                return false;
            }
        }

        // Verificar día del mes (para frecuencia mensual)
        if ($setting->frequency === 'monthly' && $setting->day_of_month !== null) {
            if ($now->day !== $setting->day_of_month) {
                return false;
            }
        }

        return true;
    }
}
