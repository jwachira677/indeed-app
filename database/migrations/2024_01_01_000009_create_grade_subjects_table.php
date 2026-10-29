<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grade_subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('grade_level_id')->constrained('grade_levels')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $t->boolean('is_compulsory')->default(true);
            $t->timestamps();
            $t->unique(['grade_level_id','subject_id'], 'uk_grade_subject');
        });
    }
    public function down(): void { Schema::dropIfExists('grade_subjects'); }
};