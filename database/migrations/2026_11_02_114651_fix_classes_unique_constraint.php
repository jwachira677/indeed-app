<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // 1. Drop the foreign keys so MySQL releases its grip on the index
            $table->dropForeign(['grade_level_id']);
            $table->dropForeign(['academic_year_id']);

            // 2. Drop the old unique constraint
            $table->dropUnique('uk_class');
            
            // 3. Create the new unique constraint including stream_id
            $table->unique(['grade_level_id', 'academic_year_id', 'class_name', 'stream_id'], 'uk_class');

            // 4. Re-add the foreign keys
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->restrictOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['grade_level_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropUnique('uk_class');
            $table->unique(['grade_level_id', 'academic_year_id', 'class_name'], 'uk_class');
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->restrictOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();
        });
    }
};