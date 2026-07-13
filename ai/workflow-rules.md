# Workflow Rules

## General

The project is developed incrementally.

Every task must be divided into small and manageable steps.

Never generate large amounts of code without prior analysis.

Always explain architecture decisions before implementation.

Always respect existing project architecture.

## Code Generation

Generated code must be production-ready.

Generated code must be complete.

Generated code must be manually transferable into the project.

Do not generate pseudo-code.

Do not omit important parts of implementation.

Do not use placeholders such as:

* TODO
* implementation later
* omitted for brevity

Every generated example should be runnable.

## Development Process

Before coding:

1. Analyze requirements.
2. Propose implementation plan.
3. Wait for approval if architecture changes are required.
4. Generate code.

## Architecture

Follow:

* SOLID
* DRY
* KISS
* YAGNI

Controllers must remain thin.

Business logic belongs to Actions.

Use DTOs for data transfer.

Use Events for side effects.

Prefer composition over inheritance.

## Layer Boundaries

* **Application** (Actions) must not depend on HTTP: no `Illuminate\Http\*`, no `redirect()`,
  `abort()`, `response()`, no `Request`/`Response`/`JsonResponse`/`RedirectResponse`.
* Actions return business results (DTOs, primitives, domain value objects) or throw **domain
  exceptions** defined under `Domain/Exceptions`.
* **Presentation** (controllers) maps those results to HTTP: status codes, JSON envelopes,
  redirects, headers. Example: an Action returns a redirect URL (`string`); the controller calls
  `redirect()->away(...)` and maps domain exceptions to `abort(403)` / validation errors.
* **Infrastructure** owns persistence queries. Actions/controllers depend on
  `Domain/Contracts/{Entity}RepositoryInterface`, never `Model::query()` directly (except inside the
  Eloquent repository implementation).

## Repository Convention

* Contract (interface): `Domain/Contracts/{Entity}RepositoryInterface.php` — always use the `Interface` suffix.
* Implementation: `Infrastructure/Persistence/Repositories/Eloquent{Entity}Repository.php`.
* Bind contract → implementation in the module `ServiceProvider` (`register()`).

## Communication

The assistant should keep project context.

The assistant may ask clarification questions before implementation.

The assistant should never assume business rules without confirmation.

The assistant should explain tradeoffs when multiple solutions are possible.

## Definition of Done (per module slice)

A slice is done only when:

* Domain, Application, Infrastructure and Presentation layers are implemented per architecture rules.
* The module self-registers via its ServiceProvider (added to `bootstrap/providers.php`).
* Validation follows the backend rules (FormRequest for every endpoint; Actions take typed DTOs).
* Feature and Unit tests cover the happy path and key failure cases.
* `composer check` passes (Pint, PHPStan, Rector, tests).

## Working With the Assistant (delivery model)

**Project-wide rule — always, every session, no exceptions.**

* Work is delivered in small reviewable batches ("porcja"): plan -> approval -> **code shown in chat**.
* **Never auto-edit** `backend/` or `frontend/` unless the user explicitly asks to apply a patch,
  implement in the repo, or similar (e.g. „wdróż”, „zastosuj”, „patch”).
* **Always** provide complete, copy-paste-ready application code in the response.
* Phrases like **„przygotuj kod”**, **„pokaż implementację”**, **„daj kod”** mean **show code in chat**
  — **not** write or modify files.
* Auto-edit without extra confirmation: **`docs/` and `ai/` only**.
* For non-trivial tasks first state: assumptions, plan, impacted files, risks — then wait for
  approval before showing implementation code.

## Quality Gates (authoritative)

* `composer check` (== `check:push`): `artisan test` + PHPStan level 6 (Larastan) +
  `pint --test` + `rector --dry-run`.
* `composer check:commit`: `pint --test` + Unit tests.
* `composer fix`: Pint + Rector.
* A slice is not done until `composer check` is green.

## Testing

* Runner: PHPUnit via `php artisan test` (suites `Unit`, `Feature`).
* Tests live in `modules/{Module}/Tests/{Unit,Feature}` (module-owned).

## HTTP Responses (status conventions)

* Return a DTO when the default status fits (GET -> 200, POST -> 201) — e.g. `RegisterController`,
  `ProfileController`.
* POST endpoints that **do not create a resource** (e.g. Configurator `evaluate`, `validate`)
  return **200** — use `JsonResponse` or `->toResponse($request)->setStatusCode(200)` explicitly;
  do not rely on the default POST -> 201 from Spatie Data.
* When an API response must expose an **associative map keyed by ULID** (e.g. `evaluate` →
  `attributes.{public_id}`), build the JSON envelope in the controller if Spatie Data
  serialization would flatten keys to a numeric list.
* To override the status on a DTO response, declare the method return type
  `Symfony\Component\HttpFoundation\Response` and chain `->toResponse($request)->setStatusCode(...)`
  — e.g. `LoginController` (POST -> 200). Rationale: `Responsable::toResponse()` is typed as
  `Symfony Response`, so PHPStan rejects a narrowed `JsonResponse` return.

## Filament Conventions

* A module registers its Filament Resources and Policies from its `ModuleServiceProvider`.
* Policies live in `Presentation/Filament/Policies`.
