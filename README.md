# Purdia

Modular API platform built on Laravel 13 with Domain-Driven Design architecture.

## What is Purdia?

Purdia is a modular ERP-grade backend system designed for scalability and maintainability. Each business domain lives in its own isolated module with clear boundaries, strict contracts, and zero cross-module coupling.

## Architecture

```
src/
├── Shared/           Contracts, DTOs, Events — the glue between modules
├── Identity/         Authentication (register, login, logout, refresh)
├── Authorization/    RBAC with granular permission (per-page, per-component)
└── [Future modules]  POS, Inventory, CRM, HRM, ...
```

**Key principles:**
- Strict boundaries between modules — communication via Gateway contracts and Events only
- Flexible internals — each module decides its own complexity level
- Laravel native — Eloquent, Sanctum, Gates, Middleware. DDD in structure, not against framework
- API-only — no frontend, pure JSON responses with consistent error format
- Testable by design — interfaces at boundaries, concrete implementations swappable

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum (token-based auth)
- MariaDB / MySQL

## API Endpoints

```
POST   /api/auth/register    Register a new user
POST   /api/auth/login       Authenticate and get token
POST   /api/auth/logout      Revoke current token
POST   /api/auth/refresh     Rotate token
GET    /api/auth/me          Get authenticated user
```

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Documentation

See [KNOWLEDGE.md](./KNOWLEDGE.md) for architecture decisions, roadmap, and development progress.

## License

Proprietary. All rights reserved.
