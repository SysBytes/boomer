<?php

namespace App\Services;

use App\Models\TimeLog;
use App\Models\Overtime;
use App\Models\Holiday;
use Carbon\Carbon;

class OvertimeCalculator
{
    /**
     * Calcula y registra las horas extras (si aplican) para un TimeLog dado.
     */
    public static function calculateForTimeLog(TimeLog $timeLog)
    {
        if (!$timeLog->check_out_time || !$timeLog->hours_worked) {
            return;
        }

        $checkInDate = Carbon::parse($timeLog->check_in_time);
        $hoursWorked = (float) $timeLog->hours_worked;

        // Obtener estado previo antes de borrar para mantener los status (e.g. approved)
        $existingOvertimes = Overtime::where('time_log_id', $timeLog->time_log_id)->get()->keyBy('type');

        // Limpiar registros previos por si es un recálculo
        Overtime::where('time_log_id', $timeLog->time_log_id)->delete();

        // 1. Determinar si es Domingo
        if ($checkInDate->isSunday()) {
            self::createOvertime($timeLog, $checkInDate, $hoursWorked, 'sunday', $existingOvertimes);
            return; // Si es domingo, TODO el tiempo es extra
        }

        // 2. Determinar si es Feriado
        // Obtener el país del proyecto
        $timeLog->loadMissing('work.client');
        $countryId = $timeLog->work->client->country_id ?? null;

        $isHoliday = false;
        if ($countryId) {
            // Buscar feriados para este país
            $holidays = Holiday::where('country_id', $countryId)->where('active', 1)->get();
            foreach ($holidays as $holiday) {
                $holDate = Carbon::parse($holiday->holiday_date);
                
                if ($holiday->is_annual) {
                    if ($holDate->month == $checkInDate->month && $holDate->day == $checkInDate->day) {
                        $isHoliday = true;
                        break;
                    }
                } else {
                    if ($holDate->isSameDay($checkInDate)) {
                        $isHoliday = true;
                        break;
                    }
                }
            }
        }

        if ($isHoliday) {
            self::createOvertime($timeLog, $checkInDate, $hoursWorked, 'holiday', $existingOvertimes);
            return; // Si es feriado, TODO el tiempo es extra
        }

        // 3. Exceso Diario (> 9 horas)
        if ($hoursWorked > 9.00) {
            $extraHours = $hoursWorked - 9.00;
            self::createOvertime($timeLog, $checkInDate, $extraHours, 'daily_excess', $existingOvertimes);
        }
    }

    private static function createOvertime(TimeLog $timeLog, Carbon $date, float $hours, string $type, $existingOvertimes = null)
    {
        $status = 'pending';
        $approvedBy = null;

        if ($existingOvertimes && $existingOvertimes->has($type)) {
            $existing = $existingOvertimes->get($type);
            if ($existing->status === 'approved') {
                $status = $existing->status;
                $approvedBy = $existing->approved_by;
            }
        }

        Overtime::create([
            'user_id'       => $timeLog->user_id,
            'work_id'       => $timeLog->work_id,
            'time_log_id'   => $timeLog->time_log_id,
            'date'          => $date->toDateString(),
            'hours'         => round($hours, 2),
            'type'          => $type,
            'status'        => $status,
            'approved_by'   => $approvedBy,
            'supervisor_id' => $timeLog->work->supervisor_id ?? 0, // Cargar supervisor del work
        ]);
    }
}
