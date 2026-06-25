# Security

Sanctum, HttpOnly Cookies, Spatie Permission, Audit Log.

## Authentication

- Laravel Sanctum SPA session authentication with HttpOnly cookies; guard `web`. No Fortify.
- Auth flows are our own thin invokable controllers + `FormRequest` + Actions + DTOs, living in
  the `Users` module (`register`, `login`, `logout`, `me`). They use the framework natively
  (`Auth::attempt`, session regeneration, `RateLimiter`, Password broker, email verification).
- Login throttling (per email + IP) lives in `LoginRequest` (Breeze-style), firing `Lockout`.
- CSRF handshake via `/sanctum/csrf-cookie` before mutating requests from the SPA.

## Authorization (RBAC)

- spatie/laravel-permission. Roles: `admin`, `manager`, `sales`, `customer`.
- Back-office (Filament `admin` panel) access: `admin`, `manager`, `sales`
  (via `User::canAccessPanel` / `Role::panelRoles()`). `customer` uses the SPA only.
- Permissions strategy: role-based now. Granular per-feature permissions are introduced
  per module as those modules are built; each module owns and seeds its own permissions.

## Auditing

- spatie/laravel-activitylog for the audit trail.

## Data exposure

- External identifiers are ULIDs (`public_id`); numeric primary keys are never exposed.
