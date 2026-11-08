<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Tareas Programadas ──
// Notificar horas extras pendientes todos los miércoles a las 8:00 AM
Schedule::command('overtimes:notify-pending')->weeklyOn(3, '08:00');

// Verificar y ejecutar respaldos programados cada minuto
Schedule::command('backup:run-scheduled')->everyMinute();

// Verificar check-outs pendientes cada hora (solo L-V, 8am - 7pm)
Schedule::command('app:check-missing-checkouts')
    ->hourly()
    ->weekdays()
    ->between('07:00', '21:05');
