<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // The member's Fayda Identification Number
            $table->string('fayda_fin')->nullable()->unique()->after('phone_verified_at');

            // Has this member verified with Fayda?
            $table->boolean('fayda_verified')->default(false)->after('fayda_fin');

            // When did they verify?
            $table->timestamp('fayda_verified_at')->nullable()->after('fayda_verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fayda_fin', 'fayda_verified', 'fayda_verified_at']);
        });
    }
};