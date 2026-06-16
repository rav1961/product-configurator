# System Architecture

Monorepo:
- backend (Laravel API + Filament)
- frontend (Next.js)

## Backend: Modular Monolith

The backend is a Modular Monolith. Every bounded context lives in `backend/modules/{Module}`
and is organized into explicit layers:

```
modules/{Module}/
  Domain/                         # models, enums, value objects, domain events, concerns
  Application/                    # actions (use cases), DTOs
  Infrastructure/
    Persistence/                  # migrations, factories, seeders
    Providers/                    # {Module}ServiceProvider
  Presentation/
    Http/                         # controllers, requests, resources
    Routes/                       # api.php
  Tests/
```

Shared kernel:

```
modules/Shared/
  Domain/Concerns/               # reusable model behaviors (HasPublicId, HasModuleFactory)
  Infrastructure/Providers/       # ModuleServiceProvider (abstract base)
  Presentation/Http/Controllers/  # ApiController (base controller)
```

Conventions:
- Namespace: `Modules\{Module}\...` (PSR-4 -> `modules/{Module}`).
- Each module registers via `{Module}ServiceProvider` (in `Infrastructure/Providers`)
  extending `Modules\Shared\Infrastructure\Providers\ModuleServiceProvider`, which auto-loads
  `Infrastructure/Persistence/Migrations` and `Presentation/Routes/api.php`.
- Factories are resolved by convention through the `HasModuleFactory` behavior:
  `Domain\Models\{Model}` -> `Infrastructure\Persistence\Factories\{Model}Factory`.
- Inter-module communication via Actions / DTOs / Events only.
- Framework infrastructure (auth scaffolding, cache, jobs, permissions, media) stays in `app/` and `database/migrations`.
