<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 執行遷移 - 用戶管理模組
     * 包含：用戶、角色、權限管理
     */
    public function up(): void
    {
        $this->createRolesTable();
        $this->createUsersTable();
        $this->addUserForeignKeys();
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('roles');
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

    private function addUserForeignKeys(): void
    {
        // 檢查是否已存在外鍵約束
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'users'
            AND COLUMN_NAME = 'role_id'
            AND CONSTRAINT_NAME != 'PRIMARY'
        ");

        if (empty($foreignKeys)) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            });
        }
    }
};
