<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    //
    protected $primaryKey = "work_id";
    protected $fillable = [
        'location_id',
        'work_name',
        'work_description',
        'supervisor_id',
        'start_date',
        'end_date',
        'work_status',
        'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    /**
     * Acceso al cliente a través de la locación.
     */
    public function client()
    {
        return $this->hasOneThrough(
            Client::class,
            Location::class,
            'location_id',   // FK en locations
            'client_id',     // FK en clients
            'location_id',   // LK en works
            'client_id'      // LK en locations
        );
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'work_id', 'work_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'work_id', 'work_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'work_user', 'work_id', 'user_id'); // Técnicos asignados
    }
}
