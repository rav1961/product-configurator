# Architecture Rules

Laravel API + Next.js. Modular Monolith (Laravel).

## Modular Monolith

- Each bounded context is a module in `backend/modules/{Module}`.
- PSR-4 namespace `Modules\{Module}\` mapped to `modules/{Module}`.
- Modules are organized into explicit layers:
  - `Domain` — models, enums, value objects, domain events, domain exceptions, repository contracts (`*Interface`).
  - `Application` — actions (use cases), DTOs. No HTTP dependencies.
  - `Infrastructure` — persistence (migrations/factories/seeders/repositories) and service providers.
  - `Presentation` — Http (controllers/requests/resources), routes. Maps Action results to HTTP.
- Each module self-registers via `Modules\{Module}\Infrastructure\Providers\{Module}ServiceProvider`
  extending the shared `Modules\Shared\Infrastructure\Providers\ModuleServiceProvider`.
- Factories are wired by convention via the `HasModuleFactory` behavior (no global resolver).
- Inter-module communication only through public contracts (Actions, DTOs, Events).
- Cross-cutting, framework-level building blocks live in the `Shared` kernel module.
  Example: `Modules\Shared\Presentation\Http\ApiRouteMiddleware` — shared route middleware stacks.
- No DDD-Lite shortcuts: each module owns its domain, persistence, presentation and tests.
- Repository pattern: `Domain/Contracts/{Entity}RepositoryInterface` + `Infrastructure/Persistence/Repositories/Eloquent{Entity}Repository`.
- All domain contracts (interfaces) use the `Interface` suffix (e.g. `UserRepositoryInterface`, not `UserRepository`).
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

## API Routing

- Domain API routes live in each module's `Presentation/Routes/api.php` — **not** in
  `backend/routes/api.php` (that file stays empty).
- `ModuleServiceProvider` loads module routes with middleware `api` and prefix `/api`.
- Reusable middleware stacks are defined in
  `Modules\Shared\Presentation\Http\ApiRouteMiddleware` (e.g. `VERIFIED`,
  `SENSITIVE_THROTTLE`). Modules reference these constants in their route files — do not
  duplicate `['auth:sanctum', 'verified']` literals across modules.
- Per-module routing keeps public exceptions explicit (e.g. `register` / `login` in Users vs
  verified-only Catalog/Configurator endpoints).
