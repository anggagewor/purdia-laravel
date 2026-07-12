# Purdia — Coding Rules & Conventions

Rules ini WAJIB diikuti untuk semua code yang ditulis dalam project ini.

---

## 0. AI Instructions

Sebelum generate code apapun:

1. Baca `KNOWLEDGE.md` — pahami arsitektur, keputusan, dan progress saat ini
2. Baca file ini (`coding-rules.md`) — ikuti semua convention tanpa exception
3. Ikuti pattern module yang sudah ada — consistency over cleverness
4. Jangan introduce arsitektur/pattern baru tanpa update Decision Log di `KNOWLEDGE.md` terlebih dahulu

Prinsip:
- **Kalau ragu, ikuti module yang sudah ada.** Jangan invent pattern baru.
- **Kalau butuh pattern baru** — update `KNOWLEDGE.md` dulu (decision log + rationale), baru implementasi.
- **Jangan bikin abstraksi kalau baru dipakai sekali.** Abstraksi earned, bukan direncanakan.
- **Jangan implement fitur masa depan** kecuali requirement saat ini benar-benar butuh. YAGNI.
- **Prefer boring code yang konsisten** daripada clever code yang cuma lu yang ngerti.

---

## 1. Module Boundary Rules (STRICT)

### 1.1 Layer Dependency (NON-NEGOTIABLE)
```
Foundation    → depends on: NOTHING above
Engines       → depends on: Foundation ONLY
Building Blocks → depends on: Foundation + Engines (via contracts)
Business Modules → depends on: Foundation + Engines + Building Blocks (via contracts)
```

- ❌ Foundation TIDAK BOLEH depend ke Engine/Block/Module
- ❌ Engine TIDAK BOLEH depend ke Engine lain (no circular)
- ❌ Building Block TIDAK BOLEH depend ke Building Block lain secara langsung
- ❌ Business Module TIDAK BOLEH implement logic yang harusnya di Engine
- ❌ Business Module TIDAK BOLEH import Business Module lain

### 1.2 Cross-Module Communication
- Module DILARANG import class langsung dari module lain
- Module HANYA BOLEH depend ke `Purdia\Shared\Contracts\*`, `Purdia\Shared\DTOs\*`, `Purdia\Shared\Events\*`
- Data yang cross module boundary WAJIB dibungkus DTO
- Komunikasi sync: lewat Gateway/Engine interface
- Komunikasi async: lewat Domain Event

### 1.3 Dependency Direction (within a module)
```
Presentation → Application → Domain ← Infrastructure
```
- Domain TIDAK BOLEH depend ke Infrastructure
- Domain TIDAK BOLEH depend ke Presentation
- Application boleh depend ke Domain
- Infrastructure implement interface dari Domain
- Presentation hanya memanggil Application layer

### 1.4 Module Isolation
- Tiap module punya ServiceProvider sendiri
- Tiap module punya route file sendiri
- Tiap module punya migration sendiri
- Tiap module register bindings sendiri di ServiceProvider

### 1.5 Engine Pattern
- Business module TIPIS — hanya orchestrate engines
- Business module NEVER implement domain logic sendiri
- Domain logic HARUS di Engine, di-expose via contract

---

## 2. Naming Conventions

### 2.1 Files & Classes

| Thing | Pattern | Example |
|-------|---------|---------|
| Module folder | PascalCase | `Identity`, `Authorization`, `Config` |
| Model | Singular PascalCase | `User`, `Role`, `Permission` |
| Controller | PascalCase + Controller | `AuthController`, `RoleController` |
| Action | {Verb}{Noun}Action | `RegisterAction`, `CreateRoleAction` |
| DTO | {Purpose}DTO | `RegisterDTO`, `LoginDTO`, `CreateRoleDTO` |
| Exception | {Description}Exception | `InvalidCredentialsException` |
| Gateway interface | {Module}Gateway | `IdentityGateway`, `AuthorizationGateway` |
| Engine interface | {Name}Engine | `PricingEngine`, `DocumentEngine`, `InventoryEngine` |
| Gateway impl | {Module}GatewayImpl | `IdentityGatewayImpl` |
| Repository interface | {Model}Repository | `UserRepository`, `RoleRepository` |
| Repository impl | Eloquent{Model}Repository | `EloquentUserRepository` |
| Request | {Action}{Model}Request | `CreateRoleRequest`, `LoginRequest` |
| Resource | {Model}Resource | `UserResource`, `RoleResource` |
| Enum | PascalCase (descriptive) | `UserStatus`, `PermissionScope` |
| ServiceProvider | {Module}ServiceProvider | `IdentityServiceProvider` |
| Middleware | PascalCase (descriptive) | `CheckPermission` |
| Event | PastTense | `UserRegistered`, `OrderCompleted` |

