<?php

declare(strict_types=1);

return [
    'attribute_type' => [
        'text' => 'Tekst',
        'number' => 'Liczba',
        'boolean' => 'Tak/Nie',
        'select' => 'Lista wyboru',
        'multiselect' => 'Wielokrotny wybór',
    ],
    'dependency_condition' => [
        'equals' => 'Równa się',
        'not_equals' => 'Nie równa się',
        'is_set' => 'Jest ustawione',
        'is_empty' => 'Jest puste',
    ],
    'dependency_action' => [
        'show' => 'Pokaż',
        'hide' => 'Ukryj',
        'require' => 'Wymagaj',
        'disable' => 'Wyłącz',
    ],
];
