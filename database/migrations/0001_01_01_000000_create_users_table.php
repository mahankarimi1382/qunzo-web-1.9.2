<?php

use App\Enums\UserType;
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
            $table->enum('role', ['User', 'Agent', 'Merchant'])->default(UserType::User);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->unique();
            $table->string('account_number')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('kyc')->default(0)->nullable();
            $table->tinyInteger('phone_verified')->default(0)->nullable();
            $table->string('otp')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->decimal('balance', 28, 8)->default(0.00)->nullable();
            $table->string('country')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->text('address')->nullable();
            $table->text('close_reason')->nullable();
            $table->integer('ref_id')->nullable();
            $table->text('referral_code')->nullable();
            $table->text('google2fa_secret')->nullable();
            $table->tinyInteger('two_fa')->default(0)->nullable();
            $table->tinyInteger('withdraw_status')->default(1)->nullable();
            $table->tinyInteger('otp_status')->default(1)->nullable();
            $table->tinyInteger('deposit_status')->default(1)->nullable();
            $table->tinyInteger('transfer_status')->default(1)->nullable();
            $table->tinyInteger('referral_status')->default(1)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email');
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
