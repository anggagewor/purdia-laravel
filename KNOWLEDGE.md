# Purdia — Knowledge Base

> **Composable Business Platform** — bukan ERP biasa, tapi platform untuk membangun aplikasi bisnis dengan domain yang dipisah berdasarkan kapabilitas inti.

---

## Identity & Vision

Purdia bukan kumpulan modul ERP. Purdia adalah sekumpulan **bounded context kecil** yang bisa dikomposisi menjadi aplikasi bisnis apapun.

**Prinsip utama:**
- Modul dipisah berdasarkan **kapabilitas**, bukan berdasarkan menu atau departemen
- Business module itu TIPIS — cuma orchestrate engines
- Jangan bikin modul berdasarkan nama menu (Customer, Supplier, Employee). Yang benar: Party.
- Jangan bikin modul berdasarkan proses (Purchase, Sales, POS). Yang benar: compose Inventory + Pricing + Document.

---

## Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│  LAYER 3: Business Modules (thin orchestration)             │
│  CRM, POS, Sales, Purchasing, HRM, Finance, Manufacturing  │
├─────────────────────────────────────────────────────────────┤
│  LAYER 2: Building Blocks (shared business entities)        │
│  Party, Catalog, Classification, Comment, Activity,         │
│  Audit, Dimension                                           │
├─────────────────────────────────────────────────────────────┤
│  LAYER 1: Engines (domain logic core)                       │
│  Document, Workflow, Pricing, Tax, Inventory, Notification  │
├─────────────────────────────────────────────────────────────┤
│  LAYER 0: Foundation (infrastructure)                       │
│  Shared, Identity, Authorization, Tenant, Config,           │
│  Storage, Reference                                         │
└─────────────────────────────────────────────────────────────┘
```

### Dependency Rules (STRICT — NON-NEGOTIABLE)

```
Foundation    → depends on: NOTHING above
Engines       → depends on: Foundation ONLY
Building Blocks → depends on: Foundation + Engines (via contracts)
Business Modules → depends on: Foundation + Engines + Building Blocks (via contracts)
```

**FORBIDDEN:**
- ❌ Foundation depend ke Engine/Block/Module
- ❌ Engine depend ke Engine lain (no circular)
- ❌ Building Block depend ke Building Block lain secara langsung
- ❌ Business Module implement logic sendiri yang harusnya di Engine
- ❌ Business Module import Business Module lain

**ALLOWED:**
- ✅ Business Module orchestrate multiple Engines
- ✅ Building Block consume Engine via contract
- ✅ Cross-layer communication via Shared contracts/events only

---

## Layer 0: Foundation (DONE)

| Module | Status | Description |
|--------|--------|-------------|
| Shared | ✅ | Contracts, DTOs, events, traits, exceptions, helpers |
| Identity | ✅ | Auth (register, login, logout, refresh). Sanctum. |
| Authorization | ✅ | RBAC (roles, permissions, granular). Gate + Middleware. |
| Tenant | ✅ | Multi-company, branch (store/warehouse/office/factory/virtual), context |
| Config | ✅ | DB-driven settings. Grouped per module. |
| Storage | ✅ | File management, access control, storage rules, attachment |
| Reference | ✅ | Countries, currencies, units, timezones, languages, tax categories, lookups |

---

## Layer 1: Engines (Planned)

Engines = **pure domain logic**. Mereka nggak punya UI. Mereka expose contract/interface yang dipake layer di atasnya.

| Engine | Contains | Used By |
|--------|----------|---------|
| **Document** | Sequence, Numbering, Lifecycle (status), Revision | ALL business modules |
| **Workflow** | State Machine, Transition, Condition, Action | Purchasing, HRM, Finance, any approval flow |
| **Pricing** | Price List, Discount, Promotion, Price Resolution | POS, Sales, CRM, B2B |
| **Tax** | Tax Rule, Tax Group, Tax Rate, Tax Authority, Tax Area | POS, Sales, Purchasing, Finance |
| **Inventory** | Stock, Movement, Reservation, Adjustment | POS, Sales, Purchasing, Manufacturing |
| **Notification** | Channel (email/WA/push/SMS/slack), Template, Dispatch | ALL |

### Engine Pattern

```php
// Business module NEVER calculates — it delegates to engine
$price = $pricingEngine->resolve($product, $customer, $branch);
$tax = $taxEngine->calculate($price, $taxRules);
$reservation = $inventoryEngine->reserve($product, $qty, $branch);
$number = $documentEngine->generate('INV', $branch);
$workflow = $workflowEngine->submit($document);
```

### Key Design Notes

**Document Engine:**
- Sequence: configurable numbering format (`INV-BDG-{YYYY}{MM}-{####}`)
- Lifecycle: Draft → Submitted → Approved → Rejected → Cancelled → Archived
- Revision: version tracking (Quotation Rev 1, Rev 2, Rev 3)
- Document nyimpan state. Workflow ngatur transition rules.

**Workflow Engine:**
- Generic state machine, BUKAN hardcode approval
- Transition → Condition → Action
- Condition: `amount > 10_000_000`, `country == 'ID'`, `branch.type == 'warehouse'`
- Action: approve, reject, notify, assign, trigger event

**Inventory Engine:**
- Cuma tau Movement (+/-). Nggak peduli sumber (POS, Purchase, Manufacturing).
- Stock ≠ Available. Available = Stock - Reserved.
- Reservation layer terpisah. Inventory nggak tau siapa yang reserve.

**Pricing Engine:**
- Resolve harga berdasarkan: product, customer, branch, qty, date
- Apply discount rules, promotion rules
- Final price = Pricing.resolve() + Tax.calculate()

**Tax Engine:**
- Eventually pisah dari Pricing (complex enough for own module)
- Support multi-scheme: VAT, GST, PPN, Sales Tax, Withholding Tax
- Tax Rule, Tax Group, Tax Authority, Tax Rate, Tax Area

**Notification Engine:**
- Channel-based (pluggable): Email, WA, Push, SMS, Slack, Discord
- Template engine: satu template, render ke multiple channels
- Register channel kayak plugin

---

## Layer 2: Building Blocks (Planned)

Building Blocks = **shared business entities**. Dipakai banyak module.

| Block | Contains | Used By |
|-------|----------|---------|
| **Party** | Person, Organization, Contact, Address, Relationship, Business Role | CRM, HRM, Purchasing, Sales, ALL |
| **Catalog** | Product, Category, Brand, Variant, Attribute, Barcode | POS, Inventory, Sales, Manufacturing |
| **Classification** | Tag, Label, Custom Field | ALL (polymorphic) |
| **Comment** | Comment, Mention, Reaction, Thread, Resolve | CRM, Project, Ticket, HR |
| **Activity** | Business timeline (event-driven) | ALL |
| **Audit** | Field-level change tracking | ALL |
| **Dimension** | Cost Center, Department, Project, Region, Business Unit, Profit Center | Finance, HRM, Inventory, Sales |

### Key Design Notes

**Party:**
```
parties (id, type, display_name)
├── persons (party_id, first_name, last_name, birth_date, gender, ...)
└── organizations (party_id, legal_name, tax_number, npwp, nib, ...)

contacts (party_id, type, value) — email, phone, etc
addresses (party_id, type, ...) — billing, shipping, etc
relationships (party_a_id, party_b_id, type) — owns, works_for, reports_to
```

- Customer = Party with business role "customer"
- Supplier = Party with business role "supplier"  
- Employee = Party with business role "employee"
- Party itu identity. Business role itu label.

**Catalog:**
- Product NGGAK punya image langsung. Image lewat Storage → Attachment (polymorphic).
- Catalog nggak tau file. Catalog nggak tau harga. Harga di Pricing Engine.

**Activity:**
- Source of truth = Domain Event. Jangan `Activity::create()` di mana-mana.
- Activity subscribe ke events: `InvoiceCreated → Activity`, `PaymentReceived → Activity`

**Dimension:**
- Generic entity untuk analitik/grouping
- Finance pake buat jurnal (cost center, profit center)
- HRM pake buat department
- Inventory pake buat warehouse region
- Sales pake buat territory
- Satu konsep, lintas modul

---

## Layer 3: Business Modules (Future)

Business modules = **thin orchestration**. Mereka compose engines dan building blocks.

| Module | Orchestrates | Description |
|--------|-------------|-------------|
| **CRM** | Party + Pricing + Document + Activity + Classification | Lead, Opportunity, Pipeline, Quotation |
| **POS** | Party + Catalog + Pricing + Tax + Inventory + Document | Cart, Checkout, Receipt |
| **Sales** | Party + Catalog + Pricing + Tax + Inventory + Document + Workflow | SO, Invoice, Payment |
| **Purchasing** | Party + Catalog + Pricing + Tax + Inventory + Document + Workflow | PO, Goods Receive |
| **HRM** | Party + Document + Workflow + Dimension | Employee, Leave, Payroll, Attendance |
| **Finance** | Document + Dimension + Tax + Party | Journal, Account, Reconciliation |
| **Manufacturing** | Catalog + Inventory + Document + Workflow | BOM, Work Order, Production |
| **Project** | Party + Activity + Comment + Dimension | Task, Milestone, Timesheet |

---

## Module Structure

```
src/[ModuleName]/
├── Domain/
│   ├── Models/              → Eloquent models
│   ├── Contracts/           → Repository/Engine interfaces
│   ├── Enums/               → PHP enums (string-backed, NO DB enum)
│   └── Events/              → Internal domain events
├── Application/
│   ├── Actions/             → Use cases (single responsibility)
│   ├── DTOs/                → Plain readonly classes
│   ├── Exceptions/          → Extend DomainException
│   └── Engine/              → Engine implementation (Layer 1 only)
├── Infrastructure/
│   ├── Providers/           → Module ServiceProvider
│   ├── Repositories/        → Concrete implementations
│   ├── Gateway/             → Gateway/Context adapter
│   ├── Routes/              → Module route files
│   ├── Middleware/
│   └── Database/
│       ├── Migrations/
│       ├── Factories/
│       └── Seeders/
└── Presentation/
    ├── Controllers/         → Thin, dispatch to Actions
    ├── Requests/            → Laravel Form Requests
    └── Resources/
        └── V1/              → API Resources (response contract)
```

---

## Conventions

### Cross-Module Communication

| Method | When |
|--------|------|
| Gateway/Contract (sync) | Need data from another module |
| Domain Event (async-ready) | Side effects, fire-and-forget |
| TenantContext | Access current tenant/branch |
| Engine contract | Delegate domain logic |

### Naming

| Thing | Convention | Example |
|-------|-----------|---------|
| Engine interface | `{Name}Engine` | `PricingEngine`, `DocumentEngine` |
| Gateway interface | `{Module}Gateway` | `IdentityGateway` |
| Action | `{Verb}{Noun}Action` | `RegisterAction`, `CreateRoleAction` |
| DTO | `{Purpose}DTO` | `RegisterDTO`, `CreateBranchDTO` |
| Exception | `{Description}Exception` | `InvalidCredentialsException` |
| Error code | `MODULE.ERROR_NAME` | `IDENTITY.INVALID_CREDENTIALS` |
| Permission | `{module}.{scope}.{resource}.{action}` | `orders.action.create` |

### DTOs
- Always `final readonly class`
- Constructor promotion only
- No methods, no logic

### Enums
- PHP native `enum` with `string` or `int` backing
- Stored as `string` column in DB (NEVER `enum()` in migration)
- Cast via `casts()` method

### Error Handling
```json
{"error": {"code": "MODULE.ERROR", "message": "...", "context": {}}}
```

### API Response
```json
{"message": "...", "data": {...}}
```

### Models (Phase 2+ business tables)
- WAJIB: `use BelongsToTenant, HasAudit, SoftDeletes;`
- WAJIB: `tenant_id` column
- Optional: `branch_id` column (for branch-scoped data)

---

## Current Progress

### Foundation (Layer 0) — ✅ COMPLETE
- [x] Project setup (Laravel 13, Sanctum)
- [x] Modular DDD structure
- [x] Shared module (contracts, exceptions, helpers, traits)
- [x] Identity module (auth: register, login, logout, refresh)
- [x] Authorization module (RBAC: roles, permissions, middleware, CRUD)
- [x] Tenant module (multi-company, branch, context, resolver chain)
- [x] Config module (DB-driven, grouped, dot notation)
- [x] Storage module (file management, access control, storage rules)
- [x] Reference module (countries, currencies, units, timezones, languages, lookups)

### Engines (Layer 1) — NEXT
- [ ] Document Engine (sequence, lifecycle, revision)
- [ ] Party (person, organization, contact, address, relationship)
- [ ] Catalog (product, category, brand, variant, attribute, barcode)
- [ ] Pricing Engine (price list, discount, promotion)
- [ ] Tax Engine (tax rule, group, rate, authority)
- [ ] Inventory Engine (stock, movement, reservation, adjustment)
- [ ] Workflow Engine (state machine, transition, condition, action)
- [ ] Classification (tag, label, custom field)
- [ ] Activity (event-driven timeline)
- [ ] Dimension (cost center, department, project, region)
- [ ] Notification Engine (channel, template, dispatch)
- [ ] Comment (comment, mention, reaction, thread)
- [ ] Audit (field-level change tracking)

### Business Modules (Layer 3) — FUTURE
- [ ] CRM
- [ ] POS
- [ ] Sales
- [ ] Purchasing
- [ ] HRM
- [ ] Finance
- [ ] Manufacturing
- [ ] Project

---

## Shared Traits

### BelongsToTenant
- Auto global scope (filter by current tenant)
- Auto-set `tenant_id` on create
- `::withoutTenantScope()` for admin operations

### HasAudit
- Auto-set `created_by`, `updated_by`, `deleted_by`
- Via model events (observer pattern)
- Developer nggak perlu mikir

---

## Tenant Design

- 1 Tenant = 1 perusahaan/bisnis
- Branch = cabang/toko/gudang/kantor/factory (type enum)
- User many-to-many Tenant (1 user bisa handle banyak company)
- Role per-tenant (beda role di beda company)
- Branch access via separate pivot (`branch_users`)
- No `owner_id` — owner = role
- Settings inheritance: Branch → Tenant → System Config → default
- Resolver chain: Header → Subdomain → JWT (extensible)
- Context: `TenantContext` (single source of truth)
- UI: tampilkan "Company" bukan "Tenant"

---

## Settings Inheritance

```
Branch.setting('tax')     → cek branch settings JSON
  ↓ fallback
Tenant.setting('tax')     → cek tenant settings JSON
  ↓ fallback
Config.get('general', 'tax')  → cek system config DB
  ↓ fallback
default value dari code
```

---

## Adding a New Module

1. Determine layer (Foundation / Engine / Building Block / Business)
2. Create folder structure under `src/{ModuleName}/`
3. Create `{ModuleName}ServiceProvider` in `Infrastructure/Providers/`
4. Register provider in `app/Providers/ModuleServiceProvider.php`
5. If cross-module access needed: define interface in `src/Shared/Contracts/{ModuleName}/`
6. Create migrations in `Infrastructure/Database/Migrations/`
7. Run `composer dump-autoload` and `php artisan migrate`
8. Update this file (KNOWLEDGE.md) with module documentation

**Coding rules & conventions:** See `.kiro/steering/coding-rules.md`

---

## Decision Log

| Date | Decision | Reason |
|------|----------|--------|
| 2026-07-12 | Namespace `Purdia\` | Brand identity |
| 2026-07-12 | API-only, no frontend | Decoupled, consumed by separate FE |
| 2026-07-12 | Composable Business Platform positioning | Bukan ERP biasa. Building blocks yang di-compose. |
| 2026-07-12 | 4-layer architecture (Foundation → Engine → Block → Module) | Strict dependency direction. No circular. |
| 2026-07-12 | Engine pattern (domain logic core) | Business module tipis. Delegate ke engine. |
| 2026-07-12 | Plain readonly DTO | No dependencies, native PHP |
| 2026-07-12 | Sync events by default, queue opt-in + Saga | Jangan async tanpa compensation handler |
| 2026-07-12 | Gateway pattern for cross-module | Clear "entry point" semantics |
| 2026-07-12 | Interface on boundaries only | Repository, Gateway, Engine. Actions stay concrete. |
| 2026-07-12 | No DB enum type | PHP enum + string column |
| 2026-07-12 | Sanctum for auth | Laravel native, simple, revocable tokens |
| 2026-07-12 | Authorization as separate module | Auth ≠ Authorization |
| 2026-07-12 | Se-native mungkin dengan Laravel | DDD di struktur, bukan melawan framework |
| 2026-07-12 | Tenant = Perusahaan, Branch = Cabang | 1 tenant = 1 bisnis. Toko/gudang = branch. |
| 2026-07-12 | No owner_id di tenant | Owner = role. Flexible kalau ownership berubah. |
| 2026-07-12 | User-Tenant many-to-many | User bisa handle banyak company |
| 2026-07-12 | Branch access via separate pivot | Flexible multi-branch tanpa duplicate role |
| 2026-07-12 | TenantContext as single source of truth | Module lain cuma akses via contract |
| 2026-07-12 | Resolver chain (Header → Subdomain → JWT) | Extensible, not locked |
| 2026-07-12 | Settings inheritance: Branch → Tenant → System | Fallback chain |
| 2026-07-12 | HasAudit trait (created_by, updated_by, deleted_by) | Auto via model events |
| 2026-07-12 | BelongsToTenant trait (auto scope + set) | All queries tenant-safe |
| 2026-07-12 | Soft delete semua entity bisnis | ERP never really deletes |
| 2026-07-12 | Party = all actors (person/org). Business role = label. | Customer, Supplier, Employee = Party with role |
| 2026-07-12 | Party split: parties + persons + organizations | Avoid table monster. Class table inheritance. |
| 2026-07-12 | Catalog nggak tau file | Image via Storage → Attachment (polymorphic) |
| 2026-07-12 | Tax eventually separate from Pricing | Complex enough for own domain |
| 2026-07-12 | Document = Sequence + Lifecycle + Revision | Bukan cuma numbering |
| 2026-07-12 | Workflow = generic state machine | Transition → Condition → Action. Not just approval. |
| 2026-07-12 | Inventory = Movement only | Nggak peduli sumber. +/- aja. |
| 2026-07-12 | Stock ≠ Available. Reservation layer. | Available = Stock - Reserved |
| 2026-07-12 | Activity = event-driven, bukan manual create | Subscribe domain events |
| 2026-07-12 | Notification = channel-based + template | Pluggable channels |
| 2026-07-12 | Dimension = generic analytic entity | Cost center, dept, project — satu konsep lintas modul |
| 2026-07-12 | Classification (bukan Tag) | Tag + Label + Custom Field |
| 2026-07-12 | Build order: Document → Party → Catalog → Pricing → Tax → Inventory → Workflow | Foundation capabilities first |
| 2026-07-12 | Config table tanpa tenant_id | System-level. Tenant config = separate table nanti. |
| 2026-07-12 | Generic lookup system | lookup_types + lookup_items. Extensible tanpa code. |
| 2026-07-12 | Unified /api/lookups endpoint | Single request buat multiple reference data |
| 2026-07-12 | Storage rules buat auto-routing | File ke disk/path berdasarkan mime/extension |
| 2026-07-12 | File access: public/private/restricted | Per user/role, level: read_only/read_write/full_control |
| 2026-07-12 | Branch type enum + code + hierarchy | store/warehouse/office/factory/virtual. parent_branch_id. |
| 2026-07-12 | Permission enforce backend WAJIB | Frontend permission cuma UX |
| 2026-07-12 | UI: "Company" bukan "Tenant" | User-friendly naming |
