<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Roles
    |--------------------------------------------------------------------------
    |
    | Define all available roles and their display names.
    | To add a new role, add it to the 'all' array and define its permissions.
    |
    */

    'default' => env('DEFAULT_ROLE', 'employee'),

    'all' => [
        'admin'       => 'مدير النظام',
        'manager'     => 'مدير',
        'sales'       => 'مبيعات',
        'operations'  => 'عمليات',
        'inventory'   => 'مخزون',
        'employee'    => 'موظف',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | Each role maps to a list of permission keys.
    | Permission keys are used by the CheckRole middleware.
    |
    */
    'permissions' => [
        'admin' => ['*'],
        'manager' => ['sales.*', 'inventory.*', 'reports.*', 'expenses.*'],
        'sales' => ['sales.*', 'sales-orders.create', 'sales-orders.edit'],
        'operations' => ['sales-orders.view', 'inventory.products.view'],
        'inventory' => ['inventory.*'],
        'employee' => ['sales-orders.create'],
    ],
];
