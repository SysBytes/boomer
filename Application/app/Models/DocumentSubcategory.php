<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSubcategory extends Model
{
    use HasFactory;

    protected $table = 'document_subcategories';

    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function contents()
    {
        return $this->hasMany(CorporateContent::class, 'subcategory_id');
    }
}

