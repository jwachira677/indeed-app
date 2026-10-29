<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teacher_class_subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $t->timestamps();
            $t->unique(['teacher_id','class_id','subject_id','academic_year_id'], 'uk_teacher_assignment');
        });
    }
    public function down(): void { Schema::dropIfExists('teacher_class_subjects'); }
};