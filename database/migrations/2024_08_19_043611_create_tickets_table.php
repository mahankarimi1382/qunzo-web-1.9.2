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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('low')->nullable();
            $table->enum('status', ['open', 'closed', 'pending'])->default('open')->nullable();
            $table->boolean('is_resolved')->default(false)->nullable();
            $table->boolean('is_locked')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
