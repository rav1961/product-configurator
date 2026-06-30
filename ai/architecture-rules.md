# Architecture Rules

Laravel API + Next.js. Modular Monolith (Laravel).

## Modular Monolith

- Each bounded context is a module in `backend/modules/{Module}`.
- PSR-4 namespace `Modules\{Module}\` mapped to `modules/{Module}`.
- Modules are organized into explicit layers:
  - `Domain` — models, enums, value objects, domain events, domain exceptions, repository contracts.
  - `Application` — actions (use cases), DTOs. No HTTP dependencies.
  - `Infrastructure` — persistence (migrations/factories/seeders/repositories) and service providers.
  - `Presentation` — Http (controllers/requests/resources), routes. Maps Action results to HTTP.
- Each module self-registers via `Modules\{Module}\Infrastructure\Providers\{Module}ServiceProvider`
  extending the shared `Modules\Shared\Infrastructure\Providers\ModuleServiceProvider`.
- Factories are wired by convention via the `HasModuleFactory` behavior (no global resolver).
- Inter-module communication only through public contracts (Actions, DTOs, Events).
- Cross-cutting, framework-level building blocks live in the `Shared` kernel module.
- No DDD-Lite shortcuts: each module owns its domain, persistence, presentation and tests.
- Repository pattern: `Domain/Contracts/{Entity}Repository` + `Infrastructure/Persistence/Repositories/Eloquent{Entity}Repository`.
- Application layer never imports `Illuminate\Http`; HTTP mapping belongs in Presentation only.

## Module Self-Registration & Framework Integration

- Every module registers itself via `{Module}ServiceProvider` (in `Infrastructure/Providers`),
  added to `bootstrap/providers.php`.
- A module provider may wire framework integrations that belong to that module's domain.
  Example: the `Users` module registers its Filament resources and access policies from
  `UsersServiceProvider`.
- Framework configuration files stay in `config/` (framework layer). Modules own *behavior*
  (bindings, actions, policies, resources, routes) — not the framework config files themselves.
- A module owns the migrations for its domain tables, placed in
  `Infrastructure/Persistence/Migrations` (auto-loaded by `ModuleServiceProvider`). Example:
  the `Users` module owns the `users` and `password_reset_tokens` tables, together with the
  Model (`Domain/Models`), Factory (`Infrastructure/Persistence/Factories`) and Seeders
  (`Infrastructure/Persistence/Seeders`).
- Only framework runtime tables that belong to no bounded context stay in `database/migrations`
  (`sessions`, `cache`, `jobs`), together with third-party package migrations
  (spatie/permission, activitylog).
