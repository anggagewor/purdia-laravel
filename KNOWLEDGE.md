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

**Endpoints:**
- `GET /api/roles` — List all roles with permissions
- `POST /api/roles` — Create role
- `GET /api/roles/{id}` — Get role detail
- `PUT /api/roles/{id}` — Update role
- `DELETE /api/roles/{id}` — Delete role
- `PUT /api/roles/{id}/permissions` — Sync permissions to role
- `GET /api/permissions` — List all permissions
- `POST /api/permissions` — Create permission
- `GET /api/permissions/{id}` — Get permission detail
- `PUT /api/permissions/{id}` — Update permission
- `DELETE /api/permissions/{id}` — Delete permission

### Config ✅

Database-driven configuration system. Each module can store its own config without touching code.

**Design:**
- Grouped by module name (e.g., `pos`, `inventory`, `identity`)
- Global configs use group `general`
- Key uses dot notation within group for namespacing
- Unique constraint on `group` + `key` combo (same key name allowed in different groups)
- Values stored as text, typed on retrieval (string, boolean, integer, float, json, array)

**Models:** Config

**Tables:** configs (group, key, value, type)

**Gateway:** `ConfigGateway` — get, has (read-only for other modules)

**Endpoints:**
- `GET /api/configs` — List all config groups
- `GET /api/configs/{group}` — Get all configs in a group
- `PUT /api/configs/{group}` — Set a single config value
- `PUT /api/configs/{group}/bulk` — Bulk set multiple configs
- `DELETE /api/configs/{group}/{key}` — Delete a config entry

**Usage from other modules:**
```php
// Inject the gateway
public function __construct(
    private readonly ConfigGateway $config,
) {}

// Read config
$taxRate = $this->config->get('pos', 'tax_rate', 11);
$appName = $this->config->get('general', 'app.name', 'Purdia');
```

### Reference ✅

Master/reference data — countries, currencies. Read-only API, seeded from JSON.

**Models:** Country, Currency

**Tables:** countries, currencies

**Data:** 143 countries with their currencies, seeded via `CountrySeeder`

**Endpoints:**
- `GET /api/references/countries` — List countries (filter: region, search, active_only)
- `GET /api/references/countries/{id}` — Get country with currency
- `GET /api/references/currencies` — List currencies (filter: search, active_only)
- `GET /api/references/currencies/{id}` — Get single currency
- `GET /api/references/units` — List all unit categories with units
- `GET /api/references/units/convert?from=kg&to=g&value=5` — Convert between units
- `GET /api/references/units/{category-slug}` — Get units for a category

**Seeded Data:**
- 143 countries with currencies
- 7 unit categories: Weight, Length, Volume, Area, Temperature, Time, Piece
- 46 units with 104 conversion pairs (bidirectional)

**Unit Categories:**
| Category | Base Unit | Units |
|----------|-----------|-------|
| Weight | g | mg, g, kg, t, oz, lb |
| Length | m | mm, cm, m, km, in, ft, yd, mi |
| Volume | l | ml, l, m³, gal, qt, pt, cup, fl oz |
| Area | m² | mm², cm², m², ha, km², ft², ac |
| Temperature | °C | °C, °F, K (formula-based, no factor) |
| Time | s | ms, s, min, h, d, wk, mo, yr |
| Piece | pcs | pcs, dz, gr, pr, box, pack |

**Seeding:**
```bash
php artisan db:seed
```

**Lookup System:**

Unified endpoint buat ambil banyak reference data dalam 1 request:

```
GET /api/lookups?types=country,currency,gender,timezone
```

Response:
```json
{
  "data": {
    "country": [...],
    "currency": [...],
    "gender": [...],
    "timezone": [...]
  }
}
```

Available lookup types:
- `country` — dari tabel countries
- `currency` — dari tabel currencies
- `timezone` — dari tabel timezones
- `language` — dari tabel languages
- `tax-category` — dari tabel tax_categories
- `unit` — dari tabel unit_categories + units
- `gender` — dari lookup_items
- `religion` — dari lookup_items
- `marital-status` — dari lookup_items
- `blood-type` — dari lookup_items
- `education` — dari lookup_items
- `employment-status` — dari lookup_items

Single lookup: `GET /api/lookups/gender`

**Design:**
- Data yang punya field complex (timezone, language, tax) → tabel sendiri
- Data yang cuma id + name (gender, religion, dll) → generic `lookup_types` + `lookup_items`
- Semua bisa diakses via unified `/api/lookups` endpoint
- Extensible: tambahin lookup type baru cuma perlu insert data, nggak perlu code baru

---

## Roadmap

### Phase 1 — Foundation ✅
- [x] Project setup (Laravel 13)
- [x] Modular DDD structure
- [x] Shared module (contracts, exceptions, helpers)
- [x] Identity module (auth: register, login, logout, refresh)
- [x] Authorization module (RBAC: roles, permissions, middleware, full CRUD)
- [x] Config module (DB-driven config per module, grouped, dot notation)
- [x] Reference module (countries, currencies — seeded data)

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

