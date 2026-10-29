<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $t->date('payment_date');
            $t->decimal('amount', 10, 2);
            $t->enum('payment_method', ['CASH','MPESA','BANK','CHEQUE','CARD']);
            $t->string('reference_number', 100)->nullable();
            $t->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('notes', 255)->nullable();
            $t->timestamps();
            $t->index(['student_id','payment_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};