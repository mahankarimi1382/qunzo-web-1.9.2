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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('currency');
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->string('to');
            $table->text('address');
            $table->text('email');
            $table->json('items');
            $table->decimal('charge', 28, 8);
            $table->decimal('amount', 28, 8);
            $table->decimal('total_amount', 28, 8);
            $table->boolean('is_paid');
            $table->boolean('is_published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
