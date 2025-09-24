<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // 建立國定假日事件
        $this->call([
            NationalHolidaySeeder::class,
        ]);

        // 建立國安國小校務事件
        $this->call([
            GuoanSchoolEventSeeder::class,
        ]);

        // 建立測試用戶（如果不存在則建立）
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '測試用戶',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => Role::where('name', '學生')->first()?->id ?? 1,
            ]
        );
    }
}
