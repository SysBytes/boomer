<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    //
    protected $primaryKey = "material_id";
    protected $fillable = [
        'material_category_id',
        'material_name',
        'material_description',
        'material_unit',
        'stock',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function requisitions()
    {
        return $this->belongsToMany(Requisition::class, 'material_requisition', 'material_id', 'requisition_id')->withPivot('quantity')->withTimestamps();
    }
}
