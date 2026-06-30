# Backend Rules

Thin (invokable) controllers, DTOs, Actions, Events, Form Requests.

## Layers & Responsibilities

- Controllers are thin and invokable (`__invoke`); they delegate to Actions and return DTOs.
- Business logic lives in Application Actions (single responsibility, one public method).
- Output is shaped by DTOs (`spatie/laravel-data`); never return Eloquent models directly from the API.
- Side effects are handled via Domain Events.
- **Application must not use HTTP** — no `Illuminate\Http\*`, `redirect()`, `abort()`, or `response()`.
  Actions return DTOs/primitives or throw domain exceptions (`Domain/Exceptions`); controllers map
  those to HTTP (JSON, status codes, redirects).
- Persistence queries live in repositories; Actions depend on `Domain/Contracts/{Entity}RepositoryInterface`,
  not `Model::query()` (except inside `Infrastructure/Persistence/Repositories/`).

## Validation (consistent — no exceptions)

- ALWAYS validate in a `FormRequest` (e.g. `Catalog\...\ProductIndexRequest`,
  `Users\...\RegisterRequest`). Never validate inside an Action.
- Actions receive typed DTOs (`spatie/laravel-data`, e.g. `RegisterData`), never raw arrays.
- Auth flows use the framework natively (Sanctum SPA + `Auth`/Password broker); we do not use
  Fortify and do not override library action contracts.

## Identifiers

- Public/external identifiers are ULIDs via the `HasPublicId` concern, exposed as `public_id`
  (mapped to `id` in DTOs). NEVER expose the numeric primary key in APIs.

## Persistence Conventions

- Repository contract: `Domain/Contracts/{Entity}RepositoryInterface.php` (always `Interface` suffix).
- Eloquent implementation: `Infrastructure/Persistence/Repositories/Eloquent{Entity}Repository.php` (no `Interface` suffix on concrete class).
- Bind in the module `ServiceProvider` (`register()`).
- Model factories follow the `HasModuleFactory` convention:
  `Modules\{Module}\Infrastructure\Persistence\Factories\{Model}Factory`.
  Do NOT place factories for module models in `database/factories`.
- Seeders live in `Modules\{Module}\Infrastructure\Persistence\Seeders`; `DatabaseSeeder`
  only orchestrates module seeders.

## Code Style

- `declare(strict_types=1)` in every PHP file; classes `final` by default.
- Follow SOLID, DRY, KISS, YAGNI. Prefer first-party / well-established packages (Sanctum,
  spatie/laravel-permission, spatie/laravel-data, spatie/laravel-activitylog) over custom code.
- Quality gates must pass: `composer check` (Pint, PHPStan/Larastan, Rector, tests).
