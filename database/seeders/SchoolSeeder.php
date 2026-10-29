<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $year2025 = DB::table('academic_years')->where('year_name', '2025')->value('id');
        $term1    = DB::table('terms')->where('academic_year_id', $year2025)->where('term_number', 1)->value('id');

        // Classes
        $classSpecs = [
            ['grade' => 'G4',  'name' => 'Grade 4',  'stream' => 'East'],
            ['grade' => 'G7',  'name' => 'Grade 7',  'stream' => 'North'],
            ['grade' => 'G10', 'name' => 'Grade 10', 'stream' => 'West'],
        ];

        $classIds = [];
        foreach ($classSpecs as $cs) {
            $gId = DB::table('grade_levels')->where('code', $cs['grade'])->value('id');
            $classIds[$cs['grade']] = DB::table('classes')->insertGetId([
                'grade_level_id'   => $gId,
                'academic_year_id' => $year2025,
                'class_name'       => $cs['name'],
                'stream'           => $cs['stream'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Students
        $studentSpecs = [
            ['ADM001','Amani','Mwangi','MALE'],
            ['ADM002','Baraka','Otieno','MALE'],
            ['ADM003','Chebet','Kipchoge','FEMALE'],
            ['ADM004','Dotty','Wanjiku','FEMALE'],
            ['ADM005','Eli','Onyango','MALE'],
            ['ADM006','Faith','Njeri','FEMALE'],
            ['ADM007','Gitau','Kamau','MALE'],
            ['ADM008','Hawa','Ali','FEMALE'],
            ['ADM009','Imani','Kariuki','MALE'],
            ['ADM010','Joy','Mutiso','FEMALE'],
        ];

        $studentIds = [];
        foreach ($studentSpecs as $s) {
            $studentIds[] = DB::table('students')->insertGetId([
                'admission_number' => $s[0],
                'first_name'       => $s[1],
                'last_name'        => $s[2],
                'gender'           => $s[3],
                'date_of_birth'    => '2015-04-12',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Enrollments: 4 → G4, 3 → G7, 3 → G10
        $enrollments = [
            [$studentIds[0], $classIds['G4']],
            [$studentIds[1], $classIds['G4']],
            [$studentIds[2], $classIds['G4']],
            [$studentIds[3], $classIds['G4']],
            [$studentIds[4], $classIds['G7']],
            [$studentIds[5], $classIds['G7']],
            [$studentIds[6], $classIds['G7']],
            [$studentIds[7], $classIds['G10']],
            [$studentIds[8], $classIds['G10']],
            [$studentIds[9], $classIds['G10']],
        ];

        $enrollmentIds = [];
        foreach ($enrollments as [$sid, $cid]) {
            $enrollmentIds[] = DB::table('student_enrollments')->insertGetId([
                'student_id'      => $sid,
                'class_id'        => $cid,
                'enrollment_date' => '2025-01-06',
                'status'          => 'ACTIVE',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Teachers
        $teacherSpecs = [
            ['TSC001','Mary','Wanjala','mary@example.com','0700111222'],
            ['TSC002','John','Kibet','john@example.com','0700333444'],
            ['TSC003','Peter','Mutua','peter@example.com','0700555666'],
        ];
        $teacherIds = [];
        foreach ($teacherSpecs as $t) {
            $teacherIds[] = DB::table('teachers')->insertGetId([
                'staff_number' => $t[0],
                'first_name'   => $t[1],
                'last_name'    => $t[2],
                'email'        => $t[3],
                'phone'        => $t[4],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Teacher assignments
        $assignments = [
            [$teacherIds[0], $classIds['G4'],  'ENG'],
            [$teacherIds[0], $classIds['G4'],  'MATH'],
            [$teacherIds[1], $classIds['G7'],  'INT_SCI'],
            [$teacherIds[1], $classIds['G7'],  'MATH'],
            [$teacherIds[2], $classIds['G10'], 'BIO'],
            [$teacherIds[2], $classIds['G10'], 'CHEM'],
        ];
        foreach ($assignments as [$tid, $cid, $subjCode]) {
            DB::table('teacher_class_subjects')->insert([
                'teacher_id'       => $tid,
                'class_id'         => $cid,
                'subject_id'       => DB::table('subjects')->where('code', $subjCode)->value('id'),
                'academic_year_id' => $year2025,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Senior pathway choices for the 3 Grade-10 students
        $stemId = DB::table('pathways')->where('code', 'STEM')->value('id');
        foreach ([7, 8, 9] as $idx) {
            DB::table('student_pathways')->insert([
                'student_id'       => $studentIds[$idx],
                'pathway_id'       => $stemId,
                'academic_year_id' => $year2025,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Register subjects for every enrollment
        foreach ($enrollmentIds as $i => $eid) {
            $gradeCode = $i < 4 ? 'G4' : ($i < 7 ? 'G7' : 'G10');
            $gradeId   = DB::table('grade_levels')->where('code', $gradeCode)->value('id');

            // compulsory subjects from grade_subjects
            $subjIds = DB::table('grade_subjects')
                ->where('grade_level_id', $gradeId)
                ->where('is_compulsory', true)
                ->pluck('subject_id')
                ->all();

            // for G10 students, add 3 STEM pathway subjects
            if ($gradeCode === 'G10') {
                $extra = DB::table('pathway_subjects')
                    ->where('pathway_id', $stemId)
                    ->limit(3)->pluck('subject_id')->all();
                $subjIds = array_merge($subjIds, $extra);
            }

            foreach ($subjIds as $sid) {
                DB::table('student_subjects')->insert([
                    'student_enrollment_id' => $eid,
                    'subject_id'            => $sid,
                    'status'                => 'ACTIVE',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}