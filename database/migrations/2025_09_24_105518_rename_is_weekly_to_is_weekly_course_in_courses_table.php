<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 將 is_weekly 欄位重命名為 is_weekly_course
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 重命名欄位：is_weekly -> is_weekly_course
            $table->renameColumn('is_weekly', 'is_weekly_course');
        });
    }

    /**
     * 回滾遷移 - 將 is_weekly_course 欄位重命名回 is_weekly
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 重命名欄位：is_weekly_course -> is_weekly
            $table->renameColumn('is_weekly_course', 'is_weekly');
        });
    }
};
