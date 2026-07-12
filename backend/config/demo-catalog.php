<?php

declare(strict_types=1);

return [
    'categories' => [
        [
            'name' => 'Meble biurowe',
            'slug' => 'meble-biurowe',
            'description' => 'Biurka, krzesła i szafy do nowoczesnego workspace. Wszystkie modele można dopasować pod wymiary i wykończenie.',
            'position' => 1,
            'image_seed' => 'demo-cat-office',
            'products' => [
                [
                    'name' => 'Biurko Nova Pro',
                    'slug' => 'biurko-nova-pro',
                    'sku' => 'DEMO-OFF-001',
                    'description' => 'Regulowane biurko z blatem kompozytowym lub szklanym. Idealne do pracy stojąco-siedząco. Opcjonalny przelot kablowy i organizer.',
                    'position' => 1,
                    'image_seed' => 'demo-desk-nova',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Wymiary',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Szerokość blatu',
                                        'key' => 'width',
                                        'type' => 'number',
                                        'position' => 0,
                                        'is_required' => true,
                                    ],
                                    [
                                        'name' => 'Głębokość blatu',
                                        'key' => 'depth',
                                        'type' => 'number',
                                        'position' => 1,
                                        'is_required' => true,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Wykończenie',
                                'position' => 1,
                                'attributes' => [
                                    [
                                        'name' => 'Materiał blatu',
                                        'key' => 'top_material',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Kompozyt', 'value' => 'composite', 'is_default' => true],
                                            ['label' => 'Szkło', 'value' => 'glass'],
                                            ['label' => 'Dąb', 'value' => 'oak'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Kolor konstrukcji',
                                        'key' => 'frame_color',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Czarny', 'value' => 'black', 'is_default' => true],
                                            ['label' => 'Srebrny', 'value' => 'silver'],
                                            ['label' => 'Biały', 'value' => 'white'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Wykończenie krawędzi',
                                        'key' => 'edge_finish',
                                        'type' => 'select',
                                        'position' => 2,
                                        'is_required' => false,
                                        'options' => [
                                            ['label' => 'Faza 2 mm', 'value' => 'chamfer_2mm', 'is_default' => true],
                                            ['label' => 'Zaoblona', 'value' => 'rounded'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Przelot kablowy',
                                        'key' => 'cable_tray',
                                        'type' => 'boolean',
                                        'position' => 3,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [
                            [
                                'source' => 'top_material',
                                'target' => 'edge_finish',
                                'condition' => 'equals',
                                'condition_value' => 'glass',
                                'action' => 'show',
                                'position' => 0,
                            ],
                        ],
                        'rules' => [
                            [
                                'name' => 'Dopłata za blat szklany',
                                'description' => 'Dopłata materiałowa i komunikat przy wyborze szkła hartowanego.',
                                'groups_match_mode' => 'all',
                                'position' => 0,
                                'is_active' => true,
                                'groups' => [
                                    [
                                        'conditions_match_mode' => 'all',
                                        'position' => 0,
                                        'conditions' => [
                                            [
                                                'source' => 'top_material',
                                                'condition' => 'equals',
                                                'condition_value' => 'glass',
                                                'position' => 0,
                                            ],
                                        ],
                                    ],
                                ],
                                'actions' => [
                                    [
                                        'type' => 'add_modifier',
                                        'payload' => [
                                            'amount' => '450.00',
                                            'label' => 'Blat szklany hartowany',
                                        ],
                                        'position' => 0,
                                    ],
                                    [
                                        'type' => 'add_message',
                                        'payload' => [
                                            'level' => 'warning',
                                            'message' => 'Blat szklany wydłuża czas realizacji o ok. 2 tygodnie.',
                                        ],
                                        'position' => 1,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Organizer kablowy',
                                'description' => 'Dopłata za przelot kablowy.',
                                'groups_match_mode' => 'any',
                                'position' => 1,
                                'is_active' => true,
                                'groups' => [
                                    [
                                        'conditions_match_mode' => 'all',
                                        'position' => 0,
                                        'conditions' => [
                                            [
                                                'source' => 'cable_tray',
                                                'condition' => 'is_set',
                                                'condition_value' => null,
                                                'position' => 0,
                                            ],
                                        ],
                                    ],
                                ],
                                'actions' => [
                                    [
                                        'type' => 'add_modifier',
                                        'payload' => [
                                            'amount' => '89.00',
                                            'label' => 'Przelot kablowy',
                                        ],
                                        'position' => 0,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Krzesło Aero',
                    'slug' => 'krzeslo-aero',
                    'sku' => 'DEMO-OFF-002',
                    'description' => 'Krzesło biurowe z regulacją wysokości, oparciem siatkowym i wyborem tapicerki z kolekcji tkanin premium.',
                    'position' => 2,
                    'image_seed' => 'demo-chair-aero',
                    'configuration' => [
                        'collections' => [
                            [
                                'key' => 'fabrics',
                                'name' => 'Tkaniny',
                                'position' => 0,
                                'values' => [
                                    ['label' => 'Bawełna szara', 'value' => 'cotton_grey', 'position' => 0, 'is_default' => true],
                                    ['label' => 'Wełna granat', 'value' => 'wool_navy', 'position' => 1],
                                    ['label' => 'Skóra ekologiczna', 'value' => 'vegan_leather', 'position' => 2],
                                ],
                            ],
                        ],
                        'steps' => [
                            [
                                'name' => 'Tapicerka',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Tkanina',
                                        'key' => 'fabric',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'collection_key' => 'fabrics',
                                    ],
                                    [
                                        'name' => 'Kolor ramy',
                                        'key' => 'frame_color',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Grafit', 'value' => 'graphite', 'is_default' => true],
                                            ['label' => 'Chrom', 'value' => 'chrome'],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Opcje',
                                'position' => 1,
                                'attributes' => [
                                    [
                                        'name' => 'Podłokietniki',
                                        'key' => 'armrests',
                                        'type' => 'boolean',
                                        'position' => 0,
                                        'is_required' => false,
                                    ],
                                    [
                                        'name' => 'Podparcie lędźwi',
                                        'key' => 'lumbar_support',
                                        'type' => 'boolean',
                                        'position' => 1,
                                        'is_required' => false,
                                    ],
                                    [
                                        'name' => 'Uwagi do zamówienia',
                                        'key' => 'notes',
                                        'type' => 'text',
                                        'position' => 2,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                    ],
                ],
                [
                    'name' => 'Szafka aktowa Classic',
                    'slug' => 'szafka-aktowa-classic',
                    'sku' => 'DEMO-OFF-003',
                    'description' => 'Szafka na dokumenty z czterema szufladami. Opcja zamka i systemu antyprzewróceniowego.',
                    'position' => 3,
                    'image_seed' => 'demo-cabinet-classic',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Konfiguracja',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Wysokość',
                                        'key' => 'height',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => '120 cm', 'value' => '120', 'is_default' => true],
                                            ['label' => '140 cm', 'value' => '140'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Kolor',
                                        'key' => 'color',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Biały', 'value' => 'white', 'is_default' => true],
                                            ['label' => 'Antracyt', 'value' => 'anthracite'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Zamek',
                                        'key' => 'lock',
                                        'type' => 'boolean',
                                        'position' => 2,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Stoły i krzesła',
            'slug' => 'stoly-i-krzesla',
            'description' => 'Stoły konferencyjne, jadalniane i krzesła do przestrzeni spotkań oraz domowych jadalni.',
            'position' => 2,
            'image_seed' => 'demo-cat-dining',
            'products' => [
                [
                    'name' => 'Stół konferencyjny Round',
                    'slug' => 'stol-konferencyjny-round',
                    'sku' => 'DEMO-DIN-001',
                    'description' => 'Okrągły stół konferencyjny z wyborem średnicy i wykończenia blatu. Nogi w stylu industrial lub minimalistycznym.',
                    'position' => 1,
                    'image_seed' => 'demo-table-round',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Rozmiar',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Średnica',
                                        'key' => 'diameter',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => '120 cm', 'value' => '120', 'is_default' => true],
                                            ['label' => '150 cm', 'value' => '150'],
                                            ['label' => '180 cm', 'value' => '180'],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Wygląd',
                                'position' => 1,
                                'attributes' => [
                                    [
                                        'name' => 'Blat',
                                        'key' => 'top',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Orzech', 'value' => 'walnut', 'is_default' => true],
                                            ['label' => 'Dąb bielony', 'value' => 'whitened_oak'],
                                            ['label' => 'Beton', 'value' => 'concrete'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Nogi',
                                        'key' => 'legs',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Industrialne', 'value' => 'industrial', 'is_default' => true],
                                            ['label' => 'Minimalistyczne', 'value' => 'minimal'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                    ],
                ],
                [
                    'name' => 'Stół jadalny Oak Line',
                    'slug' => 'stol-jadalny-oak-line',
                    'sku' => 'DEMO-DIN-002',
                    'description' => 'Rodzinny stół z litego drewna. Regulowana długość i opcjonalna dokładka (leaf).',
                    'position' => 2,
                    'image_seed' => 'demo-table-oak',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Wymiary',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Długość',
                                        'key' => 'length',
                                        'type' => 'number',
                                        'position' => 0,
                                        'is_required' => true,
                                    ],
                                    [
                                        'name' => 'Dokładka',
                                        'key' => 'extension_leaf',
                                        'type' => 'boolean',
                                        'position' => 1,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Wykończenie',
                                'position' => 1,
                                'attributes' => [
                                    [
                                        'name' => 'Olejowanie',
                                        'key' => 'oil_finish',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Naturalne', 'value' => 'natural', 'is_default' => true],
                                            ['label' => 'Ciemne', 'value' => 'dark'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Kolor dokładki',
                                        'key' => 'leaf_color',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => false,
                                        'options' => [
                                            ['label' => 'Dopasowany', 'value' => 'matched', 'is_default' => true],
                                            ['label' => 'Kontrast', 'value' => 'contrast'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [
                            [
                                'source' => 'extension_leaf',
                                'target' => 'leaf_color',
                                'condition' => 'equals',
                                'condition_value' => '1',
                                'action' => 'show',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Krzesło tapicerowane Lounge',
                    'slug' => 'krzeslo-tapicerowane-lounge',
                    'sku' => 'DEMO-DIN-003',
                    'description' => 'Wygodne krzesło do jadalni z wyborem tkaniny i opcjonalnym przeszyciem guzikowym.',
                    'position' => 3,
                    'image_seed' => 'demo-chair-lounge',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Tapicerka',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Kolor',
                                        'key' => 'color',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Beż', 'value' => 'beige', 'is_default' => true],
                                            ['label' => 'Zieleń butelkowa', 'value' => 'bottle_green'],
                                            ['label' => 'Granat', 'value' => 'navy'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Przeszycie guzikowe',
                                        'key' => 'tufting',
                                        'type' => 'boolean',
                                        'position' => 1,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Przechowywanie',
            'slug' => 'przechowywanie',
            'description' => 'Regały modułowe, szafy i komody — konfiguracja wymiarów, frontów i akcesoriów wewnętrznych.',
            'position' => 3,
            'image_seed' => 'demo-cat-storage',
            'products' => [
                [
                    'name' => 'Regał modułowy Flex',
                    'slug' => 'regal-modulowy-flex',
                    'sku' => 'DEMO-STR-001',
                    'description' => 'System regałów łączonych w linie. Wybierz liczbę modułów i wysokość półek.',
                    'position' => 1,
                    'image_seed' => 'demo-shelf-flex',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Układ',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Liczba modułów',
                                        'key' => 'modules',
                                        'type' => 'number',
                                        'position' => 0,
                                        'is_required' => true,
                                    ],
                                    [
                                        'name' => 'Wysokość półek',
                                        'key' => 'shelf_height',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Standardowa', 'value' => 'standard', 'is_default' => true],
                                            ['label' => 'Wysoka', 'value' => 'tall'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                    ],
                ],
                [
                    'name' => 'Szafa biurowa Prime',
                    'slug' => 'szafa-biurowa-prime',
                    'sku' => 'DEMO-STR-002',
                    'description' => 'Szafa dwudrzwiowa z opcją wnętrza biurowego lub archiwalnego.',
                    'position' => 2,
                    'image_seed' => 'demo-wardrobe-prime',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Front',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Szerokość',
                                        'key' => 'width',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => '100 cm', 'value' => '100', 'is_default' => true],
                                            ['label' => '120 cm', 'value' => '120'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Typ drzwi',
                                        'key' => 'doors',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Pełne', 'value' => 'solid', 'is_default' => true],
                                            ['label' => 'Przesuwne', 'value' => 'sliding'],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Wnętrze',
                                'position' => 1,
                                'attributes' => [
                                    [
                                        'name' => 'Układ wnętrza',
                                        'key' => 'interior',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Biurowy', 'value' => 'office', 'is_default' => true],
                                            ['label' => 'Archiwalny', 'value' => 'archive'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Szuflady wewnętrzne',
                                        'key' => 'inner_drawers',
                                        'type' => 'boolean',
                                        'position' => 1,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [
                            [
                                'source' => 'interior',
                                'target' => 'inner_drawers',
                                'condition' => 'equals',
                                'condition_value' => 'office',
                                'action' => 'show',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Komoda Nordic',
                    'slug' => 'komoda-nordic',
                    'sku' => 'DEMO-STR-003',
                    'description' => 'Komoda w stylu skandynawskim z wyborem liczby szuflad i wykończenia uchwytów.',
                    'position' => 3,
                    'image_seed' => 'demo-sideboard-nordic',
                    'configuration' => [
                        'steps' => [
                            [
                                'name' => 'Konfiguracja',
                                'position' => 0,
                                'attributes' => [
                                    [
                                        'name' => 'Liczba szuflad',
                                        'key' => 'drawers',
                                        'type' => 'select',
                                        'position' => 0,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => '3', 'value' => '3', 'is_default' => true],
                                            ['label' => '4', 'value' => '4'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Uchwyty',
                                        'key' => 'handles',
                                        'type' => 'select',
                                        'position' => 1,
                                        'is_required' => true,
                                        'options' => [
                                            ['label' => 'Drewniane', 'value' => 'wood', 'is_default' => true],
                                            ['label' => 'Metal czarny', 'value' => 'black_metal'],
                                        ],
                                    ],
                                    [
                                        'name' => 'Domykanie miękkie',
                                        'key' => 'soft_close',
                                        'type' => 'boolean',
                                        'position' => 2,
                                        'is_required' => false,
                                    ],
                                ],
                            ],
                        ],
                        'dependencies' => [],
                        'rules' => [
                            [
                                'name' => 'Dopłata za 4 szuflady',
                                'description' => 'Większa komoda — dopłata za dodatkową szufladę.',
                                'groups_match_mode' => 'all',
                                'position' => 0,
                                'is_active' => true,
                                'groups' => [
                                    [
                                        'conditions_match_mode' => 'all',
                                        'position' => 0,
                                        'conditions' => [
                                            [
                                                'source' => 'drawers',
                                                'condition' => 'equals',
                                                'condition_value' => '4',
                                                'position' => 0,
                                            ],
                                        ],
                                    ],
                                ],
                                'actions' => [
                                    [
                                        'type' => 'add_modifier',
                                        'payload' => [
                                            'amount' => '120.00',
                                            'label' => 'Dodatkowa szuflada',
                                        ],
                                        'position' => 0,
                                    ],
                                ],
                            ],
                            [
                                'name' => 'Domykanie miękkie',
                                'description' => 'Dopłata za mechanizm soft-close.',
                                'groups_match_mode' => 'all',
                                'position' => 1,
                                'is_active' => true,
                                'groups' => [
                                    [
                                        'conditions_match_mode' => 'all',
                                        'position' => 0,
                                        'conditions' => [
                                            [
                                                'source' => 'soft_close',
                                                'condition' => 'is_set',
                                                'condition_value' => null,
                                                'position' => 0,
                                            ],
                                        ],
                                    ],
                                ],
                                'actions' => [
                                    [
                                        'type' => 'add_modifier',
                                        'payload' => [
                                            'amount' => '199.00',
                                            'label' => 'Domykanie miękkie',
                                        ],
                                        'position' => 0,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
