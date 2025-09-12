<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'name',
        'permissions',
        'description',
        'is_active',
        'sort_order'
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * 角色下的用戶關聯
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * 檢查角色是否有特定權限
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * 檢查角色是否有任一權限
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return !empty(array_intersect($permissions, $this->permissions ?? []));
    }

    /**
     * 檢查角色是否有所有權限
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return empty(array_diff($permissions, $this->permissions ?? []));
    }
}
