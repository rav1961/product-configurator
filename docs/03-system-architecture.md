# System Architecture

Monorepo:
- backend (Laravel API + Filament)
- frontend (Next.js)

## Backend: Modular Monolith

The backend is a Modular Monolith. Every bounded context lives in `backend/modules/{Module}`:

```
modules/
  Shared/         # shared kernel (base providers, HTTP primitives, common concerns)
  Catalog/
  Configurator/
  ...
```

Conventions:
- Namespace: `Modules\{Module}\...` (PSR-4 -> `modules/{Module}`).
- Layers per module: Domain, Application, Http, Filament, Database (Migrations/Factories/Seeders), Routes, Tests.
- Registration: `{Module}ServiceProvider` extends `Modules\Shared\Providers\ModuleServiceProvider`.
- Inter-module communication via Actions / DTOs / Events only.
- Framework infrastructure (auth scaffolding, cache, jobs, permissions, media) stays in `app/` and `database/migrations`.
