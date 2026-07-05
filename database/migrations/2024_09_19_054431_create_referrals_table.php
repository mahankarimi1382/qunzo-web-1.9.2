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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->integer('affiliate_id')->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->integer('item_id')->nullable();
            $table->float('item_price', 8)->default(0)->nullable();
            $table->string('referral_code')->nullable();
            $table->decimal('commission', 8, 2)->nullable();
            $table->boolean('converted')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
