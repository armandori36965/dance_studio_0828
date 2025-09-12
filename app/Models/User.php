<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasAnyPermission(array $permissions)
 * @method bool hasAllPermissions(array $permissions)
 * @method bool hasRole(string $roleName)
 */
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
        'password',
        'role_id',
        'campus_id',
        'sort_order'
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

    /**
     * 用戶角色關聯
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * 檢查用戶是否有特定權限
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    /**
     * 檢查用戶是否有任一權限
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->role?->hasAnyPermission($permissions) ?? false;
    }

    /**
     * 檢查用戶是否有所有權限
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return $this->role?->hasAllPermissions($permissions) ?? false;
    }

    /**
     * 檢查用戶是否有角色
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    /**
     * 與校區的關係
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
