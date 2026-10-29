<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassFeeStructureSeeder extends Seeder
{
    public function run(): void
    {
        $year2025 = DB::table('academic_years')->where('year_name', '2025')->value('id');
        $term1    = DB::table('terms')->where('academic_year_id', $year2025)->where('term_number', 1)->value('id');

        $feeTypeIds = DB::table('fee_types')->pluck('id', 'code');

        // [gradeCode => [feeCode => [amount, is_optional, notes]]]
        $schedule = [
            'G4' => [
                'TUITION'   => [15000.00, false, null],
                'MEALS'     => [ 4500.00, true,  null],
                'TRANSPORT' => [ 3500.00, true,  'Route A – Kilimani'],
                'EXAM'      => [ 2000.00, false, null],
            ],
            'G7' => [
                'TUITION'   => [18000.00, false, null],
                'MEALS'     => [ 5000.00, true,  null],
                'TRANSPORT' => [ 3500.00, true,  'Route B – Karen'],
                'EXAM'      => [ 2500.00, false, null],
            ],
            'G10' => [
                'TUITION'   => [22000.00, false, null],
                'MEALS'     => [ 6000.00, true,  null],
                'TRANSPORT' => [ 4000.00, true,  'Route A – Kilimani'],
                'EXAM'      => [ 3000.00, false, null],
            ],
        ];

        foreach ($schedule as $gradeCode => $fees) {
            $classId = DB::table('classes')
                ->join('grade_levels', 'grade_levels.id', '=', 'classes.grade_level_id')
                ->where('grade_levels.code', $gradeCode)
                ->where('classes.academic_year_id', $year2025)
                ->value('classes.id');

            foreach ($fees as $feeCode => [$amount, $optional, $notes]) {
                DB::table('class_fee_structures')->insert([
                    'class_id'      => $classId,
                    'term_id'       => $term1,
                    'fee_type_id'   => $feeTypeIds[$feeCode],
                    'amount'        => $amount,
                    'is_optional'   => $optional,
                    'due_date'      => '2025-02-14',
                    'notes'         => $notes,
                    'created_at'    => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}