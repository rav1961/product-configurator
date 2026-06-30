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
* Authenticated-only API: every domain endpoint sits behind `auth:sanctum`; the only public exceptions are `register`, `login`, `forgot-password`, `reset-password`, `email/verify/{id}/{hash}` (signed) and `health`
* Catalog endpoints (`categories`, `products`) require authentication; auth is applied per-module in `Presentation/Routes/api.php`, never globally
* Email verification required: business endpoints add `verified` (`EnsureEmailIsVerified`) on top of `auth:sanctum`. Account endpoints (`logout`, `profile`, resend verification) intentionally do NOT require `verified`
* Email verification + password reset use native primitives (`MustVerifyEmail`, Password broker, events `Registered`/`Verified`/`PasswordReset`) wrapped in our controller + FormRequest + DTO + Action pattern
* Mail links target the SPA: configured via `config('app.frontend_url')` (`FRONTEND_URL`); reset opens a frontend form, email verification confirms server-side then redirects to the SPA

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

* All user-facing strings go through translations (`__()`), never hardcoded.
* Location: `resources/lang/{locale}/{domain}.php`; one file per Filament resource
  (`users.php`, `catalog.php`, `products.php`).
* Key convention: `domain.section.key` (e.g. `users.fields.roles`, `users.role.admin`,
  `products.status.draft`). Enum display labels expose `label()` returning `__()`.
* Primary language is `pl`; `en` deferred to the Multilanguage stage.
* `__()` is typed as `string` by larastan, so it can be returned directly from `: string` methods.
* No per-module `Lang` folders / namespace registration (KISS — framework auto-loads `resources/lang/`).

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
