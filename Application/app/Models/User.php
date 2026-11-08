<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'country',
        'active',
        'induction_status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function works()
    {
        return $this->belongsToMany(Work::class, 'work_user', 'user_id', 'work_id'); // Obras asignadas al técnico
    }

    public function supervisedWorks()
    {
        return $this->hasMany(Work::class, 'supervisor_id'); // Obras supervisadas por el usuario
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function requisitionComments()
    {
        return $this->hasMany(RequisitionComment::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return false;
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return $this->roles->whereIn('slug', $roles)->isNotEmpty();
        }
        return $this->hasRole($roles);
    }

    public function getRoleAttribute()
    {
        $role = $this->roles->first();
        return $role ? $role->slug : 'technician';
    }

    public function getPrimaryRoleNameAttribute()
    {
        if ($this->roles->isEmpty()) {
            return '';
        }

        // Priorizar roles operativos sobre los administrativos
        $operationalSlugs = ['staff', 'supervisor', 'technician', 'assistant'];

        foreach ($operationalSlugs as $slug) {
            $role = $this->roles->firstWhere('slug', $slug);
            if ($role) {
                return $role->name;
            }
        }

        // Si no tiene roles operativos, devolver el primero (ej. Administrador)
        return $this->roles->first()->name;
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function hasCountry($countryId)
    {
        return $this->countries->contains('id', $countryId);
    }

    public function contentProgress()
    {
        return $this->hasMany(UserContentProgress::class, 'user_id');
    }

    public function inductionEvidences()
    {
        return $this->hasMany(UserInductionEvidence::class, 'user_id');
    }

    public function targetedCorporateContents()
    {
        return $this->hasMany(CorporateContent::class, 'target_user_id');
    }

    public function latestInductionEvidence()
    {
        return $this->hasOne(UserInductionEvidence::class, 'user_id')->latestOfMany();
    }
}
