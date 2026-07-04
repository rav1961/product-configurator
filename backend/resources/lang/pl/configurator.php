<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Konfigurator',
        'steps' => 'Kroki',
        'attributes' => 'Atrybuty',
        'collections' => 'Kolekcje',
        'values' => 'Wartości',
        'dependencies' => 'Zależności',
    ],
    'label' => [
        'step' => [
            'singular' => 'Krok',
            'plural' => 'Kroki',
        ],
        'attribute' => [
            'singular' => 'Atrybut',
            'plural' => 'Atrybuty',
        ],
    ],
    'fields' => [
        'name' => 'Nazwa',
        'key' => 'Klucz',
        'position' => 'Pozycja',
        'type' => 'Typ',
        'is_required' => 'Wymagany',
        'collection' => 'Kolekcja opcji',
        'collection_help' => 'Opcjonalnie: współdzielona lista wartości zamiast własnych opcji atrybutu.',
        'actions' => 'Akcje',
    ],
    'actions' => [
        'edit_step' => 'Edytuj krok',
        'delete_step' => 'Usuń krok',
        'edit_attribute' => 'Edytuj atrybut',
        'delete_attribute' => 'Usuń atrybut',
    ],
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
