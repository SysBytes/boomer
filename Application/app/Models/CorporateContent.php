<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateContent extends Model
{
    use HasFactory;

    protected $table = 'corporate_contents';

    protected $fillable = [
        'subcategory_id',
        'title',
        'type',
        'file_path',
        'url',
        'required_time_seconds',
        'target_role_id',
        'target_user_id',
        'is_mandatory',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_time_seconds' => 'integer',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Indica si el contenido es un enlace externo (YouTube/Vimeo).
     */
    public function getIsExternalAttribute(): bool
    {
        return !empty($this->url);
    }

    /**
     * Contenidos activos visibles para un usuario:
     *  - Asignados específicamente a él (target_user_id), o
     *  - Sin usuario específico y: globales (sin roles asignados) o con
     *    alguno de los roles del usuario en la tabla pivote corporate_content_role.
     */
    public function scopeVisibleTo($query, User $user)
    {
        $roleIds = $user->roles()->pluck('roles.id')->all();

        return $query->where('corporate_contents.is_active', true)
            ->where(function ($q) use ($user, $roleIds) {
                $q->where('corporate_contents.target_user_id', $user->id)
                    ->orWhere(function ($q2) use ($roleIds) {
                        $q2->whereNull('corporate_contents.target_user_id')
                            ->where(function ($q3) use ($roleIds) {
                                $q3->whereDoesntHave('roles');
                                if (!empty($roleIds)) {
                                    $q3->orWhereHas('roles', function ($r) use ($roleIds) {
                                        $r->whereIn('roles.id', $roleIds);
                                    });
                                }
                            });
                    });
            });
    }

    public function subcategory()
    {
        return $this->belongsTo(DocumentSubcategory::class, 'subcategory_id');
    }

    public function targetRole()
    {
        return $this->belongsTo(Role::class, 'target_role_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'corporate_content_role', 'corporate_content_id', 'role_id')
            ->withTimestamps();
    }

    public function userProgress()
    {
        return $this->hasMany(UserContentProgress::class, 'content_id');
    }
}
