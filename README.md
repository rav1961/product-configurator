# Product Configurator (WIP)

A platform for product configuration and request-for-quote (RFQ) workflows.  
This is **not an e-commerce store** — users configure products and the system produces quotes and RFQ requests.

## Stack

| Layer    | Technologies                                   |
|----------|------------------------------------------------|
| Backend  | Laravel 12, PHP 8.2+, Filament, PostgreSQL     |
| Frontend | Next.js 16, React 19, Tailwind CSS, shadcn/ui  |
| Infra    | Docker, Redis, RabbitMQ, Mailpit               |

## Architecture

Modular monolith under `backend/modules/`:

- **Users** — authentication (Sanctum SPA), roles (admin / manager / sales / customer)
- **Catalog** — categories and products (API + admin panel)
- **Configurator** — steps, attributes, dependencies, configuration evaluation
- **Shared** — cross-cutting building blocks (media, API middleware, module conventions)

For details, see [`docs/`](docs/) and [`ai/`](ai/).

## Requirements

- Docker + Docker Compose
- Make (optional)

## Local setup

### 1. Environment

```bash
cp backend/.env.example backend/.env
# optional: adjust UID/GID in the root .env
```

### 2. Start containers and backend

```bash
make setup-dev
# or manually:
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --force
docker compose exec php php artisan db:seed
```

### 3. Frontend

```bash
docker compose exec node npm install
docker compose exec node npm run dev
```

### 4. URLs

| Service          | URL                          |
|------------------|------------------------------|
| API (backend)    | http://localhost             |
| Admin panel      | http://localhost/admin       |
| Frontend (SPA)   | http://localhost:3000        |
| Mailpit (email)  | http://localhost:8025        |
| pgAdmin          | http://localhost:5050        |
| RabbitMQ         | http://localhost:15672       |

### Demo accounts

After `db:seed`, demo users from `config/demo-users.php` are available (default password: `password`):

| Role     | Email                |
|----------|----------------------|
| admin    | admin@example.com    |
| manager  | manager@example.com  |
| sales    | sales@example.com    |
| customer | customer@example.com |

## Useful commands

```bash
make backend          # shell into the PHP container
make frontend         # shell into the Node container

docker compose exec php composer check   # tests + PHPStan + Pint + Rector
docker compose exec php php artisan test # PHPUnit only
```

## Repository layout

```
├── backend/          # Laravel API + Filament (modules in modules/)
├── frontend/         # Next.js SPA
├── docker/           # images and nginx/PHP configuration
├── docs/             # project documentation
├── ai/               # rules and context for AI assistants
└── scripts/          # developer scripts
```

## Status

Current module progress: [`docs/STATUS.md`](docs/STATUS.md).
