<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_of_payment')->nullable();
            $table->string('extracted_transaction_id')->nullable();
            $table->decimal('extracted_amount', 10, 2)->nullable();
            $table->date('extracted_date')->nullable();
            $table->string('raw_ocr_text', 2000)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'proof_of_payment',
                'extracted_transaction_id',
                'extracted_amount',
                'extracted_date',
                'raw_ocr_text',
            ]);
        });
    }
};