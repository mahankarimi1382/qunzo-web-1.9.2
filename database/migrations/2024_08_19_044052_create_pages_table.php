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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->string('title')->nullable();
            $table->string('code')->nullable();
            $table->longText('data')->nullable();
            $table->enum('type', ['static', 'dynamic'])->default('dynamic')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('locale')->default('en')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
