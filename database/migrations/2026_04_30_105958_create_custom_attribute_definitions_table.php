<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_attribute_definitions', function (Blueprint $table) {
            $table->id();

            // The internal name e.g. "department" — no spaces
            $table->string('name')->unique();

            // What the admin sees e.g. "Department"
            $table->string('label');

            // What kind of field it is
            $table->enum('type', ['text', 'number', 'date', 'select', 'boolean'])
                  ->default('text');

            // Only used when type is 'select' — stores the dropdown options
            // e.g. ["IT", "Finance", "HR"]
            $table->json('options')->nullable();

            // Must the member fill this in?
            $table->boolean('is_required')->default(false);

            // Controls the order fields appear on the form
            $table->integer('sort_order')->default(0);

            // Admin can disable a field without deleting it
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_attribute_definitions');
    }
};