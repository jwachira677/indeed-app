<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular member access',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Guest user access',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};