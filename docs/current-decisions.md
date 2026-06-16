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

* Sanctum
* HttpOnly Cookies

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
