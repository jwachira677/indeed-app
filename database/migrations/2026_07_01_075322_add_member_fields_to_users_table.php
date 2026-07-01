<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role_id as foreign key
            $table->foreignId('role_id')
                ->after('id')
                ->default(2) // Default to 'member' role
                ->constrained()
                ->onDelete('restrict');

            // Member specific fields
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->string('national_id', 50)->unique()->nullable()->after('phone_number');
            
            // Additional useful fields
            $table->string('member_id', 50)->unique()->nullable()->after('national_id');
            $table->date('date_of_birth')->nullable()->after('member_id');
            $table->text('address')->nullable()->after('date_of_birth');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending')->after('address');
            $table->timestamp('joined_at')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('joined_at');
            
            // Indexes for better performance
            $table->index('phone_number');
            $table->index('status');
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'phone_number',
                'national_id',
                'member_id',
                'date_of_birth',
                'address',
                'status',
                'joined_at',
                'last_login_at'
            ]);
        });
    }
};