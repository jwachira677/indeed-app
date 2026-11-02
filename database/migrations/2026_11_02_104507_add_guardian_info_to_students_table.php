<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('mother_name', 100)->nullable()->after('last_name');
            $table->string('father_name', 100)->nullable()->after('mother_name');
            $table->string('guardian_name', 100)->nullable()->after('father_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['mother_name', 'father_name', 'guardian_name']);
        });
    }
};