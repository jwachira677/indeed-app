<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // code => [name, is_core]
        $subjects = [
            'INDIG'   => ['Indigenous Language',                     true],
            'KIS'     => ['Kiswahili',                               true],
            'KSL'     => ['Kenya Sign Language',                     true],
            'ENG'     => ['English',                                 true],
            'MATH'    => ['Mathematics',                             true],
            'RE'      => ['Religious Education',                     true],
            'ENV'     => ['Environmental Activities',                true],
            'CREAT'   => ['Creative Activities',                     true],
            'AGRI_NUT'=> ['Agriculture and Nutrition',               true],
            'SOC'     => ['Social Studies',                          true],
            'CREAT_ARTS'=>['Creative Arts',                          true],
            'SCI_TECH'=> ['Science and Technology',                  true],
            'INT_SCI' => ['Integrated Science',                      true],
            'PRE_TECH'=> ['Pre-Technical and Pre-Career Education',  true],
            'AGRI'    => ['Agriculture',                             true],
            'PE'      => ['Sports and Physical Education',           true],
            'CSL'     => ['Community Service Learning',              true],
            'CORE_MATH'=>['Core Mathematics',                        true],
            'ESS_MATH'=> ['Essential Mathematics',                   true],
            'BIO'     => ['Biology',                                 false],
            'CHEM'    => ['Chemistry',                               false],
            'PHYS'    => ['Physics',                                 false],
            'COMP'    => ['Computer Studies',                        false],
            'HIST'    => ['History & Citizenship',                   false],
            'GEO'     => ['Geography',                               false],
            'BUS'     => ['Business Studies',                        false],
            'LIT'     => ['Literature in English',                   false],
            'KIS_LIT' => ['Kiswahili Literature',                    false],
            'FINE_ART'=> ['Fine Arts',                               false],
            'THEATRE' => ['Theatre & Film',                          false],
            'MUSIC'   => ['Music & Dance',                           false],
            'SPORTS'  => ['Sports & Recreation',                     false],
            'SPORTS_SCI'=>['Sports Science',                         false],
            'FRENCH'  => ['French',                                  false],
            'GERMAN'  => ['German',                                  false],
            'MANDARIN'=> ['Mandarin',                                false],
            'ARABIC'  => ['Arabic',                                  false],
        ];

        $ids = [];
        foreach ($subjects as $code => [$name, $isCore]) {
            $ids[$code] = DB::table('subjects')->insertGetId([
                'code' => $code, 'name' => $name, 'is_core' => $isCore,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // grade code => [ [subject_code, is_compulsory], ... ]
        $gradeMap = [
            'G1' => ['INDIG'=>1,'KIS'=>1,'ENG'=>1,'MATH'=>1,'RE'=>1,'ENV'=>1,'CREAT'=>1],
            'G2' => ['INDIG'=>1,'KIS'=>1,'ENG'=>1,'MATH'=>1,'RE'=>1,'ENV'=>1,'CREAT'=>1],
            'G3' => ['INDIG'=>1,'KIS'=>1,'ENG'=>1,'MATH'=>1,'RE'=>1,'ENV'=>1,'CREAT'=>1],
            'G4' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'RE'=>1,'AGRI_NUT'=>1,'SOC'=>1,'CREAT_ARTS'=>1,'SCI_TECH'=>1,'FRENCH'=>0],
            'G5' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'RE'=>1,'AGRI_NUT'=>1,'SOC'=>1,'CREAT_ARTS'=>1,'SCI_TECH'=>1,'FRENCH'=>0],
            'G6' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'RE'=>1,'AGRI_NUT'=>1,'SOC'=>1,'CREAT_ARTS'=>1,'SCI_TECH'=>1,'FRENCH'=>0],
            'G7' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'INT_SCI'=>1,'PRE_TECH'=>1,'SOC'=>1,'RE'=>1,'AGRI'=>1,'PE'=>1],
            'G8' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'INT_SCI'=>1,'PRE_TECH'=>1,'SOC'=>1,'RE'=>1,'AGRI'=>1,'PE'=>1],
            'G9' => ['ENG'=>1,'KIS'=>1,'MATH'=>1,'INT_SCI'=>1,'PRE_TECH'=>1,'SOC'=>1,'RE'=>1,'AGRI'=>1,'PE'=>1],
            'G10'=> ['ENG'=>1,'KIS'=>1,'CORE_MATH'=>1,'CSL'=>1],
            'G11'=> ['ENG'=>1,'KIS'=>1,'CORE_MATH'=>1,'CSL'=>1],
            'G12'=> ['ENG'=>1,'KIS'=>1,'CORE_MATH'=>1,'CSL'=>1],
        ];

        foreach ($gradeMap as $gCode => $map) {
            $gId = DB::table('grade_levels')->where('code', $gCode)->value('id');
            foreach ($map as $sCode => $compulsory) {
                DB::table('grade_subjects')->insert([
                    'grade_level_id' => $gId,
                    'subject_id'     => $ids[$sCode],
                    'is_compulsory'  => (bool) $compulsory,
                    'created_at'     => now(), 'updated_at' => now(),
                ]);
            }
        }

        // pathway → subjects
        $pathwayMap = [
            'STEM'            => ['BIO','CHEM','PHYS','COMP','AGRI'],
            'SOCIAL_SCIENCES' => ['HIST','GEO','BUS','LIT','KIS_LIT','RE','FRENCH','GERMAN','MANDARIN','ARABIC'],
            'ARTS_SPORTS'     => ['FINE_ART','THEATRE','MUSIC','SPORTS','SPORTS_SCI'],
        ];

        foreach ($pathwayMap as $pCode => $subjectCodes) {
            $pId = DB::table('pathways')->where('code', $pCode)->value('id');
            foreach ($subjectCodes as $sCode) {
                DB::table('pathway_subjects')->insert([
                    'pathway_id' => $pId,
                    'subject_id' => $ids[$sCode],
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}