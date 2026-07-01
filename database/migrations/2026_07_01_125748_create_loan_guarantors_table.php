<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_guarantors', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Guarantor details
            $table->decimal('amount_guaranteed', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->storedAs('amount_guaranteed - amount_paid');
            
            // Relationship with borrower
            $table->enum('relationship', [
                'spouse', 
                'parent', 
                'sibling', 
                'friend', 
                'colleague', 
                'other'
            ])->default('other');
            
            // Guarantor contact (in case different from user profile)
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // Status
            $table->enum('status', [
                'pending', 
                'approved', 
                'active', 
                'released', 
                'defaulted'
            ])->default('pending');
            
            // Timestamps
            $table->timestamp('guaranteed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['loan_id', 'user_id']);
            $table->index('status');
            
            // Ensure a user can't be a guarantor twice for the same loan
            $table->unique(['loan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_guarantors');
    }
};