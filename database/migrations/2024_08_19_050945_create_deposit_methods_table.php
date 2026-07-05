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
        Schema::create('deposit_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('name')->nullable();
            $table->enum('type', ['auto', 'manual'])->default('manual')->nullable();
            $table->string('gateway_code')->nullable();
            $table->decimal('charge', 28, 8)->default(0);
            $table->enum('charge_type', ['percentage', 'fixed']);
            $table->decimal('minimum_deposit', 28, 8);
            $table->decimal('maximum_deposit', 28, 8);
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->longText('field_options')->nullable();
            $table->longText('payment_details')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_methods');
    }
};
