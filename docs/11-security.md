# Security

Sanctum, HttpOnly Cookies, Spatie Permission, Audit Log.

## Authentication

- Laravel Fortify (registration, login, logout, password reset, email verification,
  profile/password update). Fortify `views` disabled (JSON only).
- Laravel Sanctum SPA session authentication with HttpOnly cookies; guard `web`.
- CSRF handshake via `/sanctum/csrf-cookie` before mutating requests from the SPA.
- Fortify action contracts are bound to module Actions in `UsersServiceProvider`.

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
