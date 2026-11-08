<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = "location_id";
    protected $fillable = [
        'client_id',
        'location_name',
        'location_description',
        'location_city',
        'location_address',
        'location_reference',
        'location_lat',
        'location_lng',
        'active',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function works()
    {
        return $this->hasMany(Work::class, 'location_id', 'location_id');
    }
}
