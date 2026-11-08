<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInductionEvidence extends Model
{
    use HasFactory;

    protected $table = 'user_induction_evidences';

    protected $fillable = [
        'user_id',
        'evidence_photo_path',
        'ip_address',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

