<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payment_allocations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $t->foreignId('student_fee_bill_id')->constrained('student_fee_bills')->restrictOnDelete();
            $t->decimal('amount', 10, 2);
            $t->timestamps();
            $t->unique(['payment_id','student_fee_bill_id'], 'uk_payment_bill');
        });
    }
    public function down(): void { Schema::dropIfExists('payment_allocations'); }
};