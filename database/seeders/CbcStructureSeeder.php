<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CbcStructureSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['code' => 'PP1', 'name' => 'Pre-Primary 1', 'level_group' => 'PRE_PRIMARY',      'sort_order' => 1],
            ['code' => 'PP2', 'name' => 'Pre-Primary 2', 'level_group' => 'PRE_PRIMARY',      'sort_order' => 2],
            ['code' => 'G1',  'name' => 'Grade 1',       'level_group' => 'LOWER_PRIMARY',    'sort_order' => 3],
            ['code' => 'G2',  'name' => 'Grade 2',       'level_group' => 'LOWER_PRIMARY',    'sort_order' => 4],
            ['code' => 'G3',  'name' => 'Grade 3',       'level_group' => 'LOWER_PRIMARY',    'sort_order' => 5],
            ['code' => 'G4',  'name' => 'Grade 4',       'level_group' => 'UPPER_PRIMARY',    'sort_order' => 6],
            ['code' => 'G5',  'name' => 'Grade 5',       'level_group' => 'UPPER_PRIMARY',    'sort_order' => 7],
            ['code' => 'G6',  'name' => 'Grade 6',       'level_group' => 'UPPER_PRIMARY',    'sort_order' => 8],
            ['code' => 'G7',  'name' => 'Grade 7',       'level_group' => 'JUNIOR_SECONDARY', 'sort_order' => 9],
            ['code' => 'G8',  'name' => 'Grade 8',       'level_group' => 'JUNIOR_SECONDARY', 'sort_order' => 10],
            ['code' => 'G9',  'name' => 'Grade 9',       'level_group' => 'JUNIOR_SECONDARY', 'sort_order' => 11],
            ['code' => 'G10', 'name' => 'Grade 10',      'level_group' => 'SENIOR_SECONDARY', 'sort_order' => 12],
            ['code' => 'G11', 'name' => 'Grade 11',      'level_group' => 'SENIOR_SECONDARY', 'sort_order' => 13],
            ['code' => 'G12', 'name' => 'Grade 12',      'level_group' => 'SENIOR_SECONDARY', 'sort_order' => 14],
        ];

        foreach ($grades as $g) {
            DB::table('grade_levels')->insert($g + ['created_at' => now(), 'updated_at' => now()]);
        }

        $pathways = [
            ['code' => 'STEM',           'name' => 'STEM (Science, Technology, Engineering & Mathematics)'],
            ['code' => 'SOCIAL_SCIENCES','name' => 'Social Sciences'],
            ['code' => 'ARTS_SPORTS',    'name' => 'Arts & Sports Science'],
        ];

        foreach ($pathways as $p) {
            DB::table('pathways')->insert($p + ['created_at' => now(), 'updated_at' => now()]);
        }
    }
}