<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionComment extends Model
{
    use HasFactory;

    protected $table = 'requisition_comments';
    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'requisition_id',
        'user_id',
        'comment',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
