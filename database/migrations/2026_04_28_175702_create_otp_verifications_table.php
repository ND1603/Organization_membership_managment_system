<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');        // the email address
            $table->string('type')->default('email');
            $table->string('otp');               // stored as a hash, not plain text
            $table->timestamp('expires_at');     // code expires after 10 minutes
            $table->boolean('used')->default(false); // prevents reuse
            $table->timestamps();

            $table->index(['identifier', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};