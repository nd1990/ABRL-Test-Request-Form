<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission modules
    |--------------------------------------------------------------------------
    | Central registry of every module and its assignable permissions.
    | The 'view' key is the permission that unlocks the module in the sidebar
    | and (together with the per-action keys) protects the admin routes.
    */

    'modules' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'view' => 'dashboard.view',
            'permissions' => [
                'dashboard.view' => 'View dashboard',
            ],
        ],

        'quotations' => [
            'label' => 'Quotations',
            'view' => 'quotations.view',
            'permissions' => [
                'quotations.view' => 'View quotations',
                'quotations.view_accepted' => 'View accepted quotations',
                'quotations.create' => 'Create quotations',
                'quotations.edit' => 'Edit quotations',
                'quotations.delete' => 'Delete quotations',
                'quotations.send_email' => 'Send quotation emails',
            ],
        ],

        'invoices' => [
            'label' => 'Invoices',
            'view' => 'invoices.view',
            'permissions' => [
                'invoices.view' => 'View invoices',
                'invoices.create' => 'Create invoices',
                'invoices.edit' => 'Edit invoices',
                'invoices.delete' => 'Delete invoices',
                'invoices.send_email' => 'Send invoice emails',
            ],
        ],

        'services' => [
            'label' => 'Lab Tests',
            'view' => 'lab_tests.view',
            'permissions' => [
                'lab_tests.view' => 'View lab tests',
                'lab_tests.create' => 'Add & import lab tests',
                'lab_tests.edit' => 'Edit lab tests',
                'lab_tests.delete' => 'Delete lab tests',
            ],
        ],

        'users' => [
            'label' => 'Manage Users',
            'view' => 'users.view',
            'permissions' => [
                'users.view' => 'View users',
                'users.create' => 'Create users',
                'users.edit' => 'Edit users',
                'users.delete' => 'Delete users',
            ],
        ],

        'backups' => [
            'label' => 'Backups',
            'view' => 'backups.view',
            'permissions' => [
                'backups.view' => 'View backups',
                'backups.create' => 'Create backups & schedule',
                'backups.restore' => 'Restore backups',
                'backups.delete' => 'Delete backups',
            ],
        ],

        'settings' => [
            'label' => 'Settings',
            'view' => 'settings.view',
            'permissions' => [
                'settings.view' => 'View settings',
                'settings.edit' => 'Edit settings',
            ],
        ],

        'public_site' => [
            'label' => 'Public Site',
            'view' => 'public_site.view',
            'permissions' => [
                'public_site.view' => 'View public site',
            ],
        ],
    ],

    // Flat list of every assignable permission key.
    'assignable' => [
        'dashboard.view',
        'quotations.view',
        'quotations.view_accepted',
        'quotations.create',
        'quotations.edit',
        'quotations.delete',
        'quotations.send_email',
        'invoices.view',
        'invoices.create',
        'invoices.edit',
        'invoices.delete',
        'invoices.send_email',
        'lab_tests.view',
        'lab_tests.create',
        'lab_tests.edit',
        'lab_tests.delete',
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'backups.view',
        'backups.create',
        'backups.restore',
        'backups.delete',
        'settings.view',
        'settings.edit',
        'public_site.view',
    ],

];