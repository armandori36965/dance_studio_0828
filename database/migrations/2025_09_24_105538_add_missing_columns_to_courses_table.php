<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為 courses 表添加缺少的欄位
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 添加 total_sessions 欄位（總課程節數）
            if (!Schema::hasColumn('courses', 'total_sessions')) {
                $table->integer('total_sessions')->nullable()->comment('總課程節數')->after('is_weekly_course');
            }

            // 添加 weekdays 欄位（上課星期）
            if (!Schema::hasColumn('courses', 'weekdays')) {
                $table->json('weekdays')->nullable()->comment('上課星期（1-7）')->after('total_sessions');
            }

            // 添加 avoid_school_events 欄位（避免校務事件）
            if (!Schema::hasColumn('courses', 'avoid_school_events')) {
                $table->boolean('avoid_school_events')->default(true)->comment('是否避免校務事件')->after('weekdays');
            }

            // 添加 avoid_event_types 欄位（避免的事件類型）
            if (!Schema::hasColumn('courses', 'avoid_event_types')) {
                $table->json('avoid_event_types')->nullable()->comment('避免的事件類型')->after('avoid_school_events');
            }
        });
    }

    /**
     * 回滾遷移 - 移除添加的欄位
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 移除添加的欄位
            $table->dropColumn([
                'total_sessions',
                'weekdays',
                'avoid_school_events',
                'avoid_event_types'
            ]);
        });
    }
};
