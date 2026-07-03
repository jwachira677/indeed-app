<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_type_id')->constrained()->onDelete('cascade');
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->integer('duration_days');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert duration tiers for Ordinary Loans
        $ordinaryLoanTypeId = DB::table('loan_types')->where('slug', 'ordinary')->first()->id;

        $tiers = [
            ['min' => 0, 'max' => 20000, 'days' => 30, 'desc' => 'Up to 20K - 30 days'],
            ['min' => 20001, 'max' => 40000, 'days' => 60, 'desc' => '21K to 40K - 60 days'],
            ['min' => 40001, 'max' => 60000, 'days' => 90, 'desc' => '41K to 60K - 90 days'],
            ['min' => 60001, 'max' => 80000, 'days' => 120, 'desc' => '61K to 80K - 120 days'],
            ['min' => 80001, 'max' => 100000, 'days' => 150, 'desc' => '81K to 100K - 150 days'],
            ['min' => 100001, 'max' => 120000, 'days' => 180, 'desc' => '101K to 120K - 180 days'],
            ['min' => 120001, 'max' => 140000, 'days' => 210, 'desc' => '121K to 140K - 210 days'],
            ['min' => 140001, 'max' => 160000, 'days' => 240, 'desc' => '141K to 160K - 240 days'],
            ['min' => 160001, 'max' => 180000, 'days' => 270, 'desc' => '161K to 180K - 270 days'],
            ['min' => 180001, 'max' => 200000, 'days' => 300, 'desc' => '181K to 200K - 300 days'],
        ];

        foreach ($tiers as $tier) {
            DB::table('loan_durations')->insert([
                'loan_type_id' => $ordinaryLoanTypeId,
                'min_amount' => $tier['min'],
                'max_amount' => $tier['max'],
                'duration_days' => $tier['days'],
                'is_active' => true,
                'description' => $tier['desc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_durations');
    }
};