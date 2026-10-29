<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->string('admission_number', 30)->unique();
            $t->string('first_name', 100);
            $t->string('last_name', 100);
            $t->date('date_of_birth')->nullable();
            $t->enum('gender', ['MALE','FEMALE','OTHER'])->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('students'); }
};