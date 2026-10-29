2024_01_01_000007_create_classes_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('classes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('grade_level_id')->constrained('grade_levels')->restrictOnDelete();
            $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $t->string('class_name', 50);              // 'Grade 4'
            $t->string('stream', 20)->default('');     // 'East','West', or ''
            $t->timestamps();
            $t->unique(['grade_level_id','academic_year_id','class_name','stream'], 'uk_class');
        });
    }
    public function down(): void { Schema::dropIfExists('classes'); }
};