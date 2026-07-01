<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Fine details
            $table->string('fine_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->text('description')->nullable();
            
            // Payment details
            $table->date('date_paid')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            
            // Payment method
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mpesa', 'cheque', 'other'])->nullable();
            $table->string('transaction_id')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'paid', 'overdue', 'waived', 'partially_paid'])->default('pending');
            
            // Timestamps
            $table->timestamp('issued_date');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('status');
            $table->index('fine_number');
            $table->index('date_paid');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};