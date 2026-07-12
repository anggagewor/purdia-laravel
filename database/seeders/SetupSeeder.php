<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Authorization\Domain\Models\Permission;
use Purdia\Authorization\Domain\Models\Role;
use Purdia\Identity\Domain\Models\User;
use Purdia\Navigation\Infrastructure\Database\Seeders\MenuSeeder;
use Purdia\Tenant\Domain\Models\Branch;
use Purdia\Tenant\Domain\Models\Tenant;
use Purdia\Tenant\Domain\Models\TenantUser;

class SetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->createTenant();
        $this->createRolesAndPermissions();
        $this->assignUserToTenant();
        $this->call(MenuSeeder::class);
    }

    private function createTenant(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'purdia'],
            [
                'name' => 'Purdia',
                'currency' => 'IDR',
                'locale' => 'id',
                'timezone' => 'Asia/Jakarta',
                'settings' => [
                    'allow_negative_stock' => false,
                    'tax' => 11,
                    'decimal' => 0,
                ],
            ],
        );

        Branch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'HQ'],
            [
                'name' => 'Head Office',
                'type' => 'office',
                'address' => 'Jakarta, Indonesia',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ],
        );
    }

    private function createRolesAndPermissions(): void
    {
        // Roles
        $owner = Role::updateOrCreate(['slug' => 'owner'], ['name' => 'Owner', 'description' => 'Full system access']);
        $admin = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'description' => 'Administrative access']);
        $manager = Role::updateOrCreate(['slug' => 'manager'], ['name' => 'Manager', 'description' => 'Branch manager']);
        $staff = Role::updateOrCreate(['slug' => 'staff'], ['name' => 'Staff', 'description' => 'Regular staff']);
        $cashier = Role::updateOrCreate(['slug' => 'cashier'], ['name' => 'Cashier', 'description' => 'POS cashier']);

        // Permissions
        $permissions = $this->getPermissions();
        $permissionIds = [];

        foreach ($permissions as $perm) {
            $p = Permission::updateOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description'], 'scope' => $perm['scope']],
            );
            $permissionIds[] = $p->id;
        }

        // Owner gets all permissions
        $owner->permissions()->sync($permissionIds);

        // Admin gets all except some finance
        $adminPerms = Permission::whereNotIn('name', [
            'finance.page.journals.view',
            'finance.page.reports.view',
        ])->pluck('id');
        $admin->permissions()->sync($adminPerms);
    }

    private function assignUserToTenant(): void
    {
        $user = User::first();
        if (! $user) {
            return;
        }

        $tenant = Tenant::where('slug', 'purdia')->first();
        $ownerRole = Role::where('slug', 'owner')->first();

        if ($tenant && $ownerRole) {
            TenantUser::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $user->id, 'role_id' => $ownerRole->id],
                ['is_active' => true],
            );
        }
    }

    private function getPermissions(): array
    {
        return [
            // Dashboard
            ['name' => 'dashboard.page.view', 'description' => 'View dashboard', 'scope' => 'page'],

            // Sales
            ['name' => 'sales.page.index.view', 'description' => 'View sales section', 'scope' => 'page'],
            ['name' => 'sales.page.orders.view', 'description' => 'View sales orders', 'scope' => 'page'],
            ['name' => 'sales.page.invoices.view', 'description' => 'View invoices', 'scope' => 'page'],
            ['name' => 'sales.page.quotations.view', 'description' => 'View quotations', 'scope' => 'page'],
            ['name' => 'sales.action.order.create', 'description' => 'Create sales order', 'scope' => 'action'],
            ['name' => 'sales.action.order.edit', 'description' => 'Edit sales order', 'scope' => 'action'],
            ['name' => 'sales.action.order.delete', 'description' => 'Delete sales order', 'scope' => 'action'],
            ['name' => 'sales.action.invoice.create', 'description' => 'Create invoice', 'scope' => 'action'],

            // Purchasing
            ['name' => 'purchasing.page.index.view', 'description' => 'View purchasing section', 'scope' => 'page'],
            ['name' => 'purchasing.page.orders.view', 'description' => 'View purchase orders', 'scope' => 'page'],
            ['name' => 'purchasing.page.suppliers.view', 'description' => 'View suppliers', 'scope' => 'page'],
            ['name' => 'purchasing.page.goods-receive.view', 'description' => 'View goods receive', 'scope' => 'page'],
            ['name' => 'purchasing.action.order.create', 'description' => 'Create purchase order', 'scope' => 'action'],
            ['name' => 'purchasing.action.order.approve', 'description' => 'Approve purchase order', 'scope' => 'action'],

            // Inventory
            ['name' => 'inventory.page.index.view', 'description' => 'View inventory section', 'scope' => 'page'],
            ['name' => 'inventory.page.products.view', 'description' => 'View products', 'scope' => 'page'],
            ['name' => 'inventory.page.stock.view', 'description' => 'View stock', 'scope' => 'page'],
            ['name' => 'inventory.page.movement.view', 'description' => 'View stock movement', 'scope' => 'page'],
            ['name' => 'inventory.page.categories.view', 'description' => 'View product categories', 'scope' => 'page'],
            ['name' => 'inventory.page.brands.view', 'description' => 'View brands', 'scope' => 'page'],
            ['name' => 'inventory.action.product.create', 'description' => 'Create product', 'scope' => 'action'],
            ['name' => 'inventory.action.product.edit', 'description' => 'Edit product', 'scope' => 'action'],
            ['name' => 'inventory.action.product.delete', 'description' => 'Delete product', 'scope' => 'action'],
            ['name' => 'inventory.action.adjustment.create', 'description' => 'Create stock adjustment', 'scope' => 'action'],

            // POS
            ['name' => 'pos.page.index.view', 'description' => 'Access POS', 'scope' => 'page'],
            ['name' => 'pos.action.transaction.create', 'description' => 'Create POS transaction', 'scope' => 'action'],
            ['name' => 'pos.action.transaction.void', 'description' => 'Void POS transaction', 'scope' => 'action'],
            ['name' => 'pos.action.discount.apply', 'description' => 'Apply manual discount in POS', 'scope' => 'action'],

            // CRM
            ['name' => 'crm.page.index.view', 'description' => 'View CRM section', 'scope' => 'page'],
            ['name' => 'crm.page.customers.view', 'description' => 'View customers', 'scope' => 'page'],
            ['name' => 'crm.page.leads.view', 'description' => 'View leads', 'scope' => 'page'],
            ['name' => 'crm.page.pipeline.view', 'description' => 'View pipeline', 'scope' => 'page'],
            ['name' => 'crm.action.customer.create', 'description' => 'Create customer', 'scope' => 'action'],
            ['name' => 'crm.action.lead.create', 'description' => 'Create lead', 'scope' => 'action'],

            // HRM
            ['name' => 'hrm.page.index.view', 'description' => 'View HRM section', 'scope' => 'page'],
            ['name' => 'hrm.page.employees.view', 'description' => 'View employees', 'scope' => 'page'],
            ['name' => 'hrm.page.attendance.view', 'description' => 'View attendance', 'scope' => 'page'],
            ['name' => 'hrm.page.leave.view', 'description' => 'View leave', 'scope' => 'page'],
            ['name' => 'hrm.page.payroll.view', 'description' => 'View payroll', 'scope' => 'page'],
            ['name' => 'hrm.action.employee.create', 'description' => 'Create employee', 'scope' => 'action'],
            ['name' => 'hrm.action.leave.approve', 'description' => 'Approve leave request', 'scope' => 'action'],

            // Finance
            ['name' => 'finance.page.index.view', 'description' => 'View finance section', 'scope' => 'page'],
            ['name' => 'finance.page.journals.view', 'description' => 'View journals', 'scope' => 'page'],
            ['name' => 'finance.page.accounts.view', 'description' => 'View chart of accounts', 'scope' => 'page'],
            ['name' => 'finance.page.reports.view', 'description' => 'View financial reports', 'scope' => 'page'],
            ['name' => 'finance.action.journal.create', 'description' => 'Create journal entry', 'scope' => 'action'],

            // Settings
            ['name' => 'settings.page.index.view', 'description' => 'View settings section', 'scope' => 'page'],
            ['name' => 'settings.page.company.view', 'description' => 'View company settings', 'scope' => 'page'],
            ['name' => 'settings.page.branches.view', 'description' => 'View branches', 'scope' => 'page'],
            ['name' => 'settings.page.users.view', 'description' => 'View users & roles', 'scope' => 'page'],
            ['name' => 'settings.page.menus.view', 'description' => 'View menu management', 'scope' => 'page'],
            ['name' => 'settings.page.config.view', 'description' => 'View configuration', 'scope' => 'page'],
            ['name' => 'settings.action.role.manage', 'description' => 'Manage roles & permissions', 'scope' => 'action'],
            ['name' => 'settings.action.user.manage', 'description' => 'Manage users', 'scope' => 'action'],
        ];
    }
}
