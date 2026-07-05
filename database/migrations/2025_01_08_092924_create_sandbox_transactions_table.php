<?php

use App\Models\User;
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
        Schema::create('sandbox_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable();
            $table->string('tnx');
            $table->string('description');
            $table->decimal('amount', 28, 8)->nullable();
            $table->string('type');
            $table->decimal('charge', 28, 8)->default('0');
            $table->decimal('final_amount', 28, 8)->default('0');
            $table->string('method')->nullable();
            $table->string('pay_currency')->nullable();
            $table->text('callback_url')->nullable();
            $table->json('manual_field_data')->nullable();
            $table->text('approval_cause')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sandbox_transactions');
    }
};
