<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function corporateContents()
    {
        return $this->belongsToMany(CorporateContent::class, 'corporate_content_role', 'role_id', 'corporate_content_id')
            ->withTimestamps();
    }
}