### 2.2 Database

| Thing | Pattern | Example |
|-------|---------|---------|
| Table | snake_case, plural | `users`, `roles`, `role_permission` |
| Pivot table | singular_singular (alphabetical) | `role_permission`, `user_role` |
| Column | snake_case | `created_at`, `is_active`, `email_verified_at` |
| Foreign key | {singular}_id | `user_id`, `role_id` |
| Boolean column | is_{adjective} atau has_{noun} | `is_active`, `has_verified` |
| Enum column | string type, NEVER DB enum | `status` → stored as 'active' |

### 2.3 API

| Thing | Pattern | Example |
|-------|---------|---------|
| URL | kebab-case, plural nouns | `/api/roles`, `/api/tax-categories` |
| Route name | dot.notation | `auth.login`, `roles.store` |
| Error code | MODULE.ERROR_NAME (SCREAMING_SNAKE) | `IDENTITY.INVALID_CREDENTIALS` |
| JSON keys | snake_case | `access_token`, `created_at` |
| Query params | snake_case | `?active_only=true&sort_by=name` |

### 2.4 Permission Naming
```
{module}.{scope}.{resource}.{action}
```
Examples:
- `orders.page.index.view` → bisa lihat halaman
- `orders.page.index.button.create` → bisa lihat tombol create
- `orders.action.create` → bisa execute create action (backend enforced)

---

## 3. Code Style Rules

### 3.1 PHP General
- PHP 8.3+ features boleh dipake (readonly, enums, named arguments, match, etc)
- Strict types: selalu type-hint parameters dan return types
- Use `final` untuk class yang tidak dimaksudkan untuk di-extend
- Use `readonly` untuk properties yang immutable
- Prefer `match` over `switch`
- Prefer early return over deep nesting

### 3.2 DTOs
```php
final readonly class CreateRoleDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
    ) {}
}
```
- SELALU `final readonly class`
- HANYA constructor promotion
- TIDAK ADA method/logic
- Nullable props pake `?Type` + default `null`

### 3.3 Actions
```php
class CreateRoleAction
{
    public function __construct(
        private readonly DependencyInterface $dep,
    ) {}

    public function execute(CreateRoleDTO $dto): Role
    {
        // business logic
    }
}
```
- Satu public method: `execute()`
- Dependencies via constructor injection (interface)
- Return typed value
- TIDAK perlu interface (concrete class, langsung testable)

### 3.4 Controllers
```php
class RoleController extends Controller
{
    public function store(CreateRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        $dto = new CreateRoleDTO(...);
        $result = $action->execute($dto);
        return ApiResponse::created(new RoleResource($result));
    }
}
```
- TIPIS — hanya: validate → build DTO → call action → return response
- TIDAK ADA business logic di controller
- Selalu return `JsonResponse`
- Gunakan `ApiResponse` helper

### 3.5 Models (Eloquent)
- Selalu define `$fillable` (JANGAN pake `$guarded = []`)
- Define `casts()` method untuk type casting
- Relationships pake return type `BelongsTo`, `HasMany`, etc
- Custom query logic → pake scope atau repository
- JANGAN taruh business logic di model

### 3.6 Exceptions
```php
class RoleNotFoundException extends DomainException
{
    public function __construct(string $roleId)
    {
        parent::__construct(
            errorCode: 'AUTHORIZATION.ROLE_NOT_FOUND',
            message: "Role with ID {$roleId} not found.",
            httpStatus: 404,
            context: ['role_id' => $roleId],
        );
    }
}
```
- SELALU extend `Purdia\Shared\Exceptions\DomainException`
- Error code format: `MODULE.ERROR_NAME`
- Message harus human-readable
- Context berisi data untuk debugging

