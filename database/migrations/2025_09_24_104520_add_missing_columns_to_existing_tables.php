<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為現有表添加缺少的欄位
     */
    public function up(): void
    {
        // 為 roles 表添加 requires_campus 欄位
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'requires_campus')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('requires_campus')->default(false)->comment('是否需要校區')->after('is_active');
            });
        }

        // 為 campuses 表添加 type 欄位
        if (Schema::hasTable('campuses') && !Schema::hasColumn('campuses', 'type')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->string('type')->default('school')->comment('校區類型：school=學校，cram_school=補習班')->after('is_active');
            });
        }
    }

    /**
     * 回滾遷移 - 移除添加的欄位
     */
    public function down(): void
    {
        // 移除 roles 表的 requires_campus 欄位
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'requires_campus')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('requires_campus');
            });
        }

        // 移除 campuses 表的 type 欄位
        if (Schema::hasTable('campuses') && Schema::hasColumn('campuses', 'type')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
