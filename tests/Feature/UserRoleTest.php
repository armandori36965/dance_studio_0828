<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試角色建立功能
     */
    public function test_can_create_role(): void
    {
        $role = Role::create([
            'name' => '測試角色',
            'description' => '這是一個測試角色',
            'permissions' => ['test.view', 'test.create'],
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => '測試角色',
            'description' => '這是一個測試角色',
        ]);

        $this->assertEquals(['test.view', 'test.create'], $role->permissions);
        $this->assertTrue($role->is_active);
    }

    /**
     * 測試用戶建立功能
     */
    public function test_can_create_user_with_role(): void
    {
        $role = Role::create([
            'name' => '測試角色',
            'description' => '這是一個測試角色',
            'permissions' => ['test.view'],
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => '測試用戶',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => '測試用戶',
            'email' => 'test@example.com',
            'role_id' => $role->id,
        ]);

        $this->assertInstanceOf(Role::class, $user->role);
        $this->assertEquals('測試角色', $user->role->name);
    }

    /**
     * 測試角色權限檢查功能
     */
    public function test_role_permission_checking(): void
    {
        $role = Role::create([
            'name' => '測試角色',
            'description' => '這是一個測試角色',
            'permissions' => ['test.view', 'test.create', 'test.edit'],
            'is_active' => true,
        ]);

        $this->assertTrue($role->hasPermission('test.view'));
        $this->assertTrue($role->hasPermission('test.create'));
        $this->assertFalse($role->hasPermission('test.delete'));

        $this->assertTrue($role->hasAnyPermission(['test.view', 'test.delete']));
        $this->assertTrue($role->hasAllPermissions(['test.view', 'test.create']));
        $this->assertFalse($role->hasAllPermissions(['test.view', 'test.delete']));
    }

    /**
     * 測試用戶權限檢查功能
     */
    public function test_user_permission_checking(): void
    {
        $role = Role::create([
            'name' => '測試角色',
            'description' => '這是一個測試角色',
            'permissions' => ['test.view', 'test.create'],
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => '測試用戶',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->assertTrue($user->hasPermission('test.view'));
        $this->assertTrue($user->hasPermission('test.create'));
        $this->assertFalse($user->hasPermission('test.delete'));

        $this->assertTrue($user->hasRole('測試角色'));
        $this->assertFalse($user->hasRole('其他角色'));
    }
}
