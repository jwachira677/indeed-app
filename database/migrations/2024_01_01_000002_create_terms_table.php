<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('terms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $t->unsignedTinyInteger('term_number'); // 1..3
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->timestamps();
            $t->unique(['academic_year_id', 'term_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('terms'); }
};