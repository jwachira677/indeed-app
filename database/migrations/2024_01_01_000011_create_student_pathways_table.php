<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_pathways', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $t->foreignId('pathway_id')->constrained('pathways')->restrictOnDelete();
            $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $t->timestamps();
            $t->unique(['student_id','academic_year_id'], 'uk_student_pathway');
        });
    }
    public function down(): void { Schema::dropIfExists('student_pathways'); }
};