<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_code',
        'active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }
}
