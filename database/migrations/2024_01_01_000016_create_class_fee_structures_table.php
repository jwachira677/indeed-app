<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('class_fee_structures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $t->foreignId('fee_type_id')->constrained('fee_types')->restrictOnDelete();
            $t->decimal('amount', 10, 2);
            $t->boolean('is_optional')->default(false);
            $t->date('due_date')->nullable();
            $t->string('notes', 255)->nullable();
            $t->timestamps();
            $t->unique(['class_id','term_id','fee_type_id'], 'uk_class_term_fee');
        });
    }
    public function down(): void { Schema::dropIfExists('class_fee_structures'); }
};