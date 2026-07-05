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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->nullable();
            $table->string('code', 30)->nullable();
            $table->enum('type', ['fiat', 'crypto'])->default('fiat');
            $table->string('symbol', 30)->nullable();
            $table->string('icon')->nullable();
            $table->decimal('conversion_rate', 28, 8)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
