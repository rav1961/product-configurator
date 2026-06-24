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

* Laravel Fortify (registration, login, logout, password reset, email verification, profile/password update)
* Sanctum SPA session
* HttpOnly Cookies
* Guard: web
* Fortify action contracts bound in `UsersServiceProvider`

## Authorization

* spatie/laravel-permission
* Roles: admin, manager, sales, customer
* Default role on registration: customer
* Filament panel access: admin, manager, sales
* Permissions: role-based now; granular per-module permissions added as modules are built

## Validation

* FormRequest for our own endpoints
* Validator-in-Action for Fortify-managed flows (shared `PasswordValidationRules`)

## Persistence Conventions

* Public identifiers: ULID via `HasPublicId` (`public_id`); numeric ids never exposed
* Module factories via `HasModuleFactory` convention
* Module-owned seeders/factories; `DatabaseSeeder` only orchestrates

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
