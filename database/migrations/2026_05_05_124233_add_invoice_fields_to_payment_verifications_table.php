<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_verifications', function (Blueprint $table) {
            // Unique invoice number e.g. INV-2026-00001
            $table->string('invoice_number')->nullable()->unique()->after('verified_at');

            // Path to the stored PDF file
            $table->string('invoice_path')->nullable()->after('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('payment_verifications', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_path']);
        });
    }
};