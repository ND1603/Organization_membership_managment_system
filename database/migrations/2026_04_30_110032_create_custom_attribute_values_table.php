<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_attribute_values', function (Blueprint $table) {
            $table->id();

            // Which member this value belongs to
            $table->foreignId('member_id')
                  ->constrained('members')
                  ->cascadeOnDelete(); // if member deleted, delete their values too

            // Which field definition this value is for
            $table->foreignId('custom_attribute_definition_id')
                  ->constrained('custom_attribute_definitions')
                  ->cascadeOnDelete(); // if field deleted, delete all its values too

            // The actual value the member entered
            $table->text('value')->nullable();

            $table->timestamps();

            // One member can only have one value per field
           $table->unique(['member_id', 'custom_attribute_definition_id'], 'member_attr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_attribute_values');
    }
};