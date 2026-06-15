# Architecture Rules

Laravel API + Next.js. Modular Monolith (Laravel).

## Modular Monolith

- Each bounded context is a module in `backend/modules/{Module}`.
- PSR-4 namespace `Modules\{Module}\` mapped to `modules/{Module}`.
- A module is a full vertical slice: Domain, Application, Http, Filament, Database, Routes, Tests.
- Each module self-registers via `Modules\{Module}\Providers\{Module}ServiceProvider`
  extending the shared `Modules\Shared\Providers\ModuleServiceProvider`.
- Inter-module communication only through public contracts (Actions, DTOs, Events).
  No reaching into another module's internals.
- Cross-cutting, framework-level code lives in the `Shared` kernel module.
- No DDD-Lite shortcuts: each module owns its domain, persistence, presentation and tests.
