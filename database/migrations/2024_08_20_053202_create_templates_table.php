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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('for')->default('User');
            $table->string('icon');
            $table->boolean('notification_status')->default(1);
            $table->boolean('email_status')->default(1);
            $table->boolean('sms_status')->default(1);
            $table->longText('sms_body')->nullable();
            $table->longText('email_body')->nullable();
            $table->longText('notification_body')->nullable();
            $table->text('short_codes')->nullable();
            $table->string('banner')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->text('salutation')->nullable();
            $table->string('button_level')->nullable();
            $table->string('button_link')->nullable();
            $table->boolean('footer_status')->default(1);
            $table->text('footer_body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
