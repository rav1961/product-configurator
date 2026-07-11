<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Reguły biznesowe',
        'rules' => 'Reguły',
        'groups' => 'Grupy warunków',
        'conditions' => 'Warunki',
        'actions' => 'Akcje',
    ],
    'label' => [
        'rule' => [
            'singular' => 'Reguła',
            'plural' => 'Reguły',
        ],
        'group' => [
            'singular' => 'Grupa',
            'plural' => 'Grupy',
        ],
    ],
    'fields' => [
        'name' => 'Nazwa',
        'description' => 'Opis',
        'groups_match_mode' => 'Łączenie grup',
        'conditions_match_mode' => 'Łączenie warunków',
        'position' => 'Pozycja',
        'is_active' => 'Aktywna',
        'source_attribute' => 'Atrybut źródłowy',
        'condition' => 'Warunek',
        'condition_value' => 'Wartość warunku',
        'type' => 'Typ akcji',
        'payload_amount' => 'Kwota',
        'payload_label' => 'Etykieta',
        'payload_attribute' => 'Atrybut',
        'payload_value' => 'Wartość opcji',
        'payload_level' => 'Poziom',
        'payload_message' => 'Komunikat',
        'actions' => 'Akcje',
    ],
    'actions' => [
        'edit_rule' => 'Edytuj regułę',
        'delete_rule' => 'Usuń regułę',
        'edit_group' => 'Edytuj grupę',
        'delete_group' => 'Usuń grupę',
        'edit_condition' => 'Edytuj warunek',
        'delete_condition' => 'Usuń warunek',
        'edit_action' => 'Edytuj akcję',
        'delete_action' => 'Usuń akcję',
    ],
    'message_level' => [
        'info' => 'Informacja',
        'warning' => 'Ostrzeżenie',
        'error' => 'Błąd',
    ],
    'match_mode' => [
        'all' => 'Wszystkie (AND)',
        'any' => 'Dowolny (OR)',
    ],
    'action_type' => [
        'add_modifier' => 'Dopłata',
        'set_override' => 'Nadpisanie ceny',
        'exclude_option' => 'Wykluczenie opcji',
        'add_message' => 'Komunikat',
    ],
];
