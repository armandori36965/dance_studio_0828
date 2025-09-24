<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 執行遷移 - 校務管理模組
     * 包含：校務活動管理
     * 依賴：校區管理（campus_id）
     */
    public function up(): void
    {
        $this->createSchoolEventsTable();
        $this->addEventIndexes();
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('school_events');
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

    private function addEventIndexes(): void
    {
        // 檢查索引是否已存在，避免重複創建
        $indexes = DB::select("SHOW INDEX FROM school_events");
        $existingIndexes = array_column($indexes, 'Key_name');

        if (!in_array('school_events_start_time_end_time_index', $existingIndexes)) {
            Schema::table('school_events', function (Blueprint $table) {
                $table->index(['start_time', 'end_time']);
            });
        }

        if (!in_array('school_events_campus_id_start_time_index', $existingIndexes)) {
            Schema::table('school_events', function (Blueprint $table) {
                $table->index(['campus_id', 'start_time']);
            });
        }

        if (!in_array('school_events_category_status_index', $existingIndexes)) {
            Schema::table('school_events', function (Blueprint $table) {
                $table->index(['category', 'status']);
            });
        }
    }
};
