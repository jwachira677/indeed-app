<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Payment details
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->decimal('penalty_charged', 15, 2)->default(0);
            
            // Payment method
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mpesa', 'cheque', 'other']);
            $table->string('transaction_id')->nullable();
            
            // Date
            $table->timestamp('payment_date');
            
            // Status
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('confirmed');
            
            // Additional
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('receipt_number');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};