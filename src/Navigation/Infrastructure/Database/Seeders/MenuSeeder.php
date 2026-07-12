<?php

namespace Purdia\Navigation\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Navigation\Domain\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

        $menus = $this->getMenuStructure();

        foreach ($menus as $menu) {
            $this->createMenu($menu, $tenantId);
        }
    }

    private function createMenu(array $data, int $tenantId, ?int $parentId = null): void
    {
        $children = $data['children'] ?? [];
        unset($data['children']);

        $menu = Menu::updateOrCreate(
            ['tenant_id' => $tenantId, 'slug' => $data['slug']],
            array_merge($data, [
                'tenant_id' => $tenantId,
                'parent_id' => $parentId,
            ]),
        );

        foreach ($children as $child) {
            $this->createMenu($child, $tenantId, $menu->id);
        }
    }

    private function getMenuStructure(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'path' => '/dashboard',
                'icon' => 'layout-dashboard',
                'permission' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sales',
                'slug' => 'sales',
                'path' => null,
                'icon' => 'shopping-cart',
                'permission' => 'sales.page.index.view',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Orders',
                        'slug' => 'sales-orders',
                        'path' => '/sales/orders',
                        'icon' => 'file-text',
                        'permission' => 'sales.page.orders.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Invoices',
                        'slug' => 'sales-invoices',
                        'path' => '/sales/invoices',
                        'icon' => 'receipt',
                        'permission' => 'sales.page.invoices.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Quotations',
                        'slug' => 'sales-quotations',
                        'path' => '/sales/quotations',
                        'icon' => 'file-signature',
                        'permission' => 'sales.page.quotations.view',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Purchasing',
                'slug' => 'purchasing',
                'path' => null,
                'icon' => 'truck',
                'permission' => 'purchasing.page.index.view',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Purchase Orders',
                        'slug' => 'purchasing-orders',
                        'path' => '/purchasing/orders',
                        'icon' => 'clipboard-list',
                        'permission' => 'purchasing.page.orders.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Suppliers',
                        'slug' => 'purchasing-suppliers',
                        'path' => '/purchasing/suppliers',
                        'icon' => 'building-2',
                        'permission' => 'purchasing.page.suppliers.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Goods Receive',
                        'slug' => 'purchasing-goods-receive',
                        'path' => '/purchasing/goods-receive',
                        'icon' => 'package-check',
                        'permission' => 'purchasing.page.goods-receive.view',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Inventory',
                'slug' => 'inventory',
                'path' => null,
                'icon' => 'warehouse',
                'permission' => 'inventory.page.index.view',
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Products',
                        'slug' => 'inventory-products',
                        'path' => '/inventory/products',
                        'icon' => 'box',
                        'permission' => 'inventory.page.products.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Stock',
                        'slug' => 'inventory-stock',
                        'path' => '/inventory/stock',
                        'icon' => 'layers',
                        'permission' => 'inventory.page.stock.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Stock Movement',
                        'slug' => 'inventory-movement',
                        'path' => '/inventory/movement',
                        'icon' => 'arrow-left-right',
                        'permission' => 'inventory.page.movement.view',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Categories',
                        'slug' => 'inventory-categories',
                        'path' => '/inventory/categories',
                        'icon' => 'folder-tree',
                        'permission' => 'inventory.page.categories.view',
                        'sort_order' => 4,
                    ],
                    [
                        'name' => 'Brands',
                        'slug' => 'inventory-brands',
                        'path' => '/inventory/brands',
                        'icon' => 'tag',
                        'permission' => 'inventory.page.brands.view',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'name' => 'POS',
                'slug' => 'pos',
                'path' => '/pos',
                'icon' => 'monitor',
                'permission' => 'pos.page.index.view',
                'sort_order' => 5,
            ],
            [
                'name' => 'CRM',
                'slug' => 'crm',
                'path' => null,
                'icon' => 'users',
                'permission' => 'crm.page.index.view',
                'sort_order' => 6,
                'children' => [
                    [
                        'name' => 'Customers',
                        'slug' => 'crm-customers',
                        'path' => '/crm/customers',
                        'icon' => 'user-check',
                        'permission' => 'crm.page.customers.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Leads',
                        'slug' => 'crm-leads',
                        'path' => '/crm/leads',
                        'icon' => 'target',
                        'permission' => 'crm.page.leads.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Pipeline',
                        'slug' => 'crm-pipeline',
                        'path' => '/crm/pipeline',
                        'icon' => 'git-branch',
                        'permission' => 'crm.page.pipeline.view',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'HRM',
                'slug' => 'hrm',
                'path' => null,
                'icon' => 'briefcase',
                'permission' => 'hrm.page.index.view',
                'sort_order' => 7,
                'children' => [
                    [
                        'name' => 'Employees',
                        'slug' => 'hrm-employees',
                        'path' => '/hrm/employees',
                        'icon' => 'user',
                        'permission' => 'hrm.page.employees.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Attendance',
                        'slug' => 'hrm-attendance',
                        'path' => '/hrm/attendance',
                        'icon' => 'clock',
                        'permission' => 'hrm.page.attendance.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Leave',
                        'slug' => 'hrm-leave',
                        'path' => '/hrm/leave',
                        'icon' => 'calendar-off',
                        'permission' => 'hrm.page.leave.view',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Payroll',
                        'slug' => 'hrm-payroll',
                        'path' => '/hrm/payroll',
                        'icon' => 'banknote',
                        'permission' => 'hrm.page.payroll.view',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'path' => null,
                'icon' => 'landmark',
                'permission' => 'finance.page.index.view',
                'sort_order' => 8,
                'children' => [
                    [
                        'name' => 'Journals',
                        'slug' => 'finance-journals',
                        'path' => '/finance/journals',
                        'icon' => 'book-open',
                        'permission' => 'finance.page.journals.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Accounts',
                        'slug' => 'finance-accounts',
                        'path' => '/finance/accounts',
                        'icon' => 'credit-card',
                        'permission' => 'finance.page.accounts.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Reports',
                        'slug' => 'finance-reports',
                        'path' => '/finance/reports',
                        'icon' => 'bar-chart-3',
                        'permission' => 'finance.page.reports.view',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'path' => null,
                'icon' => 'settings',
                'permission' => 'settings.page.index.view',
                'sort_order' => 99,
                'children' => [
                    [
                        'name' => 'Company',
                        'slug' => 'settings-company',
                        'path' => '/settings/company',
                        'icon' => 'building',
                        'permission' => 'settings.page.company.view',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Branches',
                        'slug' => 'settings-branches',
                        'path' => '/settings/branches',
                        'icon' => 'git-fork',
                        'permission' => 'settings.page.branches.view',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Users & Roles',
                        'slug' => 'settings-users',
                        'path' => '/settings/users',
                        'icon' => 'shield',
                        'permission' => 'settings.page.users.view',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Menus',
                        'slug' => 'settings-menus',
                        'path' => '/settings/menus',
                        'icon' => 'list',
                        'permission' => 'settings.page.menus.view',
                        'sort_order' => 4,
                    ],
                    [
                        'name' => 'Configuration',
                        'slug' => 'settings-config',
                        'path' => '/settings/config',
                        'icon' => 'sliders-horizontal',
                        'permission' => 'settings.page.config.view',
                        'sort_order' => 5,
                    ],
                ],
            ],
        ];
    }
}
