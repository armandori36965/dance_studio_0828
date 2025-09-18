<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為用戶表新增班級欄位
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('class')
                ->nullable()
                ->after('cram_school_id')
                ->comment('班級');
        });
    }

    /**
     * 回滾遷移 - 移除班級欄位
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('class');
        });
    }
};
