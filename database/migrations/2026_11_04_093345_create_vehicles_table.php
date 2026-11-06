<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // References the users table. Nullable in case a driver is removed or unassigned
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('registration_number', 20)->unique(); // e.g., KCA 123X
            $table->string('model', 100); // e.g., Toyota Coaster
            $table->unsignedSmallInteger('capacity'); // e.g., 33 seats
            $table->enum('status', ['ACTIVE', 'MAINTENANCE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};