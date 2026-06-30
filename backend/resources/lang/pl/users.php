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
    'mail' => [
        'verify' => [
            'subject' => 'Weryfikacja adresu e-mail',
            'line1' => 'Kliknij poniższy przycisk, aby zweryfikować swój adres e-mail.',
            'action' => 'Zweryfikuj adres e-mail',
            'line2' => 'Link wygaśnie za :count minut.',
        ],
        'reset' => [
            'subject' => 'Resetowanie hasła',
            'line1' => 'Otrzymujesz tę wiadomość, ponieważ otrzymaliśmy prośbę o reset hasła do Twojego konta.',
            'action' => 'Resetuj hasło',
            'line2' => 'Link do resetowania hasła wygaśnie za :count minut.',
            'line3' => 'Jeśli to nie Ty prosiłeś o reset hasła, zignoruj tę wiadomość.',
        ],
    ],
];
