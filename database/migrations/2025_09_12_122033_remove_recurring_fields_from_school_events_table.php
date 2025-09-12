<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_events', function (Blueprint $table) {
            // 移除重複事件相關欄位
            $table->dropColumn([
                'is_recurring',
                'recurrence_type',
                'recurrence_rule',
                'recurrence_end_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_events', function (Blueprint $table) {
            // 恢復重複事件相關欄位
            $table->boolean('is_recurring')->default(false)->after('status');
            $table->string('recurrence_type')->nullable()->after('is_recurring');
            $table->json('recurrence_rule')->nullable()->after('recurrence_type');
            $table->date('recurrence_end_date')->nullable()->after('recurrence_rule');
        });
    }
};
