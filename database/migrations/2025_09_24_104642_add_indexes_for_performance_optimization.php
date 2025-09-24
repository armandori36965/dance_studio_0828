<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 添加效能優化索引
     */
    public function up(): void
    {
        // 為 campuses 表添加複合索引，優化 is_active + type 查詢
        if (Schema::hasTable('campuses')) {
            Schema::table('campuses', function (Blueprint $table) {
                // 複合索引：is_active + type + sort_order，用於校區和補習班查詢
                $table->index(['is_active', 'type', 'sort_order'], 'idx_campuses_active_type_sort');

                // 單獨索引：type 欄位，用於類型篩選
                $table->index('type', 'idx_campuses_type');
            });
        }

        // 為 roles 表添加索引
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                // 複合索引：is_active + sort_order，用於角色選擇
                $table->index(['is_active', 'sort_order'], 'idx_roles_active_sort');
            });
        }

        // 為 courses 表添加索引（如果存在）
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                // 複合索引：campus_id + is_active，用於課程查詢
                $table->index(['campus_id', 'is_active'], 'idx_courses_campus_active');
            });
        }
    }

    /**
     * 回滾遷移 - 移除效能優化索引
     */
    public function down(): void
    {
        // 移除 campuses 表的索引
        if (Schema::hasTable('campuses')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->dropIndex('idx_campuses_active_type_sort');
                $table->dropIndex('idx_campuses_type');
            });
        }

        // 移除 roles 表的索引
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropIndex('idx_roles_active_sort');
            });
        }

        // 移除 courses 表的索引
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropIndex('idx_courses_campus_active');
            });
        }
    }
};
