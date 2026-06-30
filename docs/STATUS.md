# Project Status

_Living document. The assistant updates it at the end of each slice ("porcja")._

Legend: `[x]` done · `[~]` in progress · `[ ]` planned.

Scope is derived from: `02-business-requirements`, `06-domain-overview`,
`07-configurator-engine`, `08-rule-engine`, `09-pricing-engine`, `10-order-lifecycle`,
`13-roadmap`, `14-module-roadmap`. This platform is a **product configurator + quotation (RFQ)**
tool, not a web shop.

## Currently working on

- Users: closing Definition of Done — feature/unit tests for auth + Filament policy/hierarchy.

## Conventions in place

- Translations: `resources/lang/pl/{domain}.php` (one file per Filament resource), used via `__()`.
  Polish is the primary app language; `en` added later (Multilanguage). Applied to Users + Catalog.

## Foundations

- [x] Shared kernel: `ModuleServiceProvider`, `ApiController`, `HasPublicId`, `HasModuleFactory`.
- [x] Quality gates: `composer check` (PHPStan level 6, Pint, Rector, PHPUnit) green.
- [x] System module: health endpoint + test.

## Module backlog (roadmap order)

### 1-2. Auth + Users
- [x] Auth: Sanctum SPA (no Fortify), native primitives; register / login / logout / profile
      (thin controllers + FormRequest + DTO + Actions).
- [x] RBAC: `Role` enum (admin/manager/sales/customer), `RoleSeeder`, `AdminSeeder`, role hierarchy.
- [x] Persistence: `UserFactory`, module migration, `UserPolicy`.
- [x] Filament `UserResource` + Pages + provider wiring (resource + policy + role field via `assignableRoles()`).
- [x] PL translations (`resources/lang/pl/users.php`).
- [ ] Tests: feature (auth flows, panel access) + unit (policy / role hierarchy) to close Definition of Done.
- [ ] Password reset + email verification flows wired end-to-end (Password broker, `Registered` event).

### 3. Catalog
- [x] Domain: `Category`, `Product` models, `ProductStatus` enum.
- [x] Persistence: factories, seeders, migrations.
- [x] Read API: list/show products, list categories (DTO + Action + FormRequest) + tests.
- [x] Filament: `CategoryResource`, `ProductResource` + pages.
- [x] PL translations (`resources/lang/pl/catalog.php`, `resources/lang/pl/products.php`, `ProductStatus::label()`).
- [ ] Write/admin completeness: media (spatie/medialibrary) for product images, validation, policies.
- [ ] Link products to configurable attributes (bridge to Configurator).

### 4. Configurator
- [ ] Domain: Steps, Attributes, Attribute Values, Collections, Dependencies.
- [ ] Admin (Filament): manage steps / attributes / values / dependencies per product.
- [ ] Engine: build a configuration session, validate dependencies, expose dynamic form schema.
- [ ] API for SPA: fetch configurator schema + submit/validate a configuration.
- [ ] Tests: dependency resolution + schema generation.

### 5. Rule Engine
- [ ] Domain: `Rule -> Group -> Conditions -> Actions`.
- [ ] Evaluation engine (conditions matching, actions applying) integrated with Configurator.
- [ ] Admin (Filament): CRUD for rules / groups / conditions / actions.
- [ ] Tests: condition/action evaluation matrix.

### 6. Pricing
- [ ] Engine: base price + modifiers + overrides.
- [ ] Admin: configure modifiers / overrides.
- [ ] Integration: price a configuration deterministically (with rule-engine effects).
- [ ] Tests: pricing scenarios (base / modifier / override precedence).

### 7. Cart
- [ ] Domain: cart + line items built from a saved configuration.
- [ ] Persistence + API for SPA (add / update / remove / view).
- [ ] Tests: cart build from configuration + price totals.

### 8. Orders
- [ ] Domain: RFQ/Order created from cart; status lifecycle.
- [ ] Flow: `Configurator -> Cart -> Order -> PDF -> Notification -> Processing`.
- [ ] PDF generation of the quote/order.
- [ ] Order history for customers; status changes for sales/manager.
- [ ] Tests: order creation + status transitions + PDF artifact.

### 9. Notifications
- [ ] Email notifications (order placed, status change) + templates.
- [ ] Queue-backed delivery (RabbitMQ).
- [ ] Tests: notification dispatch on domain events.

### 10. Settings
- [ ] System settings module (admin-only) for global configuration.

## Post-MVP (roadmap 13)
- [ ] Multilanguage.
- [ ] Analytics.
