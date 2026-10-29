<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $t->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $t->date('enrollment_date')->nullable();
            $t->enum('status', ['ACTIVE','COMPLETED','TRANSFERRED','WITHDRAWN'])->default('ACTIVE');
            $t->timestamps();
            $t->unique(['student_id','class_id'], 'uk_student_class');
        });
    }
    public function down(): void { Schema::dropIfExists('student_enrollments'); }
};