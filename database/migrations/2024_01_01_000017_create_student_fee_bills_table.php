<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_fee_bills', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $t->foreignId('class_fee_structure_id')->constrained('class_fee_structures')->restrictOnDelete();
            $t->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $t->decimal('amount_due', 10, 2);
            $t->decimal('discount', 10, 2)->default(0);
            $t->enum('status', ['PENDING','PARTIAL','PAID','WAIVED','OPTED_OUT'])->default('PENDING');
            $t->date('due_date')->nullable();
            $t->timestamps();
            $t->unique(['student_enrollment_id','class_fee_structure_id'], 'uk_enrollment_structure');
        });
    }
    public function down(): void { Schema::dropIfExists('student_fee_bills'); }
};