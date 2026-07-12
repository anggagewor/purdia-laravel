# Purdia — Knowledge Base

Internal documentation for architecture decisions, conventions, and development progress.

---

## Architecture Overview

Purdia uses a **Modular DDD** approach on top of Laravel. The philosophy:

- **Strict outside** — Module boundaries, contracts, DTOs, and error formats are non-negotiable
- **Flexible inside** — Each module can be as simple or complex as needed
- **Laravel native** — We use Eloquent, Sanctum, Gates, Middleware, Events. DDD lives in folder structure, not against the framework

### Directory Structure

```
src/                         → Domain layer (namespace: Purdia\)
├── Shared/                  → Shared kernel (contracts, DTOs, events, exceptions)
├── Identity/                → Authentication module
├── Authorization/           → RBAC module
└── [ModuleName]/            → Future modules follow same pattern

app/                         → Laravel glue (thin, wiring only)
├── Providers/
│   ├── AppServiceProvider.php
│   └── ModuleServiceProvider.php  → Registers all module providers
```

### Module Anatomy

Each module follows this structure (use only what's needed):

```
src/[ModuleName]/
├── Domain/
│   ├── Models/              → Eloquent models
│   ├── Contracts/           → Repository interfaces
│   ├── Enums/               → PHP enums (stored as string in DB)
│   └── Events/              → Internal domain events
├── Application/
│   ├── Actions/             → Use cases (single responsibility)
│   ├── DTOs/                → Plain readonly classes
│   └── Exceptions/          → Module-specific errors (extend DomainException)
├── Infrastructure/
│   ├── Providers/           → Module ServiceProvider
│   ├── Repositories/        → Concrete implementations
│   ├── Gateway/             → Gateway implementation (for cross-module access)
│   ├── Routes/              → Module route files
│   ├── Middleware/           → Module-specific middleware
│   └── Database/
│       ├── Migrations/
│       ├── Factories/
│       └── Seeders/
└── Presentation/
    ├── Controllers/         → Thin, dispatch to Actions
    ├── Requests/            → Laravel Form Requests (input validation)
    └── Resources/
        └── V1/              → API Resources (response contract)
```

---

## Conventions

### Cross-Module Communication

| Method | When | Direction |
|--------|------|-----------|
| Gateway (interface) | Need data from another module synchronously | Module A → Shared Contract → Module B implements |
| Domain Event | Side effects, fire-and-forget | Publisher dispatches, any module can listen |

**Rules:**
1. Module MUST NOT import classes directly from another module
2. Module CAN depend on `Purdia\Shared\Contracts\*` and `Purdia\Shared\DTOs\*`
3. Data crossing module boundary MUST be wrapped in a DTO
4. Events are sync by default, opt-in to queue via `ShouldQueue`

### Naming

| Thing | Convention | Example |
|-------|-----------|---------|
| Cross-module interface | `{Module}Gateway` | `IdentityGateway` |
| Use case | `{Verb}{Noun}Action` | `RegisterAction`, `AssignRoleToUserAction` |
| DTO | `{Purpose}DTO` | `RegisterDTO`, `AuthTokenDTO` |
| Exception | `{Description}Exception` | `InvalidCredentialsException` |
| Error code | `MODULE.ERROR_NAME` | `IDENTITY.INVALID_CREDENTIALS` |
| Permission | `{module}.{scope}.{resource}.{action}` | `orders.page.index.button.create` |

### DTOs

- Always `final readonly class`
- Constructor promotion only
- No methods, no logic — pure data carriers

### Enums

- PHP native `enum` with `string` backing
- Stored as `string` column in DB (NO `enum` type in migrations)
- Cast via Laravel model `casts()`

### Error Handling

All domain errors extend `Purdia\Shared\Exceptions\DomainException`:

```php
abstract class DomainException extends RuntimeException
{
    public readonly string $errorCode;
    public readonly int $httpStatus;
    public readonly array $context;
}
```

API response format:
```json
{
    "error": {
        "code": "MODULE.ERROR_NAME",
        "message": "Human readable message",
        "context": {}
    }
}
```

### API Response Format

Success:
```json
{
    "message": "Optional message",
    "data": { ... }
}
```

### Repository Pattern

- Interface defined in `Domain/Contracts/`
- Implementation in `Infrastructure/Repositories/`
- Bound in module's ServiceProvider
- Domain layer is storage-agnostic (doesn't know about MySQL, Mongo, etc)

### Authentication

- Laravel Sanctum (token-based)
- Stateless — no sessions
- Token rotation via refresh endpoint

### Authorization (RBAC)

- User has many Roles (many-to-many)
- Role has many Permissions (many-to-many)
- Effective permissions = union of all permissions from all assigned roles
- Permission check via middleware: `middleware('permission:permission.name')`
- Also available via Laravel Gate: `Gate::authorize('permission.name')`

---

## Modules

### Shared ✅

Foundation utilities used across all modules.

**Contains:**
- `Contracts/` — Cross-module interfaces (Gateway pattern)
- `DTOs/` — Shared data transfer objects
- `Events/` — Cross-module domain events
- `Exceptions/` — Base DomainException + API renderer
- `Support/` — ApiResponse helper

### Identity ✅

Authentication and user management.

**Endpoints:**
- `POST /api/auth/register` — Create account + get token
- `POST /api/auth/login` — Authenticate + get token
- `POST /api/auth/logout` — Revoke current token
- `POST /api/auth/refresh` — Rotate token (delete old, create new)
- `GET /api/auth/me` — Get authenticated user profile

**Models:** User

**Gateway:** `IdentityGateway` — resolveUser, resolveUserByEmail

### Authorization ✅

Role-based access control with granular permissions.

**Models:** Role, Permission

**Tables:** roles, permissions, role_permission, user_role

**Middleware:** `permission:{name}` — checks if authenticated user has permission

**Gateway:** `AuthorizationGateway` — userCan, userPermissions, userRoles

---

## Roadmap

### Phase 1 — Foundation ✅
- [x] Project setup (Laravel 13)
- [x] Modular DDD structure
- [x] Shared module (contracts, exceptions, helpers)
- [x] Identity module (auth: register, login, logout, refresh)
- [x] Authorization module (RBAC: roles, permissions, middleware)

### Phase 2 — Core Business (Planned)
- [ ] POS module
- [ ] Inventory module
- [ ] CRM module
- [ ] HRM module

### Phase 3 — Scale
- [ ] Multi-tenancy
- [ ] Audit logging
- [ ] API versioning (V2)
- [ ] Queue-based event processing

---

## Adding a New Module

1. Create folder structure under `src/{ModuleName}/`
2. Create `{ModuleName}ServiceProvider` in `Infrastructure/Providers/`
3. Register provider in `app/Providers/ModuleServiceProvider.php`
4. If other modules need data from this module:
   - Define Gateway interface in `src/Shared/Contracts/{ModuleName}/`
   - Implement in `src/{ModuleName}/Infrastructure/Gateway/`
   - Bind in ServiceProvider
5. Create migrations in `Infrastructure/Database/Migrations/`
6. Run `composer dump-autoload` and `php artisan migrate`

---

## Decision Log

| Date | Decision | Reason |
|------|----------|--------|
| 2026-07-12 | Namespace `Purdia\` | Brand identity |
| 2026-07-12 | API-only, no frontend | Decoupled, consumed by separate FE |
| 2026-07-12 | Plain readonly DTO | No dependencies, native PHP |
| 2026-07-12 | Sync events by default | Simple debugging, queue opt-in |
| 2026-07-12 | Gateway pattern for cross-module | Clear "entry point" semantics, no clash with Laravel Facade |
| 2026-07-12 | Interface on boundaries only | Repository, Gateway, External services. Actions stay concrete |
| 2026-07-12 | No DB enum type | PHP enum + string column. Avoid migration headaches |
| 2026-07-12 | Sanctum for auth | Laravel native, simple, revocable tokens |
| 2026-07-12 | Authorization as separate module | Auth ≠ Authorization. Different bounded contexts |
| 2026-07-12 | Se-native mungkin dengan Laravel | Upgrade-friendly, DDD di struktur bukan melawan framework |
