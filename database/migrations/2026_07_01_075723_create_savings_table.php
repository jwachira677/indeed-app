<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Savings details
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            
            // Month and Year tracking (financial year starts Dec)
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->string('financial_year'); // e.g., "2024/2025"
            
            // Payment details
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mpesa', 'cheque', 'other'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            
            // Timestamps
            $table->timestamp('saved_date');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'month', 'year']);
            $table->index('financial_year');
            $table->index('status');
            $table->index('receipt_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};