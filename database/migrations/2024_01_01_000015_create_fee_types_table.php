<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fee_types', function (Blueprint $t) {
            $t->id();
            $t->string('code', 30)->unique();
            $t->string('name', 100);
            $t->text('description')->nullable();
            $t->enum('billing_frequency', ['PER_TERM','PER_MONTH','PER_YEAR','ONE_OFF'])->default('PER_TERM');
            $t->boolean('is_optional')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fee_types'); }
};