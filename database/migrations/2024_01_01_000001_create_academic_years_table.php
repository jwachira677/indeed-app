<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('academic_years', function (Blueprint $t) {
            $t->id();
            $t->string('year_name', 20)->unique();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->boolean('is_current')->default(false);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('academic_years'); }
};