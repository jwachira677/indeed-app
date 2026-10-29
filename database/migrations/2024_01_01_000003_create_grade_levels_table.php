<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grade_levels', function (Blueprint $t) {
            $t->id();
            $t->string('code', 10)->unique();   // PP1, G1 ... G12
            $t->string('name', 50);
            $t->enum('level_group', [
                'PRE_PRIMARY','LOWER_PRIMARY','UPPER_PRIMARY',
                'JUNIOR_SECONDARY','SENIOR_SECONDARY'
            ]);
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('grade_levels'); }
};