<?php

return [

    'roles' => [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'hr' => 'HR',
        'accounts' => 'Accounts',
        'staff' => 'Staff',
    ],

    /*
    | Modules and features used for sidebar + permissions.
    | access on user_permissions: view | manage
    | admin_only children are not granted to staff via permission UI later.
    */
    'modules' => [
        [
            'key' => 'clients',
            'title' => 'Clients',
            'description' => 'Job-seekers, documents, and case status.',
            'icon' => 'users',
            'children' => [
                ['key' => 'create', 'label' => 'Add Client'],
                ['key' => 'list', 'label' => 'All Clients'],
                ['key' => 'documents', 'label' => 'Documents'],
                ['key' => 'import', 'label' => 'Excel Import', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'operations',
            'title' => 'Operations',
            'description' => 'Care offs, companies, universities, countries, trades, and process statuses.',
            'icon' => 'briefcase',
            'children' => [
                ['key' => 'care_offs', 'label' => 'Care Offs'],
                ['key' => 'companies', 'label' => 'Companies'],
                ['key' => 'universities', 'label' => 'Universities'],
                ['key' => 'countries', 'label' => 'Countries'],
                ['key' => 'trades', 'label' => 'Trades'],
                ['key' => 'process_statuses', 'label' => 'Process Statuses'],
            ],
        ],
        [
            'key' => 'visitors',
            'title' => 'Daily Visitors',
            'description' => 'Office walk-in log.',
            'icon' => 'building',
            'children' => [
                ['key' => 'list', 'label' => 'Visitors'],
            ],
        ],
        [
            'key' => 'accounts',
            'title' => 'Accounts',
            'description' => 'Payment methods, payments, and reports.',
            'icon' => 'chart',
            'children' => [
                ['key' => 'payments', 'label' => 'Payments'],
                ['key' => 'payment_methods', 'label' => 'Payment Methods'],
                ['key' => 'reports', 'label' => 'Reports'],
            ],
        ],
        [
            'key' => 'attendance',
            'title' => 'HR & Attendance',
            'description' => 'Check-in, leaves, holidays, and salary slips.',
            'icon' => 'clock',
            'children' => [
                ['key' => 'my-daily-attendance', 'label' => 'My Daily Attendance'],
                ['key' => 'apply-leave', 'label' => 'Apply Leave'],
                ['key' => 'staff-daily-attendance', 'label' => 'Staff Attendance', 'admin_only' => true],
                ['key' => 'leave-approvals', 'label' => 'Leave Approvals', 'admin_only' => true],
                ['key' => 'salary-slips', 'label' => 'Salary Slips', 'admin_only' => true],
                ['key' => 'office-settings', 'label' => 'Office Settings', 'admin_only' => true],
                ['key' => 'holidays', 'label' => 'Holidays', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'settings',
            'title' => 'Settings',
            'description' => 'Users and access control.',
            'icon' => 'settings',
            'children' => [
                ['key' => 'users', 'label' => 'Manage Users', 'admin_only' => true],
            ],
        ],
    ],

];
