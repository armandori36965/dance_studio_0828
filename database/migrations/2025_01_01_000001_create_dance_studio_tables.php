<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 舞蹈工作室完整資料庫結構
     */
    public function up(): void
    {
        // 1. 基礎系統表
        $this->createCacheTable();
        $this->createJobsTable();
        $this->createUsersTable();
        $this->createRolesTable();
        $this->createSystemSettingsTable();
        $this->createAuditLogsTable();
        $this->createNotificationsTable();

        // 2. 核心業務表
        $this->createCampusesTable();
        $this->createEquipmentTable();
        $this->createSchoolEventsTable();
        $this->createCoursesTable();
        $this->createFinancesTable();
        $this->createAttendancesTable();

        // 3. 關聯表
        $this->createUserCourseTable();
        $this->createCourseSessionsTable();

        // 4. 添加外鍵約束
        $this->addForeignKeys();

        // 5. 添加索引
        $this->addIndexes();
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        // 按相反順序刪除表
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('user_course');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('finances');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('school_events');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
    }

    private function createCacheTable(): void
    {
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }
    }

    private function createJobsTable(): void
    {
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }

    private function createUsersTable(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->unsignedBigInteger('role_id')->nullable();
                $table->unsignedBigInteger('cram_school_id')->nullable();
                $table->string('class')->nullable()->comment('班級');
                $table->string('emergency_contact_name')->nullable()->comment('緊急聯絡人姓名');
                $table->string('emergency_contact_phone')->nullable()->comment('緊急聯絡人電話');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    private function createRolesTable(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->json('permissions')->nullable()->comment('角色權限');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->comment('是否啟用');
                $table->boolean('requires_campus')->default(false)->comment('是否需要校區');
                $table->timestamps();
            });
        }
    }

    private function createSystemSettingsTable(): void
    {
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value');
                $table->text('description')->nullable();
                $table->string('type')->default('string');
                $table->timestamps();
            });
        }
    }

    private function createAuditLogsTable(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('user_type')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('event');
                $table->morphs('auditable');
                $table->text('old_values')->nullable();
                $table->text('new_values')->nullable();
                $table->text('url')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('tags')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'user_type']);
            });
        }
    }

    private function createNotificationsTable(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function createCampusesTable(): void
    {
        if (!Schema::hasTable('campuses')) {
            Schema::create('campuses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('校區名稱');
                $table->string('address')->nullable()->comment('地址');
                $table->string('phone')->nullable()->comment('聯絡電話');
                $table->string('email')->nullable()->comment('聯絡信箱');
                $table->text('description')->nullable()->comment('校區描述');
                $table->boolean('is_active')->default(true)->comment('是否啟用');
                $table->string('type')->default('school')->comment('校區類型：school=學校，cram_school=補習班');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
    }

    private function createEquipmentTable(): void
    {
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('設備名稱');
                $table->text('description')->nullable()->comment('設備描述');
                $table->string('status')->default('available')->comment('設備狀態');
                $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
    }

    private function createSchoolEventsTable(): void
    {
        if (!Schema::hasTable('school_events')) {
            Schema::create('school_events', function (Blueprint $table) {
                $table->id();
                $table->string('title')->comment('事件標題');
                $table->text('description')->nullable()->comment('事件描述');
                $table->datetime('start_time')->comment('開始時間');
                $table->datetime('end_time')->nullable()->comment('結束時間');
                $table->string('location')->nullable()->comment('地點');
                $table->string('category', 50)->nullable()->comment('事件類型');
                $table->string('status', 20)->default('active')->comment('狀態');
                $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null')->comment('所屬校區');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('創建者');
                $table->json('extended_props')->nullable()->comment('擴展屬性');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
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

    private function createFinancesTable(): void
    {
        if (!Schema::hasTable('finances')) {
            Schema::create('finances', function (Blueprint $table) {
                $table->id();
                $table->string('type')->comment('財務類型');
                $table->decimal('amount', 10, 2)->comment('金額');
                $table->text('description')->nullable()->comment('描述');
                $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('相關用戶');
                $table->date('transaction_date')->comment('交易日期');
                $table->integer('sort_order')->default(0)->comment('排序欄位');
                $table->timestamps();
            });
        }
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

                $table->index(['course_id', 'session_number']);
                $table->index(['start_time', 'end_time']);
            });
        }
    }

    private function addForeignKeys(): void
    {
        // 添加外鍵約束
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('cram_school_id')->references('id')->on('campuses')->onDelete('set null');
        });
    }

    private function addIndexes(): void
    {
        // 性能優化索引
        Schema::table('school_events', function (Blueprint $table) {
            $table->index(['start_time', 'end_time']);
            $table->index(['campus_id', 'start_time']);
            $table->index(['category', 'status']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['campus_id', 'is_active']);
            $table->index(['start_time', 'end_time']);
            $table->index(['teacher_id', 'is_active']);
        });

        Schema::table('course_sessions', function (Blueprint $table) {
            $table->index(['start_time', 'end_time']);
            $table->index(['status', 'start_time']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['user_id', 'attendance_date']);
            $table->index(['course_id', 'attendance_date']);
        });
    }
};
