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
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('設備名稱');
                $table->text('description')->nullable()->comment('設備描述');
                $table->string('serial_number')->nullable()->comment('序號');
                $table->enum('status', ['available', 'in_use', 'maintenance', 'broken'])->default('available')->comment('設備狀態');
                $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
                $table->date('purchase_date')->nullable()->comment('購買日期');
                $table->decimal('purchase_price', 10, 2)->nullable()->comment('購買價格');
                $table->text('maintenance_notes')->nullable()->comment('維護記錄');
                $table->timestamps();
            });
        } else {
            Schema::table('equipment', function (Blueprint $table) {
                if (!Schema::hasColumn('equipment', 'campus_id')) {
                    $table->foreignId('campus_id')->nullable()->after('id')->constrained()->onDelete('cascade')->comment('所屬校區');
                }
            });
        }
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        // 不刪除表，以避免資料遺失
    }
};

