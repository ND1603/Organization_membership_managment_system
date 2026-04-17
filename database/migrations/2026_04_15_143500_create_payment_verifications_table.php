<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // which member submitted
            $table->string('screenshot_path');           // where the image is stored
            $table->string('extracted_transaction_id')->nullable(); // OCR result
            $table->decimal('extracted_amount', 10, 2)->nullable();  // OCR result
            $table->date('extracted_date')->nullable();              // OCR result
            $table->string('raw_ocr_text', 2000)->nullable();        // full raw text from OCR
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('admin_note')->nullable();    // admin can leave a note
            $table->foreignId('verified_by')->nullable()->constrained('users'); // which admin verified
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_verifications');
    }
};