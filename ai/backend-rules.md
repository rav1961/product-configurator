# Backend Rules

Thin (invokable) controllers, DTOs, Actions, Events, Form Requests.

## Layers & Responsibilities

- Controllers are thin and invokable (`__invoke`); they delegate to Actions and return DTOs.
- Business logic lives in Application Actions (single responsibility, one public method).
- Output is shaped by DTOs (`spatie/laravel-data`); never return Eloquent models directly from the API.
- Side effects are handled via Domain Events.

## Validation (consistent — no exceptions)

- For our own HTTP endpoints: ALWAYS use a `FormRequest` (e.g. `Catalog\...\ProductIndexRequest`).
- For Fortify-managed flows (register, reset/update password, update profile): validate INSIDE
  the Fortify action using `Validator` + the shared `PasswordValidationRules` trait. Fortify
  contracts receive `array $input`, so a `FormRequest` does not fit there — do not force it.
- Never mix the two approaches inconsistently within the same flow.

## Identifiers

- Public/external identifiers are ULIDs via the `HasPublicId` concern, exposed as `public_id`
  (mapped to `id` in DTOs). NEVER expose the numeric primary key in APIs.

## Persistence Conventions

- Model factories follow the `HasModuleFactory` convention:
  `Modules\{Module}\Infrastructure\Persistence\Factories\{Model}Factory`.
  Do NOT place factories for module models in `database/factories`.
- Seeders live in `Modules\{Module}\Infrastructure\Persistence\Seeders`; `DatabaseSeeder`
  only orchestrates module seeders.

## Code Style

- `declare(strict_types=1)` in every PHP file; classes `final` by default.
- Follow SOLID, DRY, KISS, YAGNI. Prefer first-party packages (Fortify, Sanctum,
  spatie/laravel-permission, spatie/laravel-data, spatie/laravel-activitylog) over custom code.
- Quality gates must pass: `composer check` (Pint, PHPStan/Larastan, Rector, tests).
