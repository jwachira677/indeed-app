<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Loan details
            $table->string('loan_number')->unique();
            $table->enum('loan_type', ['instant', 'ordinary', 'emergency'])->default('ordinary');
            $table->decimal('amount_taken', 15, 2);
            $table->decimal('amount_due', 15, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('interest_amount', 15, 2)->default(0);
            $table->decimal('total_amount_payable', 15, 2);
            
            // Dates
            $table->timestamp('date_taken');
            $table->timestamp('date_due');
            $table->timestamp('date_paid')->nullable();
            
            // Payment details
            $table->enum('payment_frequency', ['weekly', 'monthly', 'quarterly'])->default('monthly');
            $table->decimal('installment_amount', 15, 2)->nullable();
            $table->integer('installments_count')->nullable();
            $table->integer('installments_paid')->default(0);
            
            // Status
            $table->enum('status', [
                'pending', 
                'approved', 
                'active', 
                'completed', 
                'defaulted', 
                'cancelled'
            ])->default('pending');
            
            // Additional fields
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('loan_number');
            $table->index('status');
            $table->index('date_due');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};