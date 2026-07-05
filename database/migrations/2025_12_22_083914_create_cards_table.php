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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('card_holder_id');
            $table->string('card_id')->nullable();
            $table->string('currency')->default('USD');
            $table->string('type')->default('virtual');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->decimal('amount')->default(0.00);
            $table->string('provider');
            $table->string('card_number');
            $table->string('cvc');
            $table->integer('expiration_month');
            $table->integer('expiration_year');
            $table->string('last_four_digits');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
