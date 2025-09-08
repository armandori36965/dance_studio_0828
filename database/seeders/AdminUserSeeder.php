<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 取得管理員角色
        $adminRole = Role::where('name', '管理員')->first();

        if (!$adminRole) {
            $this->command->error('管理員角色不存在，請先執行 AdminRoleSeeder！');
            return;
        }

        // 建立管理員帳號
        User::create([
            'name' => '管理員',
            'email' => 'admin@dance.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'role_id' => $adminRole->id,
        ]);

        $this->command->info('管理員帳號已建立完成！');
        $this->command->info('Email: admin@dance.com');
        $this->command->info('Password: password123');
    }
}
