<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            ['year_name' => '2024', 'start_date' => '2024-01-08', 'end_date' => '2024-10-25', 'is_current' => false],
            ['year_name' => '2025', 'start_date' => '2025-01-06', 'end_date' => '2025-10-24', 'is_current' => true],
        ];

        foreach ($years as $y) {
            $yearId = DB::table('academic_years')->insertGetId($y + [
                'created_at' => now(), 'updated_at' => now(),
            ]);

            for ($i = 1; $i <= 3; $i++) {
                DB::table('terms')->insert([
                    'academic_year_id' => $yearId,
                    'term_number'      => $i,
                    'start_date'       => date('Y-m-d', strtotime($y['start_date'] . " +" . (($i - 1) * 4) . " months")),
                    'end_date'         => date('Y-m-d', strtotime($y['start_date'] . " +" . (($i * 4) - 1) . " months")),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}