<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'backup_type',
        'enabled',
        'frequency',
        'time',
        'day_of_week',
        'day_of_month',
        'retention_days',
        'last_run_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'day_of_week' => 'integer',
            'day_of_month' => 'integer',
            'retention_days' => 'integer',
            'last_run_at' => 'datetime',
        ];
    }

    /**
     * Obtener la configuración para respaldo de archivos.
     */
    public static function forFiles()
    {
        return static::where('backup_type', 'files')->first();
    }

    /**
     * Obtener la configuración para respaldo de base de datos.
     */
    public static function forDatabase()
    {
        return static::where('backup_type', 'database')->first();
    }

    /**
     * Nombres de los días de la semana en español.
     */
    public static function daysOfWeek(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }

    /**
     * Nombres de frecuencias en español.
     */
    public static function frequencies(): array
    {
        return [
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
        ];
    }
}
