<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telepon',
        'nik',
        'jenis_kelamin',
        'alamat',
        'is_filled_data_register',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $with = ['role'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole(RoleEnum::ADMIN->deskripsi());
    }

    public function getIsPublicAttribute()
    {
        return $this->hasRole(RoleEnum::PUBLIC->deskripsi());
    }

    public function getIsVerifikatorAttribute()
    {
        return $this->hasRole(RoleEnum::VERIFIKATOR->deskripsi());
    }

    public function getIsInputerAttribute()
    {
        return $this->hasRole(RoleEnum::INPUTER->deskripsi());
    }

    public function getIsAuditorAttribute()
    {
        return $this->hasRole(RoleEnum::AUDITOR->deskripsi());
    }

    public function getRoleBadgeAttribute()
    {
        if ($this->is_admin) {
            $badge = '<span class="badge bg-primary">Admin</span>';
        } elseif ($this->is_verifikator) {
            $badge = '<span class="badge bg-warning">Verifikator</span>';
        } elseif ($this->is_public) {
            $badge = '<span class="badge bg-success">Public</span>';
        } elseif ($this->is_auditor) {
            $badge = '<span class="badge bg-info">Auditor</span>';
        } elseif ($this->is_inputer) {
            $badge = '<span class="badge bg-dark">Inputer</span>';
        } else {
            $badge = '<span class="badge bg-secondary">Undefined</span>';
        }
        return $badge;
    }

    public function hasRole($role)
    {
        return $this->role?->nama === $role;
    }
    public function getPhotoProfileAttribute()
    {
        return 'https://ui-avatars.com/api/?name=' . $this->email . '&background=random&color=fff';
    }
}
