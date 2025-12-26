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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->integer('amount');
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending');
            $table->string('provider')->default('stripe');
            $table->string('provider_payment_id')->nullable();
            $table->string('provider_checkout_url')->nullable();
            $table->string('idempotency_key');
            $table->integer('fee_amount')->nullable();
            $table->integer('net_amount')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'idempotency_key']);
            $table->index(['merchant_id', 'created_at']);
            $table->index(['merchant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
