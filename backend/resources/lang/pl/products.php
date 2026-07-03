<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Katalog',
        'label' => 'Produkty',
    ],
    'label' => [
        'singular' => 'Produkt',
        'plural' => 'Produkty',
    ],
    'fields' => [
        'category' => 'Kategoria',
        'name' => 'Nazwa',
        'slug' => 'Slug',
        'sku' => 'SKU',
        'status' => 'Status',
        'is_configurable' => 'Konfigurowalny',
        'is_configurable_help' => 'Produkt dostępny w konfiguratorze SPA. Wymaga osobnej konfiguracji kroków i atrybutów (moduł Configurator).',
        'description' => 'Opis',
        'position' => 'Pozycja',
        'updated_at' => 'Zaktualizowano',
        'cover' => 'Okładka',
    ],
    'status' => [
        'draft' => 'Szkic',
        'active' => 'Aktywny',
        'archived' => 'Zarchiwizowany',
    ],
    'cover' => 'Okładka',
];