## Architecture Notes & Anticipations

### Transaksi Lintas Modul (Eventual Consistency vs ACID)

Saat ini events di-dispatch secara sync — ini aman dan predictable. Tapi begitu traffic naik dan kita switch ke `ShouldQueue` (async), ada risk: pembayaran sukses tapi stok gagal dikurangi.

**Antisipasi:**
- Siapin **Saga pattern / Compensation handler** sebelum pindah ke async
- Setiap event yang melibatkan state change lintas module harus punya compensating action (rollback)
- Contoh: `PaymentCompleted` → reduce stock. Kalau reduce stock gagal → dispatch `StockReductionFailed` → trigger refund/reversal
- Pertimbangkan **Outbox pattern** — simpan event di DB dulu (guaranteed delivery), baru publish ke queue

**Rule:** Jangan switch ke async tanpa compensation mechanism yang jelas.

### Permission Enforcement di Backend

Permission granular (`orders.page.index.button.create`) sangat berguna buat frontend rendering. Tapi frontend BUKAN security layer.

**Rule:** Setiap action yang butuh permission WAJIB dicek di backend juga:
- Di route level via middleware: `->middleware('permission:orders.action.create')`
- Atau di Action class: `Gate::authorize('orders.action.create')`
- Frontend permission hanya untuk UX (hide/show), bukan security

**Convention:**
- Permission yang berhubungan dengan UI (button visibility) → scope `component`
- Permission yang berhubungan dengan API action → scope `action` atau `api`
- Backend HARUS enforce permission scope `action` dan `api`. Scope `component` dan `page` opsional di backend.

### Multi-Tenancy Preparation (Phase 3)

Meskipun multi-tenancy baru di Phase 3, dari sekarang semua tabel business domain (POS, Inventory, CRM, HRM) HARUS sudah include `tenant_id`.

**Rule untuk Phase 2:**
- Setiap migration tabel bisnis: WAJIB ada `$table->foreignId('tenant_id')->index()`
- Model base class nanti bisa di-scope otomatis via global scope
- Tabel yang TIDAK perlu tenant_id: configs (sudah grouped), roles/permissions (bisa shared atau per-tenant nanti)
- Untuk sekarang, `tenant_id` bisa di-default ke 1 (single tenant mode) sampai Phase 3 ready

**Catatan:** Identity module (users table) belum ada tenant_id — ini intentional. User bisa belong ke multiple tenant. Relasi user-tenant nanti dihandle via pivot table di Phase 3.

### Config & Multi-Tenancy Strategy

Config module yang sekarang (`configs` table) adalah **system-level config** — berlaku untuk seluruh system, bukan per-tenant. Tidak perlu tenant_id.

**Alasan:**
- Config ini untuk internal app (timezone, tax rate default, token expiry, dll)
- Tenant config punya behavior yang beda: butuh inheritance/fallback ke system default
- Campur di satu tabel bikin query ribet dan rawan bugs

**Plan:**

| Layer | Tabel | Scope | Kapan |
|-------|-------|-------|-------|
| System config | `configs` | Global, semua tenant sama | ✅ Phase 1 |
| Tenant config | `tenant_configs` (tenant_id, group, key, value, type) | Per-tenant, override system | Phase 3 |

**Resolution flow (Phase 3 nanti):**
```
Request config value
→ Cek tenant_configs (specific tenant override)
→ Kalau nggak ada, fallback ke configs (system default)
→ Kalau nggak ada juga, return default value dari code
```

**Rule:** Jangan tambahin tenant_id ke tabel `configs`. Kalau butuh per-tenant config, bikin tabel dan mekanisme terpisah.

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
| 2026-07-12 | Config module with DB storage | Avoid code changes for config. Grouped by module, dot-notation keys, typed values |
| 2026-07-12 | Global config group = "general" | Shared/non-module configs pakai group "general" |
| 2026-07-12 | Sync events dulu, async nanti + Saga | Jangan async tanpa compensation handler. Outbox pattern dipertimbangkan |
| 2026-07-12 | Permission enforce di backend WAJIB | Frontend permission cuma UX, backend harus enforce scope action/api |
| 2026-07-12 | tenant_id di semua tabel bisnis Phase 2 | Preparation multi-tenancy. Default 1 dulu, Phase 3 baru activate |
| 2026-07-12 | Config table tanpa tenant_id | System-level config. Tenant config nanti tabel terpisah (tenant_configs) dengan fallback mechanism |
| 2026-07-12 | Reference module untuk master data | Countries, currencies — seeded, read-only. Foundation buat module bisnis (POS, Inventory) |
| 2026-07-12 | Generic lookup system | Data simple (gender, religion, dll) pake lookup_types + lookup_items. Extensible tanpa code change |
| 2026-07-12 | Unified /api/lookups endpoint | Single request buat multiple reference data. Hemat API call buat form filling |
