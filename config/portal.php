<?php

return [

    'roles' => [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'hr' => 'HR',
        'accounts' => 'Accounts',
        'staff' => 'Staff',
        'client' => 'Client',
        'employer' => 'Employer',
    ],

    /*
    | Private client files (storage/app/private via local disk).
    | max_kilobytes = 5120 → 5 MB per stored file.
    | JPG/PNG are compressed after upload to fit this limit.
    */
    'client_files' => [
        'max_kilobytes' => 5120,
        'mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
        'photo_mimes' => ['jpg', 'jpeg', 'png'],
    ],

    /*
    | Modules and features used for sidebar + permissions.
    | access on user_permissions: view | manage
    | admin_only children are not granted to staff via permission UI later.
    */
    'modules' => [
        [
            'key' => 'visitors',
            'title' => 'Daily Visitors',
            'description' => 'Office walk-in log.',
            'icon' => 'handshake',
            'children' => [
                ['key' => 'list', 'label' => 'Visitors', 'icon' => 'users'],
                ['key' => 'reminders', 'label' => 'Reminders', 'icon' => 'bell'],
            ],
        ],
        [
            'key' => 'operations',
            'title' => 'Operations',
            'description' => 'Care offs, companies, universities, countries, trades, and process statuses.',
            'icon' => 'briefcase',
            'children' => [
                ['key' => 'care_offs', 'label' => 'Care Offs', 'icon' => 'handshake'],
                ['key' => 'companies', 'label' => 'Companies', 'icon' => 'building'],
                ['key' => 'universities', 'label' => 'Universities', 'icon' => 'graduation'],
                ['key' => 'countries', 'label' => 'Countries', 'icon' => 'globe'],
                ['key' => 'trades', 'label' => 'Trades', 'icon' => 'wrench'],
                ['key' => 'process_statuses', 'label' => 'Process Statuses', 'icon' => 'trend'],
            ],
        ],
        [
            'key' => 'clients',
            'title' => 'Clients',
            'description' => 'Job-seekers, documents, and case status.',
            'icon' => 'users',
            'children' => [
                ['key' => 'create', 'label' => 'Add Client', 'icon' => 'user-plus'],
                ['key' => 'list', 'label' => 'All Clients', 'icon' => 'list'],
                ['key' => 'batches', 'label' => 'Batches', 'icon' => 'badge'],
                ['key' => 'documents', 'label' => 'Documents', 'icon' => 'document'],
                ['key' => 'import', 'label' => 'Excel Import', 'icon' => 'upload', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'attendance',
            'title' => 'Attendance for Staff',
            'description' => 'Check-in, attendance, holidays, and office settings.',
            'icon' => 'clock',
            'children' => [
                ['key' => 'my-daily-attendance', 'label' => 'My Daily Attendance', 'icon' => 'clock'],
                ['key' => 'my-loans', 'label' => 'My Loans', 'icon' => 'wallet'],
                ['key' => 'apply-leave', 'label' => 'Apply Leave', 'icon' => 'calendar'],
                ['key' => 'staff-daily-attendance', 'label' => 'Staff Attendance', 'icon' => 'badge', 'admin_only' => true],
                ['key' => 'leave-approvals', 'label' => 'Leave Approvals', 'icon' => 'check', 'admin_only' => true],
                ['key' => 'salary-slips', 'label' => 'Salary Slips', 'icon' => 'document', 'admin_only' => true],
                ['key' => 'staff-loans', 'label' => 'Staff Loans', 'icon' => 'wallet', 'admin_only' => true],
                ['key' => 'office-settings', 'label' => 'Office Settings', 'icon' => 'settings', 'admin_only' => true],
                ['key' => 'holidays', 'label' => 'Holidays', 'icon' => 'sun', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'accounts',
            'title' => 'Accounts',
            'description' => 'Payment methods, payments, and reports.',
            'icon' => 'wallet',
            'children' => [
                ['key' => 'payments', 'label' => 'Payments', 'icon' => 'wallet'],
                ['key' => 'payment_methods', 'label' => 'Payment Methods', 'icon' => 'settings'],
                ['key' => 'reports', 'label' => 'Reports', 'icon' => 'chart'],
            ],
        ],
        [
            'key' => 'employers',
            'title' => 'Employers',
            'description' => 'Approve employer accounts and job posts.',
            'icon' => 'building',
            'children' => [
                ['key' => 'approvals', 'label' => 'Employer Approvals', 'icon' => 'shield', 'admin_only' => true],
                ['key' => 'job-approvals', 'label' => 'Job Approvals', 'icon' => 'briefcase', 'admin_only' => true],
            ],
        ],
        [
            'key' => 'settings',
            'title' => 'Settings',
            'description' => 'Users and access control.',
            'icon' => 'settings',
            'children' => [
                ['key' => 'users', 'label' => 'Manage Users', 'icon' => 'users', 'admin_only' => true],
            ],
        ],
    ],

];
