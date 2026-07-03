<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default loan types
        DB::table('loan_types')->insert([
            [
                'name' => 'Instant Loan',
                'slug' => 'instant',
                'interest_rate' => 10.00,
                'is_active' => true,
                'description' => 'Quick loan with 10% interest, due in 7 days',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ordinary Loan',
                'slug' => 'ordinary',
                'interest_rate' => 20.00,
                'is_active' => true,
                'description' => 'Standard loan with 20% interest, tiered duration based on amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};