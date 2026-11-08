<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContentProgress extends Model
{
    use HasFactory;

    protected $table = 'user_content_progress';

    protected $fillable = [
        'user_id',
        'content_id',
        'view_time_seconds',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'view_time_seconds' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function content()
    {
        return $this->belongsTo(CorporateContent::class, 'content_id');
    }
}

