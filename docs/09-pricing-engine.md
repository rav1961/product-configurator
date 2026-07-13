# Pricing Engine

Cena bazowa + modyfikatory + nadpisania. Moduł **Pricing** liczy cenę końcową konfiguracji; modyfikatory i override pochodzą z **RulesEngine**.

## Porcja P0 — zakres

| W scope | Poza scope (YAGNI) |
|---------|-------------------|
| Moduł `Pricing` + `ProductPrice` + `PricingCalculator` | Ceny per atrybut / opcja |
| Cena bazowa per produkt (admin/manager) | VAT, waluty, cache |
| `POST .../price/calculate` dla SPA | Eventy domenowe |
| Integracja z `EvaluateRulesAction` | Breakdown modifierów w response (SPA ma `rules/evaluate`) |
| Demo seed (`base_price` w `demo-catalog.php`) | GET ceny bez `selection` |

## Model danych

```
Product (Catalog)
└── ProductPrice (Pricing)     1:1, UNIQUE product_id
    └── amount (int, grosze)   kolumna DB: amount
```

| Encja | Opis |
|-------|------|
| **ProductPrice** | Cena bazowa produktu: `amount` (int, grosze, `>= 0`) |

Prefiks tabel: `pricing_*`. Relacja na `Product` przez concern `InteractsWithPricing` (wzorzec jak `InteractsWithRules`).

Modyfikatory i `set_override` **nie są** w Pricing — konfiguruje je RulesEngine (`docs/08-rule-engine.md`).

## Logika kalkulacji (`PricingCalculator`)

```
1. basePrice ← ProductPrice.amount
2. effects ← EvaluateRulesAction(productId, selection)
3. Jeśli są overrides:
     total = override z najwyższym position (ostatni wygrywa)
     hasOverride = true
   W przeciwnym razie:
     total = basePrice + Σ modifier.operation.signedMinor(modifier.amount)
     hasOverride = false
4. total = max(0, total)    ← floor; duży rabat nie daje ujemnej wyceny
```

- Kolejność modifierów: wg `position` (rosnąco).
- Precedencja override: zgodnie z `docs/08-rule-engine.md` — wiele `set_override` → ostatni wg `position`.

## API (SPA)

Middleware: `auth:sanctum` + `verified` (`ApiRouteMiddleware::VERIFIED`).

| Metoda | URL | Opis |
|--------|-----|------|
| `POST` | `/api/products/{productId}/price/calculate` | Cena dla bieżącej `selection` |

`productId` i klucze w `selection` to **ULID** (`public_id`). Body — reuse `ConfigurationSelectionRequest` (jak Configurator / RulesEngine).

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
    "basePrice": 199900,
    "total": 244900,
    "hasOverride": false
  }
}
```

**Konwencja kwot w API Pricing:** `basePrice` i `total` to **int w groszach** (np. `199900` = 1999,00 PLN). Bez pól display (`"1999.00"`) — formatowanie po stronie SPA.

| Pole | Typ | Opis |
|------|-----|------|
| `productId` | string (ULID) | Produkt |
| `basePrice` | int | Cena bazowa w groszach |
| `total` | int | Cena końcowa w groszach |
| `hasOverride` | bool | Czy zastosowano `set_override` z RulesEngine |

Błędy:

| Status | Kiedy |
|--------|-------|
| **401** | Gość |
| **403** | Niezweryfikowany użytkownik |
| **404** | Nieznany / nieaktywny produkt |
| **422** | Produkt niekonfigurowalny; brak `ProductPrice`; błąd walidacji `selection` |

## Integracja z innymi modułami

```text
SPA → POST .../configurator/evaluate   → visible / required / disabled
SPA → POST .../rules/evaluate          → modifiers, overrides, messages
SPA → POST .../price/calculate         → basePrice + total
```

Osobne endpointy — czysta granica modułów. Szczegóły modifierów (etykiety, operacja +/-) SPA bierze z `rules/evaluate`, nie z Pricing.

`CalculatePriceAction` woła:

1. `GetConfigurableProductAction` — walidacja produktu
2. `ProductPriceRepository` — cena bazowa
3. `EvaluateRulesAction` — efekty reguł
4. `PricingCalculator` — wynik

## Admin (Filament)

Navigation group **Pricing** → **Base prices** (`ProductPriceResource`).

- List: product, SKU, amount (display), configurable flag
- Create: configurable product without an existing price + `MoneyAmountInput` (Shared)
- Edit: product read-only, amount editable
- Persist: `amount` (int, minor units); max 1 row per product (UNIQUE `product_id`)
- Access: roles `admin`, `manager` (`PricingManagementPolicy` — same as catalog)

## Demo data

W `config/demo-catalog.php` na produkcie:

```php
'base_price' => 199900,  // 1999.00 PLN
```

Seed: `DemoPricingSeeder` (po `DemoRulesSeeder`), idempotentny po `product.slug`.

## Testy P0 (tylko istotna logika)

**Unit — `PricingCalculatorTest`:** base only; base + add; base + subtract; override zastępuje wszystko; precedencja override po `position`; floor `total` przy 0.

**Feature — `PricingApiTest`:** guest 401; happy path; `hasOverride: true`; brak `ProductPrice` → 422; produkt niekonfigurowalny → 422.

**Feature — `PricingPolicyTest` (P1):** admin/manager allow; sales deny (panel role without catalog management).

## Kolejność implementacji

1. Domain (`ProductPrice`, `PricingCalculator`, exceptions, repository contract)
2. Infrastructure (migration, factory, repository, seeder, provider)
3. Application (`CalculatePriceAction`, `PriceCalculationData`)
4. Presentation (route, controller, Filament, policy)
5. Integracja (`bootstrap/providers.php`, `InteractsWithPricing` na `Product`, demo seed)
6. Testy
7. Docs (ten plik)
