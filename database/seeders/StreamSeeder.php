<?php

// php artisan db:seed --class=StreamSeeder
namespace Database\Seeders;

use App\Models\Stream;
use Illuminate\Database\Seeder;

class StreamSeeder extends Seeder
{
    public function run(): void
    {
        $streams = [
            ['name' => 'East', 'code' => 'E'],
            ['name' => 'West', 'code' => 'W'],
            ['name' => 'North', 'code' => 'N'],
            ['name' => 'South', 'code' => 'S'],
            ['name' => 'Main', 'code' => 'M'],
            ['name' => 'Blue', 'code' => 'B'],
            ['name' => 'Red', 'code' => 'R'],
            ['name' => 'Green', 'code' => 'G'],
        ];

        foreach ($streams as $stream) {
            Stream::updateOrCreate(
                ['name' => $stream['name']],
                $stream
            );
        }
    }
}