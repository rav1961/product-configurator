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
* Per-module ServiceProvider extending shared `ModuleServiceProvider`
* Shared kernel module for cross-cutting concerns
* Inter-module communication via Actions / DTOs / Events
