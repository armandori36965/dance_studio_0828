<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 出勤管理模組
     * 包含：出勤記錄、用戶課程關聯
     * 依賴：課程管理（course_id）、用戶管理（user_id）
     */
    public function up(): void
    {
        $this->createAttendancesTable();
        $this->createUserCourseTable();
        $this->addAttendanceIndexes();
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course');
        Schema::dropIfExists('attendances');
    }

    private function createAttendancesTable(): void
    {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('學員');
                $table->foreignId('course_id')->constrained()->onDelete('cascade')->comment('課程');
                $table->datetime('attendance_date')->comment('出席日期');
                $table->string('status')->default('present')->comment('出席狀態');
                $table->text('notes')->nullable()->comment('備註');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
    }

    private function createUserCourseTable(): void
    {
        if (!Schema::hasTable('user_course')) {
            Schema::create('user_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->datetime('enrolled_at')->comment('報名時間');
                $table->string('status')->default('active')->comment('狀態');
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    private function addAttendanceIndexes(): void
    {
        // 出勤性能優化索引
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['user_id', 'attendance_date']);
            $table->index(['course_id', 'attendance_date']);
            $table->index(['status', 'attendance_date']);
        });
    }
};
