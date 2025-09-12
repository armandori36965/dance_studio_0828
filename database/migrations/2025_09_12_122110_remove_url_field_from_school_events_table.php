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
            // 移除事件連結欄位
            $table->dropColumn('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_events', function (Blueprint $table) {
            // 恢復事件連結欄位
            $table->string('url')->nullable()->after('created_by');
        });
    }
};
