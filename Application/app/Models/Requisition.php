<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    //
    protected $primaryKey = "requisition_id";
    protected $fillable = [
        'user_id',
        'work_id',
        'status',
        'reject_reason',
        'approved_by',
        'date_approved',
        'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class, 'work_id', 'work_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_requisition', 'requisition_id', 'material_id')->withPivot('quantity')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(RequisitionComment::class, 'requisition_id');
    }
}
