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
        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('type')->default('manual')->nullable();
            $table->string('gateway_id')->nullable();
            $table->string('name')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('rate', 28, 8)->nullable();
            $table->string('required_time')->nullable();
            $table->string('required_time_format')->nullable();
            $table->decimal('charge', 28, 8)->nullable();
            $table->string('charge_type')->nullable();
            $table->decimal('min_withdraw', 28, 8)->nullable();
            $table->decimal('max_withdraw', 28, 8)->nullable();
            $table->text('fields')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_methods');
    }
};
