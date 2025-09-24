<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 執行遷移 - 課程管理模組
     * 包含：課程、課程堂次
     * 依賴：校區管理（campus_id）
     */
    public function up(): void
    {
        $this->createCoursesTable();
        $this->createCourseSessionsTable();
        $this->addCourseIndexes();
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('courses');
    }

    private function createCoursesTable(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('課程名稱');
                $table->text('description')->nullable()->comment('課程描述');
                $table->decimal('price', 10, 2)->default(0)->comment('課程價格');
                $table->enum('pricing_type', ['per_session', 'per_student'])->nullable()->default('per_session')->comment('計費類型');
                $table->datetime('start_time')->nullable()->comment('課程開始時間');
                $table->datetime('end_time')->nullable()->comment('課程結束時間');
                $table->integer('student_count')->default(0)->comment('目前報名學員數');
                $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null')->comment('授課老師');
                $table->string('level')->default('beginner')->comment('課程等級');
                $table->boolean('is_weekly')->default(false)->comment('是否為週期性課程');
                $table->integer('weekly_interval')->default(1)->comment('週期間隔');
                $table->json('weekly_days')->nullable()->comment('週期天數');
                $table->boolean('is_active')->default(true)->comment('是否啟用');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
    }

    private function createCourseSessionsTable(): void
    {
        if (!Schema::hasTable('course_sessions')) {
            Schema::create('course_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->integer('session_number')->comment('堂數');
                $table->datetime('start_time')->comment('開始時間');
                $table->datetime('end_time')->comment('結束時間');
                $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->comment('狀態');
                $table->text('notes')->nullable()->comment('備註');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
    }

    private function addCourseIndexes(): void
    {
        // 檢查索引是否已存在，避免重複創建
        $coursesIndexes = DB::select("SHOW INDEX FROM courses");
        $existingCoursesIndexes = array_column($coursesIndexes, 'Key_name');

        if (!in_array('courses_campus_id_is_active_index', $existingCoursesIndexes)) {
            Schema::table('courses', function (Blueprint $table) {
                $table->index(['campus_id', 'is_active']);
            });
        }

        if (!in_array('courses_start_time_end_time_index', $existingCoursesIndexes)) {
            Schema::table('courses', function (Blueprint $table) {
                $table->index(['start_time', 'end_time']);
            });
        }

        if (!in_array('courses_teacher_id_is_active_index', $existingCoursesIndexes)) {
            Schema::table('courses', function (Blueprint $table) {
                $table->index(['teacher_id', 'is_active']);
            });
        }

        $sessionsIndexes = DB::select("SHOW INDEX FROM course_sessions");
        $existingSessionsIndexes = array_column($sessionsIndexes, 'Key_name');

        if (!in_array('course_sessions_start_time_end_time_index', $existingSessionsIndexes)) {
            Schema::table('course_sessions', function (Blueprint $table) {
                $table->index(['start_time', 'end_time']);
            });
        }

        if (!in_array('course_sessions_status_start_time_index', $existingSessionsIndexes)) {
            Schema::table('course_sessions', function (Blueprint $table) {
                $table->index(['status', 'start_time']);
            });
        }
    }
};