### 3.7 Enums
```php
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
```
- SELALU backed enum (`: string` atau `: int`)
- Value pake snake_case untuk string
- Di migration: `$table->string('status')` — JANGAN `$table->enum()`
- Di model: cast via `casts()` method

### 3.8 Migrations
- JANGAN pake `enum()` column type
- Boolean columns: `$table->boolean('is_active')->default(true)`
- Selalu `cascadeOnDelete()` pada foreign keys kecuali ada alasan kuat
- Naming: `{timestamp}_create_{table}_table.php` atau `{timestamp}_add_{column}_to_{table}_table.php`
- Phase 2+ tabel bisnis: WAJIB ada `$table->foreignId('tenant_id')->index()`

---

## 4. API Rules

### 4.1 Response Format
Success:
```json
{"message": "...", "data": {...}}
```
Error:
```json
{"error": {"code": "MODULE.ERROR", "message": "...", "context": {}}}
```

### 4.2 HTTP Status Codes
| Status | When |
|--------|------|
| 200 | Success (GET, PUT, general) |
| 201 | Resource created (POST) |
| 204 | No content (DELETE) |
| 400 | Validation error / bad request |
| 401 | Unauthenticated |
| 403 | Forbidden (permission denied) |
| 404 | Not found |
| 409 | Conflict (duplicate) |
| 422 | Unprocessable entity (Laravel validation) |
| 500 | Server error |

### 4.3 Route Convention
- Prefix semua module routes dengan `api/`
- Group authenticated routes dengan `auth:sanctum` middleware
- Permission-protected routes tambah `permission:{name}` middleware
- Use `apiResource()` untuk standard CRUD
- Custom actions: verb explicit di URL (`POST /api/roles/{id}/permissions`)

---

## 5. Git & Commit Rules

### 5.1 Commit Message Format
```
type(scope): description

[optional body]
```

Types: `feat`, `fix`, `refactor`, `docs`, `chore`, `test`
Scope: module name (`identity`, `authorization`, `shared`, `config`, `reference`)

Examples:
- `feat(identity): add refresh token endpoint`
- `fix(authorization): fix permission check for nested roles`
- `refactor(shared): extract ApiResponse helper`
- `docs: update KNOWLEDGE.md with config decisions`

### 5.2 Branch Naming
```
{type}/{module}-{description}
```
Examples:
- `feat/identity-refresh-token`
- `fix/authorization-permission-check`
- `chore/reference-add-timezones`

---

## 6. Testing Rules (Future-Ready)

### 6.1 Structure
- Unit test: test Action/logic in isolation (mock repository)
- Feature test: hit endpoint, assert response + DB state
- Satu test file per Action atau per Controller

### 6.2 Naming
```
tests/
├── Unit/
│   └── Identity/
│       └── RegisterActionTest.php
└── Feature/
    └── Identity/
        └── AuthControllerTest.php
```

---

## 7. Dependency Injection Rules

### 7.1 Yang Pake Interface (WAJIB)
- Repository (data access boundary)
- Gateway (cross-module boundary)
- External service (third-party API, payment, etc)

### 7.2 Yang TIDAK Perlu Interface
- Action (concrete, langsung di-test)
- DTO (value object, no behavior)
- Controller (thin layer)
- Model (Eloquent native)

### 7.3 Binding Location
- SEMUA binding di ServiceProvider module masing-masing
- JANGAN bind di AppServiceProvider kecuali global

---

## 8. Forbidden Practices

- ❌ Direct cross-module class import
- ❌ Business logic di Controller
- ❌ Business logic di Model
- ❌ `$guarded = []` di Model
- ❌ DB `enum` type di migration
- ❌ Logic di DTO
- ❌ Hard-coded config values (pake Config module)
- ❌ Bypass permission di backend (frontend-only permission)
- ❌ Skip DTO saat data cross module boundary
- ❌ Register module provider di AppServiceProvider (harus di ModuleServiceProvider)
- ❌ Raw SQL tanpa alasan performance yang jelas (pake Eloquent)
- ❌ Return Eloquent model langsung dari API (HARUS lewat Resource)
