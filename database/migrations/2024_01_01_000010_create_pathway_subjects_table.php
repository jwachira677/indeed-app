<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pathway_subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pathway_id')->constrained('pathways')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['pathway_id','subject_id'], 'uk_pathway_subject');
        });
    }
    public function down(): void { Schema::dropIfExists('pathway_subjects'); }
};