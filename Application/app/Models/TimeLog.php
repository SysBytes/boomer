<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    //
    protected $primaryKey = "time_log_id";
    protected $fillable = [
        'user_id',
        'work_id',
        'check_in_time',
        'check_in_photo',
        'check_in_lat',
        'check_in_lng',
        'check_in_observation',
        'check_in_registered_by',
        'check_out_time',
        'check_out_photo',
        'check_out_lat',
        'check_out_lng',
        'check_out_observation',
        'check_out_registered_by',
        'hours_worked',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function user()
    {
        // users.id es la PK estándar, Laravel la resuelve solo
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function work()
    {
        // works usa work_id como PK, hay que indicarlo explicitamente
        return $this->belongsTo(Work::class, 'work_id', 'work_id');
    }

    /**
     * Devuelve las horas trabajadas en formato legible "Xh Ym".
     */
    public function getHoursWorkedFormattedAttribute(): string
    {
        if (is_null($this->hours_worked)) {
            return '—';
        }
        $hours   = (int) floor($this->hours_worked);
        $minutes = (int) round(($this->hours_worked - $hours) * 60);
        return "{$hours}h {$minutes}m";
    }

    public function checkInAdmin()
    {
        return $this->belongsTo(User::class, 'check_in_registered_by', 'id');
    }

    public function checkOutAdmin()
    {
        return $this->belongsTo(User::class, 'check_out_registered_by', 'id');
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'time_log_id', 'time_log_id');
    }

}
