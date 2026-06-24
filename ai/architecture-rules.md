# Architecture Rules

Laravel API + Next.js. Modular Monolith (Laravel).

## Modular Monolith

- Each bounded context is a module in `backend/modules/{Module}`.
- PSR-4 namespace `Modules\{Module}\` mapped to `modules/{Module}`.
- Modules are organized into explicit layers:
  - `Domain` — models, enums, value objects, domain events, reusable model behaviors.
  - `Application` — actions (use cases), DTOs.
  - `Infrastructure` — persistence (migrations/factories/seeders) and service providers.
  - `Presentation` — Http (controllers/requests/resources) and routes.
- Each module self-registers via `Modules\{Module}\Infrastructure\Providers\{Module}ServiceProvider`
  extending the shared `Modules\Shared\Infrastructure\Providers\ModuleServiceProvider`.
- Factories are wired by convention via the `HasModuleFactory` behavior (no global resolver).
- Inter-module communication only through public contracts (Actions, DTOs, Events).
- Cross-cutting, framework-level building blocks live in the `Shared` kernel module.
- No DDD-Lite shortcuts: each module owns its domain, persistence, presentation and tests.

## Module Self-Registration & Framework Integration

- Every module registers itself via `{Module}ServiceProvider` (in `Infrastructure/Providers`),
  added to `bootstrap/providers.php`.
- A module provider may wire first-party framework integrations that belong to that module's
  domain. Example: the `Users` module binds Fortify action contracts, registers the `login`
  rate limiter, and exposes its Filament resources from `UsersServiceProvider`.
- Framework configuration files stay in `config/` (framework layer). Modules own *behavior*
  (bindings, actions, policies, resources, routes) — not the framework config files themselves.
- Framework auth scaffolding tables (`users`, `sessions`, `password_reset_tokens`) stay in
  `database/migrations`. The owning module still owns the domain artifacts for those tables:
  Model (`Domain/Models`), Factory (`Infrastructure/Persistence/Factories`), and Seeders
  (`Infrastructure/Persistence/Seeders`).
