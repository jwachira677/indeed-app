<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->after('amount');
            $table->boolean('is_carried_forward')->default(false)->after('opening_balance');
            $table->decimal('carried_forward_amount', 15, 2)->default(0)->after('is_carried_forward');
            $table->decimal('total_savings_to_date', 15, 2)->default(0)->after('carried_forward_amount');
        });
    }

    public function down(): void
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->dropColumn([
                'opening_balance',
                'is_carried_forward',
                'carried_forward_amount',
                'total_savings_to_date'
            ]);
        });
    }
};