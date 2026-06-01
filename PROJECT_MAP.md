# PROJECT MAP — Moody's Business Management SaaS

## [TECH_STACK]

| Component | Version | Status |
|-----------|---------|--------|
| PHP | 8.2.12 | ✅ |
| Laravel Framework | 12.61.0 | ✅ |
| MySQL (MariaDB) | 10.4.32 | ✅ |
| Composer | 2.9.5 | ✅ |
| Node.js | 24.14.1 | ✅ |
| NPM | 11.11.0 | ✅ |
| Bootstrap 5.3 RTL | 5.3.3 | ✅ |
| Font Awesome 6 | 6.5.1 | ✅ |
| PHPUnit | 11.5.55 | ✅ |

## [ARCHITECTURE]

```
app/
├── Domains/
│   ├── Core/
│   │   ├── Traits/
│   │   │   ├── HasTenantScope.php      # Global scope by tenant_id
│   │   │   └── HasCreatorUpdater.php   # Auto-fill created_by/updated_by
│   │   └── Helpers/
│   │       ├── CurrencyHelper.php      # Currency formatting
│   │       └── DateHelper.php          # Arabic date formatting
│   ├── Auth/
│   │   ├── Controllers/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php  # Creates Tenant + User
│   │   │   ├── ForgotPasswordController.php
│   │   │   ├── ResetPasswordController.php
│   │   │   ├── DashboardController.php
│   │   │   └── SettingsController.php
│   │   ├── Models/
│   │   │   ├── Tenant.php
│   │   │   ├── User.php                # Extends Authenticatable
│   │   │   └── ActivityLog.php
│   │   └── Middleware/
│   │       ├── CheckTenant.php         # Enforces tenant context
│   │       └── CheckRole.php           # Role-based access
│   ├── Sales/
│   │   ├── Controllers/
│   │   │   ├── SalesController.php     # POS + Order CRUD
│   │   │   ├── OrderSessionController.php
│   │   │   └── PaymentController.php
│   │   ├── Models/
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   ├── OrderSession.php
│   │   │   └── Payment.php
│   │   └── Services/                   # (future: receipt generation)
│   ├── Expenses/
│   │   ├── Controllers/
│   │   │   ├── ExpenseController.php
│   │   │   └── ExpenseCategoryController.php
│   │   └── Models/
│   │       ├── Expense.php
│   │       └── ExpenseCategory.php
│   ├── Inventory/
│   │   ├── Controllers/
│   │   │   ├── ProductController.php
│   │   │   ├── ProductCategoryController.php
│   │   │   ├── SupplierController.php
│   │   │   └── PurchaseController.php
│   │   └── Models/
│   │       ├── Product.php
│   │       ├── ProductCategory.php
│   │       ├── Supplier.php
│   │       ├── Purchase.php
│   │       ├── PurchaseItem.php
│   │       └── StockMovement.php
│   ├── Invoicing/
│   │   ├── Controllers/
│   │   │   └── InvoiceController.php
│   │   ├── Models/
│   │   │   ├── Invoice.php
│   │   │   └── InvoiceItem.php
│   │   └── Services/
│   └── Reports/
│       ├── Controllers/
│       │   └── ReportController.php
│       └── Services/                   # (future: export engines)
├── Providers/
│   └── DomainServiceProvider.php
├── Http/Middleware/                     # (empty; middleware in Domains)
├── Console/                             # (future: artisan commands)
bootstrap/
└── app.php                              # Middleware aliases: role, tenant
config/
└── auth.php                             # User model → Domains\Auth\Models\User
database/
└── migrations/                          # 17 migration files (see below)
resources/views/
├── layouts/app.blade.php                # RTL admin layout with sidebar
├── dashboard.blade.php                  # KPI dashboard
├── auth/                               # login, register, password reset
├── sales/                              # pos, orders/*, sessions/*
├── expenses/                           # index, create, edit, categories
├── inventory/                          # products/*, categories, suppliers, purchases/*
└── invoicing/invoices/                 # index, create, show, print
routes/
└── web.php                             # 75 routes total
public/
├── css/app.css                         # Custom styles (1069 lines)
└── js/app.js                           # Sidebar toggle, alerts, etc.
```

