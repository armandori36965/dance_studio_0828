<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移
     */
    public function up(): void
    {
        // 為校務活動表添加索引
        Schema::table('school_events', function (Blueprint $table) {
            $table->index(['status', 'start_time'], 'idx_school_events_status_start_time');
            $table->index(['campus_id', 'start_time'], 'idx_school_events_campus_start_time');
        });

        // 為課程表添加索引
        Schema::table('courses', function (Blueprint $table) {
            $table->index(['is_active', 'start_time'], 'idx_courses_active_start_time');
            $table->index(['campus_id', 'start_time'], 'idx_courses_campus_start_time');
        });

        // 為校區表添加索引
        Schema::table('campuses', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'idx_campuses_active_sort');
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::table('school_events', function (Blueprint $table) {
            $table->dropIndex('idx_school_events_status_start_time');
            $table->dropIndex('idx_school_events_campus_start_time');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('idx_courses_active_start_time');
            $table->dropIndex('idx_courses_campus_start_time');
        });

        Schema::table('campuses', function (Blueprint $table) {
            $table->dropIndex('idx_campuses_active_sort');
        });
    }
};
