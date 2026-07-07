# Configurator Engine

Kroki, atrybuty, wartości, kolekcje, zależności, dynamiczne renderowanie formularza.

## Model danych

| Encja | Opis |
|-------|------|
| **Step** | Krok formularza (pozycja, nazwa), przypisany do produktu |
| **Attribute** | Pole w kroku: `key`, `type`, `is_required`, opcje własne lub z kolekcji |
| **AttributeCollection** | Współdzielony zestaw opcji (np. kolory) używany przez wiele atrybutów |
| **AttributeValue** | Opcja select/multiselect — należy do atrybutu **lub** kolekcji (dokładnie jeden właściciel) |
| **Dependency** | Reguła: gdy źródło spełnia warunek → akcja na atrybucie docelowym |

### Typy atrybutów (`AttributeType`)

`text`, `number`, `boolean`, `select`, `multiselect`

### Walidacja wartości (API `validate`)

- **text** — string
- **number** — `is_numeric()` (int, float lub string numeryczny, np. `120`, `199.99`, `"120"`)
- **boolean** — `true`/`false`/`0`/`1`/`"0"`/`"1"`
- **select** / **multiselect** — wartość musi być jedną z dozwolonych opcji ze schematu

## Zależności (Dependency)

### Warunki (`DependencyCondition`)

| Warunek | Znaczenie |
|---------|-----------|
| `equals` | Wartość źródła równa `condition_value` (dla multiselect: tablica **zawiera** wartość) |
| `not_equals` | Odwrotnie do `equals` |
| `is_set` | Wartość ustawiona (nie `null`, nie `""`, nie `[]`) |
| `is_empty` | Puste: `null`, `""` lub `[]` |
| `is_not_set` | Tylko `null` (np. `false` i `0` uznawane za ustawione) |

### Akcje (`DependencyAction`) — semantyka ewaluacji

Ewaluacja odbywa się w **dwóch fazach** (kolejność reguł wg `position`):

#### Faza 1 — inicjalizacja akcji `show`

Wszystkie atrybuty będące **celem** reguły z akcją `show` startują jako **ukryte** (`visible: false`).

Pozostałe atrybuty startują jako widoczne (`visible: true`).

> Wzorzec „pokaż pole B, gdy A = X”: pole B jest domyślnie ukryte i pojawia się dopiero po spełnieniu warunku.

#### Faza 2 — dopasowanie warunków

Dla każdej reguły (w kolejności `position`): jeśli warunek na atrybucie źródłowym pasuje, stosowana jest akcja na celu.

| Akcja | Efekt | Odwracalność |
|-------|-------|--------------|
| **show** | `visible = true` | Tak — późniejsza reguła `hide` może ukryć ponownie |
| **hide** | `visible = false` | Tak — późniejsza reguła `show` może pokazać ponownie |
| **require** | `required = true` | **Nie** — brak akcji „unrequire”; raz wymagane pozostaje wymagane |
| **disable** | `disabled = true` | **Nie** — brak akcji „enable”; raz zablokowane pozostaje zablokowane |

Przy **konflikcie reguł na tym samym celu** wygrywa reguła z **wyższym** `position` (późniejsza w kolejności).

### Walidacja po ewaluacji

- Ukryte atrybuty (`visible: false`) — wartość w `selection` powoduje błąd `not_applicable`
- Wymagane i widoczne — brak wartości → `required`
- Zablokowane i widoczne — wartość ustawiona → `disabled`
- Nieznany klucz w `selection` → `unknown_attribute`

## API (SPA)

Wszystkie endpointy: `auth:sanctum` + `verified`.

| Metoda | URL | Opis |
|--------|-----|------|
| `GET` | `/api/products/{productId}/configurator/schema` | Schemat formularza (kroki, atrybuty, opcje) |
| `POST` | `/api/products/{productId}/configurator/evaluate` | Ewaluacja stanu pól (`visible`, `required`, `disabled`) dla bieżącej selekcji |
| `POST` | `/api/products/{productId}/configurator/validate` | Walidacja biznesowa selekcji |

`productId` i klucze w `selection` to **ULID** (`public_id`), nigdy numeric id.

Body `evaluate` / `validate`:

```json
{
  "selection": {
    "01J...": "red",
    "01K...": 120
  }
}
```

## Admin (Filament)

Zagnieżdżone zasoby pod produktem konfigurowalnym: kroki, kolekcje, atrybuty, wartości, zależności.

Dostęp: role `admin`, `manager` (`ConfiguratorManagementPolicy`).
