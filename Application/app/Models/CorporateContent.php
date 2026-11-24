<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'has_watermark',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_time_seconds' => 'integer',
            'is_mandatory' => 'boolean',
            'has_watermark' => 'boolean',
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
     * Convierte URLs de YouTube o Vimeo al formato embebible para iframe.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (empty($this->url)) {
            return null;
        }

        $url = trim($this->url);

        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $url;
    }

    /**
     * Resuelve el disco donde reside el archivo físico (privado primero, público por retrocompatibilidad).
     */
    public function resolveStorageDisk(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        if (Storage::disk('local')->exists($this->file_path)) {
            return 'local';
        }

        if (Storage::disk('public')->exists($this->file_path)) {
            return 'public';
        }

        return null;
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

    /**
     * Usuarios activos habilitados para ver este contenido:
     *  - Si está dirigido a un usuario específico: solo ese usuario.
     *  - Si tiene roles asignados: usuarios que posean alguno de esos roles.
     *  - Si no tiene roles ni usuario: todos los usuarios activos (contenido global).
     */
    public function recipientUsers(bool $includeDevelopers = true)
    {
        $query = User::query()->where('users.active', 1);

        if ($this->target_user_id) {
            $query->where('users.id', $this->target_user_id);
        } else {
            $roleIds = $this->roles()->pluck('roles.id')->all();
            if (!empty($roleIds)) {
                $query->whereHas('roles', function ($r) use ($roleIds) {
                    $r->whereIn('roles.id', $roleIds);
                });
            }
        }

        if (!$includeDevelopers) {
            $query->whereDoesntHave('roles', function ($r) {
                $r->where('roles.slug', 'developer');
            });
        }

        return $query;
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

    public function evidences()
    {
        return $this->hasMany(UserInductionEvidence::class, 'content_id');
    }
}
