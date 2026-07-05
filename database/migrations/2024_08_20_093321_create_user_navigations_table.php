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
        Schema::create('user_navigations', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('visible_to')->nullable();
            $table->string('url')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->integer('position')->default(0)->nullable();
            $table->json('translation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_navigations');
    }
};
