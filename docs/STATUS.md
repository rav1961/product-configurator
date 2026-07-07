# Project Status

_Living document. The assistant updates it at the end of each slice ("porcja")._

Legend: `[x]` done · `[~]` in progress · `[ ]` planned.

Scope is derived from: `02-business-requirements`, `06-domain-overview`,
`07-configurator-engine`, `08-rule-engine`, `09-pricing-engine`, `10-order-lifecycle`,
`13-roadmap`, `14-module-roadmap`. This platform is a **product configurator + quotation (RFQ)**
tool, not a web shop.

## Currently working on

- **Configurator** module — dokumentacja i dalsze usprawnienia; następny krok: Rule Engine.

## Conventions in place

- Translations: `resources/lang/pl/{domain}.php` (one file per Filament resource), used via `__()`.
  Polish is the primary app language; `en` added later (Multilanguage). Applied to Users + Catalog + Configurator.
- Repository contracts: `Domain/Contracts/{Entity}RepositoryInterface.php`; Eloquent implementations
  in `Infrastructure/Persistence/Repositories/Eloquent{Entity}Repository.php`.
- Layer boundaries: Application (Actions) has no HTTP dependencies; Presentation maps to HTTP.
- **API route middleware stacks:** `Modules\Shared\Presentation\Http\ApiRouteMiddleware` (`VERIFIED`, `SENSITIVE_THROTTLE`).
- **Media (Shared kernel):** Spatie Media Library v11 + Filament plugin. Enums in
  `Shared/Domain/Enums/` (`MediaCollection`, `MediaConversion`, `MediaProfile`). Profile-specific
  dimensions via `MediaProfile::definitions()`; registration via `MediaConversionRegistrar` +
  `HasConfiguredMedia` (composite over `InteractsWithMedia`). Default MIME list in
  `MediaMimeTypes::images()`; override per model via `RegistersDefaultMediaCollection`. API shape:
  `MediaData` (`name`, `position`, `src`, `srcset`, `thumb`) — `Preview` conversion uses
  `withResponsiveImages()` for frontend srcset. Docker PHP image: GD compiled with WebP
  (`libwebp-dev`, `--with-webp`). Runtime temp files under `storage/media-library/` are gitignored.

## Foundations

- [x] Shared kernel: `ModuleServiceProvider`, `ApiController`, `HasPublicId`, `HasModuleFactory`.
- [x] Quality gates: `composer check` (PHPStan level 6, Pint, Rector, PHPUnit) green.
- [x] System module: health endpoint + test.

## Module backlog (roadmap order)

### 1-2. Auth + Users
- [x] Module complete (auth, RBAC, Filament, verification, password reset, repository pattern, PL mail copy).
- [x] Auth: Sanctum SPA (no Fortify), native primitives; register / login / logout / profile
      (thin controllers + FormRequest + DTO + Actions).
- [x] RBAC: `Role` enum (admin/manager/sales/customer), `RoleSeeder`, `DemoUsersSeeder` (one verified user per role), role hierarchy.
- [x] Persistence: `UserFactory`, module migration, `UserPolicy`.
- [x] Filament `UserResource` + Pages + provider wiring (resource + policy + role field via `assignableRoles()`).
- [x] PL translations (`resources/lang/pl/users.php`).
- [x] Tests: feature (auth flows, panel access) + unit (policy / role hierarchy). Shared `InteractsWithSpaSession` test trait for SPA-session feature tests.
- [x] Email verification: signed verify URL, resend notification, `verified` middleware on business endpoints, `Registered` event fix.
- [x] Password reset: `forgot-password` / `reset-password` via Password broker + SPA links.
- [x] Polish auth mail templates (`users.mail.*`), `AuthNotificationConfigurator`, default locale `pl`.
- [x] `UserRepositoryInterface` + `Infrastructure/Persistence/Repositories/EloquentUserRepository`; persistence queries out of controllers/actions.

### 3. Catalog
- [x] Domain: `Category`, `Product` models, `ProductStatus` enum.
- [x] Persistence: factories, seeders, migrations.
- [x] Read API: list/show products, list categories (DTO + Action + FormRequest) + tests. Endpoints behind `auth:sanctum` + `verified`.
- [x] Filament: `CategoryResource`, `ProductResource` + pages.
- [x] PL translations (`resources/lang/pl/catalog.php`, `resources/lang/pl/products.php`, `ProductStatus::label()`).
- [x] Repository pattern: `CategoryRepositoryInterface`, `ProductRepositoryInterface` + Eloquent implementations; `ProductIndexData` input DTO; actions `readonly`; test namespaces fixed (PSR-4).
- [x] **Porcja A — media:** Shared media kernel (`MediaCollection`, `MediaConversion`, `MediaProfile`,
      `MediaConversionDefinition`, `MediaConversionRegistrar`, `HasConfiguredMedia`,
      `RegistersDefaultMediaCollection`, `MediaData`). `Category` + `Product` cover upload (Filament,
      collection `cover`, profiles `CategoryCover` / `ProductCover`). Read API: `cover` on
      `CategoryData` / `ProductData` with responsive `src` + `srcset`. Tests: `CategoryMediaTest`,
      `ProductMediaTest`, API cover assertions. `filament/spatie-laravel-media-library-plugin`.
      Docker: GD + WebP. `storage/media-library/` gitignored.
- [x] **Porcja B — policies:** `CategoryPolicy`, `ProductPolicy`; `Role::catalogManagementRoles()`
      (`admin`, `manager` only); Gate registration in `CatalogServiceProvider`. Tests: `CatalogPolicyTest`.
- [x] **Demo users seeder:** `DemoUsersSeeder` + `config/demo-users.php` (replaces `AdminSeeder`); idempotent
      `updateOrCreateByEmail` + `syncRoles`; shared password via `DEMO_USERS_PASSWORD` / `ADMIN_PASSWORD`.
- [x] Bridge to Configurator: `Product.is_configurable`, `GetConfigurableProductAction`, relacje kroków/zależności na produkcie.

### 4. Configurator
- [x] Domain: `Step`, `Attribute`, `AttributeValue`, `AttributeCollection`, `Dependency`; enums `AttributeType`, `DependencyAction`, `DependencyCondition`; VO `ConfigurationSelection`.
- [x] Persistence: migrations, factories, 6× repository contracts + Eloquent implementations, `ConfiguratorGraphRepository`, `DependencyObserver` + `DependencyValidator`.
- [x] Domain services: `DependencyConditionMatcher`, `DependencyRuleEvaluator`, `ConfigurationValidator`.
- [x] Admin (Filament): nested resources (`StepResource`, `AttributeResource`, `AttributeCollectionResource`) + relation managers na produkcie/kroku; `ConfiguratorManagementPolicy` (admin/manager).
- [x] Engine: schema z grafu produktu, ewaluacja zależności (2-fazowa semantyka `show`), walidacja selekcji.
- [x] API for SPA: `schema`, `evaluate`, `validate` — `auth:sanctum` + `verified`; ULID w trasach i `selection`.
- [x] PL translations (`resources/lang/pl/configurator.php`).
- [x] Demo data: `DemoConfiguratorSeeder` + `config/demo-catalog.php` (katalog + konfiguracja produktów demo).
- [x] Tests: unit (matcher, evaluator, validator, dependency validator) + feature (API, repositories, models, policies, schema action).
- [ ] Saved configuration session (persistencja wyboru użytkownika) — przed Cart.
- [ ] Pełny CRUD kroków/atrybutów poza relation managers (opcjonalne usprawnienia UX admina).

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