## [SYSTEM_FLOW]

```
User → Login → [CheckTenant Middleware] → Session with tenant_id
                                              │
                    ┌─────────────────────────┼─────────────────────────┐
                    │                         │                         │
                     [Role: admin]           [Role: sales]           [Role: operations]
                     │                         │                         │
         ┌───────────┼───────────┐             │                         │
         │           │           │             │                         │
     Sales      Expenses   Inventory      POS (Create         View Orders
     Orders     CRUD       Products       Orders Only)        (Status Only)
     Reports    Reports    Purchases
     Settings               Suppliers
                     │                         │
               [Role: inventory]         Inventory Only
```

### Core Data Flow:
```
POS Session (Open) → Create Order with Items → Process Payment
                                                     ↓
                                           Update Product Stock (decrement)
                                                     ↓
                                           Generate Invoice (optional)
                                                     ↓
                                           Log Activity (ActivityLog)
```

### Tenant Scoping:
All queries automatically filtered by `tenant_id` via `HasTenantScope` global scope.
All new records auto-assign `tenant_id` from authenticated user.

## Database Schema (17 tables)

| Table | Description | Key FK |
|-------|-------------|--------|
| `tenants` | Restaurant companies | — |
| `users` | Staff accounts | tenant_id → tenants |
| `expense_categories` | Expense classification | tenant_id |
| `expenses` | Expense records | tenant_id, expense_category_id, created_by |
| `product_categories` | Product groups | tenant_id |
| `products` | Menu/stock items | tenant_id, category_id |
| `suppliers` | Vendor records | tenant_id |
| `purchases` | Purchase orders | tenant_id, supplier_id |
| `purchase_items` | Purchase line items | purchase_id, product_id |
| `stock_movements` | Inventory transactions | tenant_id, product_id |
| `order_sessions` | POS daily sessions | tenant_id, user_id |
| `orders` | Sales orders | tenant_id, session_id, user_id |
| `order_items` | Order line items | order_id, product_id |
| `payments` | Payment records | tenant_id, order_id, invoice_id |
| `invoices` | Customer invoices | tenant_id, order_id |
| `invoice_items` | Invoice line items | invoice_id, product_id |
| `activity_logs` | Audit trail | tenant_id, user_id |

## [ORPHANS & PENDING]

| Item | Priority | Status | Notes |
|------|----------|--------|-------|
| PHP 8.3 upgrade | Low | ⏳ Deferred | Laravel 13 requires 8.3; current 8.2 works with L12 |
| Email config (SMTP) | Medium | ⏳ Pending | .env MAIL_MAILER=log (dev mode) |
| POS real-time updates | Low | ⏳ Future | Would need WebSockets/Pusher |
| Thermal printer config | Medium | ⏳ Pending | Print view ready, needs driver config |
| Arabic translations | Low | ⏳ Pending | UI text hardcoded in Arabic via Blade |
| CSV export (reports) | Low | ⏳ Pending | Controller stub exists |
| Tests (PHPUnit) | High | ⏳ Pending | Need Feature tests for all domains |
| Docker/Sail setup | Low | ⏳ Future | For production deployment |
| Backup system | Medium | ⏳ Pending | Automated DB backups |
| Rate limiting / Throttle | Low | ⏳ Future | API protection |
| HTTPS/SSL config | High | ⏳ Pending | For production deployment |

## Seeded Demo Data

| Table | Records |
|-------|---------|
| Tenants | 1 — Moody's Management |
| Users | 4 — admin, sales, operations, inventory |
| Expense Categories | 5 — إيجار, فواتير, رواتب, صيانة, تسويق |
| Product Categories | 4 — منتجات, مستلزمات, مواد خام, خدمات |
| Products | 23 sample products |
| Suppliers | 3 |
| Orders | 5 sample orders (12 items) |

## Login Credentials (Demo)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@althwq.com | password |
| Sales | cashier@althwq.com | password |
| Operations | kitchen@althwq.com | password |
| Inventory | stock@althwq.com | password |
