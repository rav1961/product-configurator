# Rule Engine

Logika biznesowa konfiguracji produktu: grupy warunków → akcje (ceny, wykluczenia, komunikaty).
Oddzielone od **Dependency** w Configuratorze (stan pól UI: `show` / `hide` / `require` / `disable`).

## Model danych

```
Product
└── Rule[]                          groups_match_mode: all | any
    ├── RuleGroup[]                 conditions_match_mode: all | any
    │   └── RuleCondition[]         source_attribute → SelectionCondition (Shared)
    └── RuleAction[]                type + payload (JSON)
```

| Encja | Opis |
|-------|------|
| **Rule** | Reguła per produkt: `name`, `description?`, `groups_match_mode`, `position`, `is_active` |
| **RuleGroup** | Grupa warunków w regule: `conditions_match_mode`, `position` |
| **RuleCondition** | Warunek na atrybucie źródłowym: `condition`, `condition_value?`, `position` |
| **RuleAction** | Akcja gdy reguła pasuje: `type`, `payload`, `position` |

Prefiks tabel: `rules_engine_*`. Warunki używają `SelectionCondition` z Shared (jak `Dependency`).

## Semantyka dopasowania

| Poziom | Pole | `all` | `any` |
|--------|------|-------|-------|
| Warunki w grupie | `RuleGroup.conditions_match_mode` | Wszystkie warunki muszą pasować | Wystarczy jeden |
| Grupy w regule | `Rule.groups_match_mode` | Wszystkie grupy muszą pasować | Wystarczy jedna |

- Grupa **bez warunków** → nie pasuje.
- Reguła **bez grup** → nie pasuje.
- Reguła `is_active = false` → pomijana przy ewaluacji.
- Wszystkie **pasujące** reguły (wg `position`) zwracają efekty; precedencja wielu `set_override` → moduł Pricing.

## Typy akcji (`RuleActionType`)

| Typ | Payload | Efekt (dla SPA / Pricing) |
|-----|---------|---------------------------|
| `add_modifier` | `{ "amount": "199.99", "label": "..."? }` | Dopłata do ceny bazowej |
| `set_override` | `{ "amount": "2499.00" }` | Nadpisanie ceny końcowej |
| `exclude_option` | `{ "attribute_id": "<ULID>", "value": "glass" }` | Wykluczenie opcji (SPA filtruje) |
| `add_message` | `{ "level": "info"\|"warning"\|"error", "message": "..." }` | Komunikat dla użytkownika |

Rule Engine **nie liczy ceny** — zwraca `effects`; moduł Pricing (następny) je interpretuje.

## API (SPA)

Middleware: `auth:sanctum` + `verified` (`ApiRouteMiddleware::VERIFIED`).

| Metoda | URL | Opis |
|--------|-----|------|
| `POST` | `/api/products/{productId}/rules/evaluate` | Ewaluacja reguł dla bieżącej `selection` |

`productId` i klucze w `selection` to **ULID** (`public_id`).

Body:

```json
{
  "selection": {
    "01J...": "glass",
    "01K...": 140
  }
}
```

Response **200**:

```json
{
  "data": {
    "productId": "01J...",
    "matchedRules": [
      { "id": "01K...", "name": "Dopłata za szkło", "position": 0 }
    ],
    "effects": {
      "modifiers": [
        { "ruleId": "01K...", "amount": "450.00", "label": "Blat szklany", "position": 0 }
      ],
      "overrides": [],
      "excludedOptions": [],
      "messages": [
        { "ruleId": "01K...", "level": "warning", "message": "...", "position": 1 }
      ]
    }
  }
}
```

Brak dopasowanych reguł → puste tablice (nie błąd).

Produkt musi być aktywny i `is_configurable = true` (jak endpointy Configuratora).

## Integracja z Configurator

```text
SPA → POST .../configurator/evaluate   → visible / required / disabled
SPA → POST .../rules/evaluate          → matchedRules + effects
SPA → (Pricing)                        → cena końcowa z effects
```

Osobne endpointy — czysta granica modułów; SPA może wołać równolegle.

## Admin (Filament)

Na produkcie konfigurowalnym: zakładka **Reguły** → edycja reguły → **Grupy warunków** (warunki) + **Akcje**.

Dostęp: role `admin`, `manager` (`RuleManagementPolicy`).

Zagnieżdżone resources: `Product → Rule → RuleGroup`. Przy `Resource::getUrl()` zawsze przekazuj **wszystkie** parametry rodziców (`product`, `rule`, `record`) — wzorzec jak `AttributesRelationManager` w Configuratorze. `shouldGuessMissingParameters` nie wystarcza przy 3 poziomach.

W tabelach Filament nie typuj `$state` w `formatStateUsing` dla castów JSON/enum — używaj `getStateUsing(fn (Model $record) => $record->field)` (cast Eloquent).

Rejestracja zasobów Filament w `RulesEngineServiceProvider::register()` (nie `boot()`), analogicznie do Configuratora.

## Demo data

Reguły demo w `config/demo-catalog.php` (sekcja `configuration.rules`); seed: `DemoRulesSeeder` (po `DemoConfiguratorSeeder`).

Przykład *Biurko Nova Pro*: blat szklany → dopłata + ostrzeżenie.
