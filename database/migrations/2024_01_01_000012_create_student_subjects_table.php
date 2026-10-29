<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $t->enum('status', ['ACTIVE','DROPPED','COMPLETED'])->default('ACTIVE');
            $t->timestamps();
            $t->unique(['student_enrollment_id','subject_id'], 'uk_enrollment_subject');
        });
    }
    public function down(): void { Schema::dropIfExists('student_subjects'); }
};