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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('image')->default('profile_pic/avatar.png');
            $table->float('usdt')->default(0);
            $table->float('btc')->default(0);
            $table->float('eth')->default(0);
            $table->float('bnb')->default(0);
            $table->string('code_5');
            $table->string('code_4');
            $table->string('code_3');
            $table->string('code_2');
            $table->string('code_1');
            $table->unsignedTinyInteger('no_of_codes')->default(0)->comment('code_5 means 5 codes are needed to do transfer ...');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('status')->index()->default(1)->comment('0=inactive, 1=active');
            $table->unsignedTinyInteger('privilege')->default(6)->comment('0-5=admins, 6=users');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
