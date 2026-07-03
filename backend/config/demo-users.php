<?php

declare(strict_types=1);

return [
    'password' => env('DEMO_USERS_PASSWORD', 'password'),

    'users' => [
        'admin' => [
            'name' => env('ADMIN_NAME', 'Admin Demo'),
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ],
        'manager' => [
            'name' => env('DEMO_MANAGER_NAME', 'Manager Demo'),
            'email' => env('DEMO_MANAGER_EMAIL', 'manager@example.com'),
        ],
        'sales' => [
            'name' => env('DEMO_SALES_NAME', 'Sales Demo'),
            'email' => env('DEMO_SALES_EMAIL', 'sales@example.com'),
        ],
        'customer' => [
            'name' => env('DEMO_CUSTOMER_NAME', 'Customer Demo'),
            'email' => env('DEMO_CUSTOMER_EMAIL', 'customer@example.com'),
        ],
    ],
];
