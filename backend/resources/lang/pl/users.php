<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Access',
        'label' => 'Users',
    ],
    'label' => [
        'singular' => 'User',
        'plural' => 'Users',
    ],
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'roles' => 'Roles',
        'email_verified_at' => 'Verified',
        'created_at' => 'Created at',
    ],
    'role' => [
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'sales' => 'Sales',
        'customer' => 'Customer',
    ],
];
