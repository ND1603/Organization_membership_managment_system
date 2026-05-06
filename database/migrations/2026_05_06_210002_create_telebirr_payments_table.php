<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telebirr_payments', function (Blueprint $table) {
            $table->id();

            // Who made the payment
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Our internal order ID — we generate this
            $table->string('out_trade_no')->unique();

            // Amount in ETB
            $table->decimal('amount', 10, 2);

            // What the payment is for
            $table->string('description')->nullable();

            // pending → paid → failed
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');

            // Telebirr's own transaction ID — filled when payment completes
            $table->string('trade_no')->nullable();

            // Member's phone number used on Telebirr
            $table->string('msisdn')->nullable();

            // The URL Telebirr gave us to redirect the member to
            $table->text('pay_url')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telebirr_payments');
    }
};