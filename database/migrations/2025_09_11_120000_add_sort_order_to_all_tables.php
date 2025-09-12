<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為所有主要表添加排序欄位
     */
    public function up(): void
    {
        // 定義需要添加 sort_order 欄位的表和對應的 after 欄位
        $tables = [
            'courses' => 'is_active',
            'school_events' => 'extended_props',
            'users' => 'campus_id',
            'attendances' => 'notes',
            'equipment' => 'campus_id',
            'finances' => 'campus_id',
            'roles' => 'is_active',
            'system_settings' => 'type'
        ];

        foreach ($tables as $tableName => $afterColumn) {
            // 檢查表是否存在且尚未有 sort_order 欄位
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'sort_order')) {
                Schema::table($tableName, function (Blueprint $table) use ($afterColumn) {
                    $table->integer('sort_order')->default(0)->after($afterColumn)->comment('排序欄位');
                });
            }
        }
    }

    /**
     * 回滾遷移 - 移除所有表的排序欄位
     */
    public function down(): void
    {
        $tables = [
            'courses',
            'school_events',
            'users',
            'attendances',
            'equipment',
            'finances',
            'roles',
            'system_settings'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'sort_order')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('sort_order');
                });
            }
        }
    }
};

