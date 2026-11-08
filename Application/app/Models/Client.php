<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $primaryKey = "client_id";
    protected $fillable = [
        'client_name',
        'client_email',
        'client_phone',
        'country_id',
        'active',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class, 'client_id', 'client_id');
    }

    public function works()
    {
        return $this->hasManyThrough(
            Work::class,
            Location::class,
            'client_id',    // FK en locations
            'location_id',  // FK en works
            'client_id',    // LK en clients
            'location_id'   // LK en locations
        );
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
