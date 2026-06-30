<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Uprawnienia',
        'label' => 'Użytkownicy',
    ],
    'label' => [
        'singular' => 'Użytkownik',
        'plural' => 'Użytkownicy',
    ],
    'fields' => [
        'name' => 'Nazwa',
        'email' => 'Email',
        'password' => 'Hasło',
        'roles' => 'Role',
        'email_verified_at' => 'Zweryfikowany',
        'created_at' => 'Utworzono',
    ],
    'role' => [
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'sales' => 'Obsługa',
        'customer' => 'Klient',
    ],
];
