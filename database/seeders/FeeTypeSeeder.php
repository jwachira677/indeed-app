<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'TUITION',   'name' => 'Tuition',           'billing_frequency' => 'PER_TERM',  'is_optional' => false],
            ['code' => 'MEALS',     'name' => 'Meals / Lunch',     'billing_frequency' => 'PER_TERM',  'is_optional' => true],
            ['code' => 'TRANSPORT', 'name' => 'School Transport',  'billing_frequency' => 'PER_TERM',  'is_optional' => true],
            ['code' => 'EXAM',      'name' => 'Examination Fee',   'billing_frequency' => 'PER_TERM',  'is_optional' => false],
            ['code' => 'UNIFORM',   'name' => 'Uniform',           'billing_frequency' => 'ONE_OFF',   'is_optional' => false],
        ];

        foreach ($types as $ft) {
            DB::table('fee_types')->insert($ft + [
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}