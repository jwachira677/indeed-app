<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_transport_routes_table.php
    public function up(): void {
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "Route A - Westlands", "Zone 1"
            $table->decimal('amount', 10, 2); // e.g., 3500.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};
