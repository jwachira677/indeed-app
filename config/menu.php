<?php

/*
|--------------------------------------------------------------------------
| Sidebar menu - the single source of truth for navigation
|--------------------------------------------------------------------------
| Each node may have:
|   label      (required)
|   icon       bootstrap-icons class, e.g. 'bi-person'
|   route      route name. Omit (or use a route that doesn't exist yet) and the
|                item renders as a disabled "Coming soon" link.
|   permission permission slug required to see the item. Leave out for "any
|                logged-in user".
|   active     route-name patterns that mark the item active. Defaults to the
|                route's resource prefix, e.g. teachers.index -> teachers.*
|   badge      key resolved in App\Support\Menu::badge()
|   children   nested nodes. A parent is shown only if at least one child is
|                visible to the user, so you never need a permission on it.
*/

return [

    [
        'label' => 'Main Menu',
        'icon' => 'bi-grid',
        'children' => [
            ['label' => 'Dashboard', 'icon' => 'bi-speedometer2',   'route' => 'dashboard', 'permission' => 'dashboard.view'],
            ['label' => 'Analytics', 'icon' => 'bi-bar-chart-line', 'route' => 'analytics', 'permission' => 'analytics.view'],
        ],
    ],

    [
        'label' => 'Students & Academic',
        'icon' => 'bi-person-video3',
        'children' => [
            ['label' => 'Students',    'icon' => 'bi-person',          'route' => 'students.index',    'permission' => 'students.view'],
            ['label' => 'Enrollments', 'icon' => 'bi-clipboard-check', 'route' => 'enrollments.index', 'permission' => 'enrollments.view'],
            ['label' => 'Classes',     'icon' => 'bi-door-open',       'route' => 'classes.index',     'permission' => 'classes.view'],
            ['label' => 'Teachers',    'icon' => 'bi-person-workspace', 'route' => 'teachers.index',   'permission' => 'teachers.view'],
            [
                'label' => 'Academic Calendar',
                'icon' => 'bi-calendar3',
                'children' => [
                    ['label' => 'Academic Years', 'icon' => 'bi-calendar-year', 'route' => 'academic-years.index', 'permission' => 'academic_years.view'],
                    ['label' => 'Terms',          'icon' => 'bi-calendar-week', 'route' => 'terms.index',          'permission' => 'terms.view'],
                ],
            ],
        ],
    ],

    [
        'label' => 'CBC Curriculum',
        'icon' => 'bi-journal-bookmark',
        'children' => [
            ['label' => 'Grade Levels', 'icon' => 'bi-layers',        'route' => 'grades.index',   'permission' => 'grades.view'],
            ['label' => 'Subjects',     'icon' => 'bi-book',          'route' => 'subjects.index', 'permission' => 'subjects.view'],
            ['label' => 'Pathways',     'icon' => 'bi-signpost-split', 'route' => 'pathways.index', 'permission' => 'pathways.view'],
            [
                'label' => 'Curriculum Mapping',
                'icon' => 'bi-diagram-3',
                'children' => [
                    ['label' => 'Grade Subjects',   'icon' => 'bi-list-check',   'route' => 'curriculum.grade-subjects.index',   'permission' => 'curriculum_mapping.view'],
                    ['label' => 'Pathway Subjects', 'icon' => 'bi-shuffle',      'route' => 'curriculum.pathway-subjects.index', 'permission' => 'curriculum_mapping.view'],
                    ['label' => 'Student Subjects', 'icon' => 'bi-person-check', 'route' => 'curriculum.student-subjects.index', 'permission' => 'curriculum_mapping.view'],
                ],
            ],
        ],
    ],

    [
        'label' => 'Fees & Finance',
        'icon' => 'bi-cash-stack',
        'children' => [
            ['label' => 'Fee Types',       'icon' => 'bi-tags',                'route' => 'fee-types.index',      'permission' => 'fee_types.view'],
            ['label' => 'Fee Structures',  'icon' => 'bi-table',               'route' => 'fee-structures.index', 'permission' => 'fee_structures.view'],
            ['label' => 'Student Bills',   'icon' => 'bi-receipt',             'route' => 'bills.index',          'permission' => 'bills.view'],
            ['label' => 'Payments',        'icon' => 'bi-cash-coin',           'route' => 'payments.index',       'permission' => 'payments.view'],
            ['label' => 'Finance Reports', 'icon' => 'bi-file-earmark-bar-graph', 'route' => 'finance-reports.index', 'permission' => 'finance_reports.view'],
        ],
    ],

    [
        'label' => 'System',
        'icon' => 'bi-gear',
        'children' => [
            ['label' => 'Users',       'icon' => 'bi-people',      'route' => 'users.index', 'permission' => 'users.view', 'badge' => 'users'],
            ['label' => 'Roles',       'icon' => 'bi-shield-lock', 'route' => 'roles.index', 'permission' => 'roles.view'],
            ['label' => 'Permissions', 'icon' => 'bi-key',         'route' => 'permissions', 'permission' => 'permissions.view', 'active' => ['permissions', 'permissions.*']],
            ['label' => 'Settings',    'icon' => 'bi-sliders',     'route' => 'settings.index', 'permission' => 'settings.view'],
            ['label' => 'Help & Support', 'icon' => 'bi-question-circle', 'route' => 'help'],
        ],
    ],
        [
        'label' => 'Examinations',
        'icon' => 'bi-journal-check',
        'children' => [
            ['label' => 'Exams',   'icon' => 'bi-pencil-square', 'route' => 'exams.index',   'permission' => 'examinations.view'],
            ['label' => 'Results', 'icon' => 'bi-award',         'route' => 'results.index', 'permission' => 'examinations.view'],
        ],
    ],

];
