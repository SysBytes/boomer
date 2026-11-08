<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $primaryKey = "holiday_id";
    protected $fillable = [
        'holiday_name',
        'holiday_date',
        'country_id',
        'is_annual',
        'active',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
