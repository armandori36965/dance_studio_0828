<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 建立角色
        $this->call([
            AdminRoleSeeder::class,
        ]);

        // 建立管理員用戶
        $this->call([
            AdminUserSeeder::class,
        ]);

        // 建立系統設定
        $this->call([
            SystemSettingSeeder::class,
        ]);

        // 建立校務活動
        $this->call([
            SchoolEventSeeder::class,
        ]);

        // 建立測試用戶
        User::factory()->create([
            'name' => '測試用戶',
            'email' => 'test@example.com',
        ]);
    }
}
