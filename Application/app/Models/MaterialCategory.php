<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    //
    protected $primaryKey = "material_category_id";
    protected $fillable = [
        'material_category_name',
        'material_category_description',
        'active',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'material_category_id', 'material_category_id');
    }
}
