<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $table = 'overtimes';
    protected $primaryKey = 'overtime_id';

    protected $fillable = [
        'user_id',
        'work_id',
        'time_log_id',
        'date',
        'hours',
        'type', // 'daily_excess', 'sunday', 'holiday'
        'status', // 'pending', 'approved', 'rejected'
        'approved_by',
        'supervisor_id',
    ];

    /**
     * El usuario (técnico/asistente) al que pertenecen las horas extras.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * El proyecto (obra) en donde se hicieron las horas extras.
     */
    public function work()
    {
        return $this->belongsTo(Work::class, 'work_id', 'work_id');
    }

    /**
     * El registro de tiempo original.
     */
    public function timeLog()
    {
        return $this->belongsTo(TimeLog::class, 'time_log_id', 'time_log_id');
    }

    /**
     * El supervisor del proyecto al momento de generarse la hora extra.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Quien aprobó/rechazó (puede ser admin o supervisor).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeFormattedAttribute()
    {
        return match ($this->type) {
            'daily_excess' => 'Exceso Diario (>9h)',
            'sunday'       => 'Dominical',
            'holiday'      => 'Feriado',
            default        => ucfirst($this->type),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending'  => '<span class="badge bg-warning">Pendiente</span>',
            'approved' => '<span class="badge bg-success">Aprobada</span>',
            'rejected' => '<span class="badge bg-danger">Rechazada</span>',
            default    => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
