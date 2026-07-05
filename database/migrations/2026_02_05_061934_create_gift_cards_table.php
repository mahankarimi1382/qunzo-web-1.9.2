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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->foreignId('user_id');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('product_thumbnail');
            $table->string('sender_name');
            $table->string('recipient_country_code');
            $table->string('recipient_email');
            $table->string('recipient_phone_number');
            $table->string('transaction_id');
            $table->decimal('unit_price', 20, 8);
            $table->integer('quantity');
            $table->decimal('total_price', 20, 8);
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
