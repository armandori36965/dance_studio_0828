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
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('財務項目標題');
            $table->text('description')->nullable()->comment('財務項目描述');
            $table->enum('type', ['income', 'expense'])->comment('收入/支出');
            $table->decimal('amount', 12, 2)->comment('金額');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('相關校區');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null')->comment('相關課程');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('相關用戶');
            $table->date('transaction_date')->comment('交易日期');
            $table->string('payment_method')->nullable()->comment('付款方式');
            $table->string('reference_number')->nullable()->comment('參考號碼');
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
