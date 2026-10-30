<?php

/*
|--------------------------------------------------------------------------
| Permission catalogue - the single source of truth
|--------------------------------------------------------------------------
| Every permission slug is "<module>.<action>", e.g. students.view.
| Add a module or action here, run `php artisan db:seed --class=PermissionSeeder`,
| and it appears in the permissions matrix automatically.
|
| Removing an entry here also removes it from the database on the next seed.
*/

$crud = ['view', 'create', 'update', 'delete'];

return [

    'modules' => [
        'dashboard'          => ['label' => 'Dashboard',          'actions' => ['view']],
        'analytics'          => ['label' => 'Analytics',          'actions' => ['view', 'export']],

        // Students & Academic
        'students'           => ['label' => 'Students',           'actions' => [...$crud, 'export']],
        'enrollments'        => ['label' => 'Enrollments',        'actions' => $crud],
        'classes'            => ['label' => 'Classes',            'actions' => $crud],
        'teachers'           => ['label' => 'Teachers',           'actions' => [...$crud, 'export']],
        'academic_years'     => ['label' => 'Academic Years',     'actions' => $crud],
        'terms'              => ['label' => 'Terms',              'actions' => $crud],

        // CBC Curriculum
        'grades'             => ['label' => 'Grade Levels',       'actions' => $crud],
        'subjects'           => ['label' => 'Subjects',           'actions' => $crud],
        'pathways'           => ['label' => 'Pathways',           'actions' => $crud],
        'curriculum_mapping' => ['label' => 'Curriculum Mapping', 'actions' => $crud],

        // Fees & Finance
        'fee_types'          => ['label' => 'Fee Types',          'actions' => $crud],
        'fee_structures'     => ['label' => 'Fee Structures',     'actions' => $crud],
        'bills'              => ['label' => 'Student Bills',      'actions' => $crud],
        'payments'           => ['label' => 'Payments',           'actions' => [...$crud, 'export']],
        'finance_reports'    => ['label' => 'Finance Reports',    'actions' => ['view', 'export']],

        // System
        'users'              => ['label' => 'Users',              'actions' => $crud],
        'roles'              => ['label' => 'Roles',              'actions' => $crud],
        'permissions'        => ['label' => 'Permissions',        'actions' => ['view', 'update']],
        'settings'           => ['label' => 'Settings',           'actions' => ['view', 'update']],


        'examinations'      => ['label' => 'Examinations', 'actions' => [...$crud, 'export']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Starting permissions per role (applied by the seeder)
    |--------------------------------------------------------------------------
    | Wildcards allowed ("students.*"). The seeder only applies these to a role
    | that has NO permissions yet, so it never overwrites what an admin has
    | customised in the matrix. The admin role needs no entry: it bypasses
    | every check in Gate::before.
    */
    'defaults' => [
        'teacher' => [
            'dashboard.view',
            'students.view',
            'classes.view',
            'subjects.view',
            'grades.view',
            'enrollments.view',
            'teachers.view',
        ],
        'parent' => [
            'dashboard.view',
            'students.view',    // row-level rule: only their own children (policy)
            'bills.view',
            'payments.view',
        ],
        'member' => [
            'dashboard.view',
        ],
        'guest' => [],
    ],
];
