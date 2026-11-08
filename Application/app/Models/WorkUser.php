<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkUser extends Model
{
    protected $table = 'work_user';

    protected $fillable = [
        'work_id',
        'user_id'
    ];
}
