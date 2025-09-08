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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('學生ID');
            $table->foreignId('course_id')->constrained()->onDelete('cascade')->comment('課程ID');
            $table->date('date')->comment('出勤日期');
            $table->time('check_in_time')->nullable()->comment('簽到時間');
            $table->time('check_out_time')->nullable()->comment('簽退時間');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present')->comment('出勤狀態');
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
