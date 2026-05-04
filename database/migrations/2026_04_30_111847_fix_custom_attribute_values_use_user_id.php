<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_attribute_values', function (Blueprint $table) {
            // Drop the old member_id foreign key and column
            $table->dropForeign(['member_id']);
            $table->dropIndex('member_attr_unique');
            $table->dropColumn('member_id');

            // Add user_id instead
            $table->foreignId('user_id')
                  ->after('id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Re-add the unique constraint with new column
            $table->unique(['user_id', 'custom_attribute_definition_id'], 'user_attr_unique');
        });
    }

    public function down(): void
    {
        Schema::table('custom_attribute_values', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('user_attr_unique');
            $table->dropColumn('user_id');

            $table->foreignId('member_id')
                  ->constrained('members')
                  ->cascadeOnDelete();

            $table->unique(['member_id', 'custom_attribute_definition_id'], 'member_attr_unique');
        });
    }
};