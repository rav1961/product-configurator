# Current Decisions

## Architecture

* Monorepo
* Laravel API
* Next.js Frontend

## Database

* PostgreSQL

## Queue

* RabbitMQ

## Cache

* Redis

## Admin Panel

* Filament

## Auth

* Sanctum SPA session (no Fortify)
* HttpOnly Cookies
* Guard: web
* Own thin controllers + FormRequest + Actions + DTOs in the `Users` module (register, login, logout, me)
* Native framework primitives: `Auth::attempt`, session regeneration, `RateLimiter`, Password broker, email verification

## Authorization

* spatie/laravel-permission
* Roles: admin, manager, sales, customer
* Default role on registration: customer
* Filament panel access: admin, manager, sales
* Permissions: role-based now; granular per-module permissions added as modules are built

## Validation

* FormRequest for every endpoint (validation lives in the request, never in the Action)
* Actions receive typed DTOs (e.g. `RegisterData`), never raw arrays

## Translations

* All user-facing strings go through translations (`__()` / `trans()`), never hardcoded.
* Standard Laravel `lang/{locale}/` directory; one file per module (`lang/en/users.php`).
* Key convention: `module.section.key` (e.g. `users.fields.roles`, `users.role.admin`).
* No per-module `Lang` folders / namespace registration (KISS — framework auto-loads `lang/`).

## Persistence Conventions

* Public identifiers: ULID via `HasPublicId` (`public_id`); numeric ids never exposed
* Module factories via `HasModuleFactory` convention
* Module-owned seeders/factories; `DatabaseSeeder` only orchestrates
* Module-owned migrations for domain tables (e.g. Users owns `users`, `password_reset_tokens`); only `sessions`, `cache`, `jobs` + package migrations stay in `database/migrations`

## Frontend

* Next.js App Router
* TypeScript
* TanStack Query
* Tailwind
* shadcn/ui

## Coding Style

* DTO
* Actions
* Events
* Thin Controllers

## Pricing Engine

Base Price + Modifiers + Overrides

## Configurator

Database driven.

All steps, attributes and rules configurable from admin panel.

## Modularization

* Modular Monolith (Laravel)
* Modules in `backend/modules/{Module}`
* PSR-4 `Modules\` namespace
* Layers: Domain / Application / Infrastructure / Presentation
* Per-module ServiceProvider (`Infrastructure/Providers`) extending shared `ModuleServiceProvider`
* Shared kernel: `Domain/Concerns`, `Infrastructure/Providers`, `Presentation/Http`
* Factory wiring by convention via `HasModuleFactory` behavior (no global resolver)
* Inter-module communication via Actions / DTOs / Events
