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
