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
        // This changes made for user payment links
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('type')->default('invoice')->after('user_id');
            $table->date('issue_date')->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->string('to')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->json('items')->nullable()->change();
            $table->decimal('charge', 28, 8)->nullable()->change();
            $table->decimal('amount', 28, 8)->nullable()->change();
            $table->decimal('total_amount', 28, 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
