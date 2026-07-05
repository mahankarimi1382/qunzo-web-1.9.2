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
        Schema::create('money_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_user_id');
            $table->foreignId('recipient_user_id');
            $table->string('currency');
            $table->decimal('amount', 20, 8);
            $table->decimal('charge', 20, 8);
            $table->decimal('final_amount', 20, 8);
            $table->text('note')->nullable();
            $table->enum('status', ['success', 'pending', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_requests');
    }
};
