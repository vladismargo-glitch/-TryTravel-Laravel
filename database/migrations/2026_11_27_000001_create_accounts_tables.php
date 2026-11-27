<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->smallIncrements('role_id');
            $table->string('name', 60);
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->bigIncrements('user_id');
            $table->unsignedSmallInteger('role_id');
            $table->string('last_name', 100);
            $table->string('first_name', 100);
            $table->string('email', 254)->unique('uq_users_email');
            $table->string('phone', 32);
            $table->string('password_hash');
            $table->dateTime('consent_at');
            $table->index('role_id', 'ix_users_role');
            $table->foreign('role_id', 'fk_users_role')->references('role_id')->on('roles');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->bigIncrements('reset_id');
            $table->unsignedBigInteger('user_id');
            $table->char('token_hash', 64)->charset('ascii')->collation('ascii_bin');
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->index('token_hash', 'ix_reset_token_hash');
            $table->index('user_id', 'ix_reset_user');
            $table->foreign('user_id', 'fk_reset_user')->references('user_id')->on('users')->cascadeOnDelete();
        });

        DB::table('roles')->insert([
            ['role_id' => 1, 'name' => 'Пользователь'],
            ['role_id' => 2, 'name' => 'Администратор'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
