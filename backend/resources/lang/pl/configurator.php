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
        'collection' => [
            'singular' => 'Kolekcja',
            'plural' => 'Kolekcje',
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
        'label' => 'Etykieta',
        'value' => 'Wartość',
        'is_default' => 'Domyślna',
        'source_attribute' => 'Atrybut źródłowy',
        'target_attribute' => 'Atrybut docelowy',
        'condition' => 'Warunek',
        'condition_value' => 'Wartość warunku',
        'action' => 'Akcja',
    ],
    'actions' => [
        'edit_step' => 'Edytuj krok',
        'delete_step' => 'Usuń krok',
        'edit_attribute' => 'Edytuj atrybut',
        'delete_attribute' => 'Usuń atrybut',
        'edit_collection' => 'Edytuj kolekcję',
        'delete_collection' => 'Usuń kolekcję',
        'edit_value' => 'Edytuj wartość',
        'delete_value' => 'Usuń wartość',
        'edit_dependency' => 'Edytuj zależność',
        'delete_dependency' => 'Usuń zależność',
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
        'is_not_set' => 'Nie jest ustawione',
    ],
    'dependency_action' => [
        'show' => 'Pokaż',
        'hide' => 'Ukryj',
        'require' => 'Wymagaj',
        'disable' => 'Wyłącz',
    ],
];
