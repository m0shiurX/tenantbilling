# Tenant Billing & Rent Management System — Implementation Plan

> **Version:** 3.0
> **Date:** February 2026
> **Project:** TenantBilling — House Rent Management Application
> **Stack:** Laravel 12 + Vue 3 + Inertia v2 + Tailwind CSS v4
> **Reference Architecture:** lavloss (POS/Inventory system — same stack)

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Excel Analysis & Data Mapping](#excel-analysis--data-mapping)
3. [Sidebar Navigation Architecture](#sidebar-navigation-architecture)
4. [Domain Model & Entity Relationships](#domain-model--entity-relationships)
5. [Database Schema Design](#database-schema-design)
6. [Configurable Utility Charge System](#configurable-utility-charge-system)
7. [Advance Payment System (Monetary Balance)](#advance-payment-system-monetary-balance)
8. [Module Breakdown](#module-breakdown)
9. [Billing Logic & Automation](#billing-logic--automation)
10. [Financial Accounts Module](#financial-accounts-module)
11. [Reports & Analytics](#reports--analytics)
12. [Tax Calculation Module](#tax-calculation-module)
13. [Permission Structure](#permission-structure)
14. [Dashboard](#dashboard-main--dashboard)
15. [Route & URL Design](#route--url-design)
16. [Frontend Page Structure](#frontend-page-structure)
17. [Implementation Phases](#implementation-phases)
18. [Future Enhancements](#future-enhancements)

---

## Executive Summary

This application replaces the current Excel-based rent collection system with a full-featured web application. It manages **~5 buildings with ~40 units** across different floors.

### Core Workflow

1. **Owner** manages multiple **Buildings** (e.g., 5 buildings)
2. Each Building has **Floors**, each Floor has **Units** (~40 total units)
3. Each Unit is assigned to a **Tenant** via a **Lease**
4. The Lease defines the base rent and configurable **Utility Charge Components** (fixed or variable, included in rent or separate)
5. Every month, a **Rent Bill** is auto-generated with:
   - Base rent + all fixed recurring charge components (auto-populated)
   - Variable charge components (electricity, etc.) entered manually
   - Previous due carried forward
   - **Advance balance** deducted automatically (monetary, not month-based)
6. **Payments** are recorded; advance balance is tracked as a running monetary amount
7. **Expenses** are tracked per building/unit with financial account categorization
8. **Reports** show collection, profitability, arrears, and tax-ready summaries

---

## Excel Analysis & Data Mapping

### Original Excel Columns (Bengali → English)

| Bengali Column | English Translation   | Type     | Behavior                               |
| -------------- | --------------------- | -------- | -------------------------------------- |
| তারিখ            | Date                  | Date     | Collection date (next month)           |
| নাম             | Name                  | String   | Tenant name                            |
| ভাড়া             | Rent                  | Currency | Fixed monthly base rent per unit       |
| পানি             | Water                 | Currency | **Configurable** — fixed charge        |
| ঝাড়ু             | Dusting / Cleaning    | Currency | **Configurable** — fixed charge (৳200) |
| ময়লা            | Garbage Collection    | Currency | **Configurable** — fixed charge (৳100) |
| পিছনে বাকি         | Previous Due          | Currency | Carry-forward from previous months     |
| গ্যাস            | Gas                   | Currency | **Configurable** — fixed charge        |
| কারেন্ট           | Electricity (Current) | Currency | **Variable** — entered monthly         |
| আডভান্স দেয়া       | Advance Deposited     | Currency | Monetary advance balance added         |
| মোট             | Total                 | Currency | Sum of all charges                     |
| আডভান্স কাটা       | Advance Deducted      | Currency | Amount deducted from advance balance   |
| জমা             | Paid / Deposited      | Currency | Amount actually collected              |
| বাকি             | Due / Balance         | Currency | Outstanding balance                    |

### Key Observations

1. **~30+ units** across multiple buildings with rents from ৳2,700 to ৳49,500
2. **Diverse unit types**: residential rooms, apartments, shops, hotels, factories, madrasas
3. **Utility charges vary per tenant**: water (৳500–৳6,500), gas (৳550), dusting (৳200), garbage (৳100)
4. **Some tenants have no utility charges** (included in rent or different commercial arrangement)
5. **Electricity is the only truly variable charge** — different every month
6. **All other charges are predictable/fixed** and can be auto-calculated
7. **Advance is monetary** — tenant pays ৳50,000 advance, monthly bill of ৳30,000 is deducted, ৳20,000 remains
8. **Previous dues carry forward** — some tenants have significant arrears (up to ৳68,838)
9. **Monthly totals**: ৳5,51,502 expected, ৳2,32,627 collected, ৳3,18,832 outstanding
10. **Billing month ≠ Collection month** — "February 2026 rent collected in March"

---

## Sidebar Navigation Architecture

The sidebar uses a **3-rail icon system** matching the existing `SidebarIconRail` + `SidebarMenu` pattern.

### Rail 1: Main (Day-to-day Operations)

```
🏠 MAIN
├── Overview
│   └── Dashboard              → /dashboard (portfolio summary)
├── Properties
│   ├── Units                  → /units (all units, filterable by building)
│   └── Tenants                → /tenants
├── Billing
│   ├── Monthly Bills          → /billing (the main Excel replacement)
│   ├── Generate Bills         → /billing/generate
│   └── Payments               → /payments
└── Expenses
    └── All Expenses           → /expenses
```

### Rail 2: Reports (Inspired by lavloss Reports pattern)

```
📊 REPORTS
├── Overview
│   └── Monthly Summary          → /reports/overview (date-range snapshot: billing, collections, expenses)
├── Rent Reports
│   ├── Monthly Collection     → /reports/collection (THE Excel replica — exportable)
│   ├── Arrears / Overdue      → /reports/arrears (aging: current, 1mo, 2mo, 3+mo)
│   └── Advance Ledger         → /reports/advance-ledger (all advance deposits/deductions)
├── Property Reports
│   ├── Building Summary       → /reports/buildings (per-building revenue vs. expense comparison)
│   ├── Unit Profitability     → /reports/units (ranked net profit per unit)
│   └── Occupancy              → /reports/occupancy (visual grid: occupied/vacant/maintenance)
├── Tenant Reports
│   ├── Tenant Ledger          → /reports/tenant-ledger (all tenants with balances — sortable)
│   └── Tenant Statement       → /reports/tenant-statement/{tenant} (printable PDF per tenant)
└── Expense Reports
    └── Expenses               → /reports/expenses (by category, building, date range — exportable)
```

### Rail 3: Financial (Modeled after lavloss Accounting module)

```
💰 FINANCIAL
├── Accounts (Bank/Cash accounts — same as lavloss BankAccounts)
│   ├── Accounts               → /financial/accounts (+create)
│   └── Account Statement      → /financial/accounts/{account} (transaction ledger)
├── Capital (Owner investments/withdrawals for properties)
│   └── Capital                → /financial/capital (+create)
├── Reports (Financial statements)
│   ├── Dashboard              → /financial/dashboard (4 KPI cards + charts + period filters)
│   ├── Profit & Loss          → /financial/profit-loss (date-range P&L statement)
│   ├── Cash Flow              → /financial/cash-flow (operating/investing/financing)
│   └── Balance Sheet          → /financial/balance-sheet (assets, liabilities, equity)
└── Tax
    ├── Taxable Income         → /financial/tax
    └── Tax Report (PDF)       → /financial/tax/export
```

### Bottom Rail (always visible)

```
❓ Help
⚙️ Settings (existing: users, roles, permissions, company, master data)
```

### Master Data (under Settings)

```
⚙️ SETTINGS
├── System
│   ├── Users                  → /settings/users
│   ├── Roles                  → /settings/roles
│   └── Permissions            → /settings/permissions
├── Master Data
│   ├── Buildings              → /settings/buildings (CRUD)
│   ├── Charge Components      → /settings/charge-components (define available charges)
│   └── Expense Categories     → /settings/expense-categories
└── Company
    └── Company Profile        → /settings/company
```

### Menu Config (fits existing `config.ts` pattern)

```typescript
// New mainSections for the icon rail
mainSections: [
    {
        id: 'main',
        titleKey: 'nav.main',
        icon: 'DashboardIcon',
        route: dashboard,
        activePatterns: ['/dashboard', '/units*', '/tenants*', '/billing*', '/payments*', '/expenses*'],
    },
    {
        id: 'reports',
        titleKey: 'nav.reports',
        icon: 'ChartIcon',
        route: reportsIndex,
        activePatterns: ['/reports*'],
    },
    {
        id: 'financial',
        titleKey: 'nav.financial',
        icon: 'WalletIcon',
        route: financialIncome,
        activePatterns: ['/financial*'],
    },
]
```

---

## Domain Model & Entity Relationships

```
Owner (User)
  └── has many → Buildings
        └── has many → Floors
              └── has many → Units
                    ├── belongs to → Building (through Floor)
                    ├── has one current → Tenant (via active Lease)
                    ├── has many → Leases (historical)
                    └── has many → RentBills

ChargeComponent (master data — globally defined)
  └── e.g., "Water", "Gas", "Electricity", "Dusting", "Garbage"
  └── has fields → name, label_bn, is_fixed, is_variable, default_amount

Lease (junction: Unit ↔ Tenant with date range + financial terms)
  ├── belongs to → Unit
  ├── belongs to → Tenant
  ├── has fields → start_date, end_date, base_rent, advance_balance (৳)
  ├── has many → LeaseCharges (which charge components apply + amounts)
  └── has many → RentBills

LeaseCharge (configurable charges per lease)
  ├── belongs to → Lease
  ├── belongs to → ChargeComponent
  └── has fields → amount, is_included_in_rent, is_active

RentBill (monthly invoice per unit)
  ├── belongs to → Lease → Unit → Building
  ├── has many → BillLineItems (individual charges)
  ├── has many → Payments
  └── has fields → billing_month, previous_due, subtotal, advance_used, total, balance

BillLineItem (individual charge within a bill)
  ├── belongs to → RentBill
  ├── belongs to → ChargeComponent (nullable — for ad-hoc)
  └── has fields → label, amount, is_recurring

Payment (money received)
  ├── belongs to → RentBill
  └── has fields → amount, payment_date, method, reference

Expense (costs incurred)
  ├── belongs to → Building (required)
  ├── belongs to → Unit (optional)
  ├── belongs to → ExpenseCategory
  └── has fields → amount, date, description, is_tax_deductible

ExpenseCategory (master data for financial reporting)
  └── e.g., "Maintenance", "Repair", "Municipal Tax", "Insurance", "Salary"
```

---

## Database Schema Design

### `buildings`

| Column           | Type          | Notes                            |
| ---------------- | ------------- | -------------------------------- |
| id               | bigint PK     |                                  |
| name             | string        | e.g., "Jak Tower", "Building A"  |
| address          | text          | Full address                     |
| city             | string        | nullable                         |
| total_floors     | integer       | Number of floors                 |
| description      | text          | nullable                         |
| acquisition_date | date          | nullable — when acquired/built   |
| acquisition_cost | decimal(15,2) | nullable — cost for tax purposes |
| status           | enum          | active, inactive                 |
| created_by       | foreignId     | User who created                 |
| timestamps       |               |                                  |
| softDeletes      |               |                                  |

### `floors`

| Column       | Type      | Notes                                         |
| ------------ | --------- | --------------------------------------------- |
| id           | bigint PK |                                               |
| building_id  | foreignId | → buildings                                   |
| name         | string    | e.g., "Ground Floor", "1st Floor", "L1", "L2" |
| floor_number | integer   | Sortable order (0 = ground)                   |
| timestamps   |           |                                               |

### `units`

| Column      | Type      | Notes                                   |
| ----------- | --------- | --------------------------------------- |
| id          | bigint PK |                                         |
| floor_id    | foreignId | → floors                                |
| building_id | foreignId | → buildings (denormalized for querying) |
| unit_number | string    | e.g., "A1", "101", "Shop-3"             |
| unit_type   | enum      | residential, commercial, mixed          |
| size_sqft   | decimal   | nullable — area in square feet          |
| description | text      | nullable                                |
| status      | enum      | vacant, occupied, maintenance, reserved |
| timestamps  |           |                                         |
| softDeletes |           |                                         |

### `tenants`

| Column          | Type      | Notes                        |
| --------------- | --------- | ---------------------------- |
| id              | bigint PK |                              |
| name            | string    | Full name                    |
| phone           | string    | nullable                     |
| email           | string    | nullable                     |
| national_id     | string    | nullable — NID number        |
| address         | text      | nullable — permanent address |
| emergency_phone | string    | nullable                     |
| notes           | text      | nullable                     |
| status          | enum      | active, inactive             |
| timestamps      |           |                              |
| softDeletes     |           |                              |

### `charge_components`

> Master data: globally defined charge types (Water, Gas, Electricity, Dusting, Garbage, etc.)

| Column         | Type          | Notes                                           |
| -------------- | ------------- | ----------------------------------------------- |
| id             | bigint PK     |                                                 |
| name           | string        | System name (e.g., "water", "electricity")      |
| label          | string        | Display name English (e.g., "Water")            |
| label_bn       | string        | Display name Bangla (e.g., "পানি")                |
| type           | enum          | **fixed** or **variable**                       |
| default_amount | decimal(12,2) | nullable — default amount for fixed charges     |
| is_system      | boolean       | default false — system charges can't be deleted |
| sort_order     | integer       | Display ordering                                |
| is_active      | boolean       | default true                                    |
| timestamps     |               |                                                 |

**Seed data:**

| name        | label              | label_bn | type     | default_amount |
| ----------- | ------------------ | -------- | -------- | -------------- |
| rent        | Rent               | ভাড়া       | fixed    | null           |
| water       | Water              | পানি       | fixed    | 500            |
| gas         | Gas                | গ্যাস      | fixed    | 550            |
| dusting     | Dusting/Cleaning   | ঝাড়ু       | fixed    | 200            |
| garbage     | Garbage Collection | ময়লা      | fixed    | 100            |
| electricity | Electricity        | কারেন্ট     | variable | null           |

### `leases`

| Column           | Type          | Notes                                      |
| ---------------- | ------------- | ------------------------------------------ |
| id               | bigint PK     |                                            |
| unit_id          | foreignId     | → units                                    |
| tenant_id        | foreignId     | → tenants                                  |
| start_date       | date          | Lease start                                |
| end_date         | date          | nullable — null = ongoing                  |
| base_rent        | decimal(12,2) | Monthly rent amount                        |
| security_deposit | decimal(12,2) | nullable — one-time security deposit       |
| advance_balance  | decimal(12,2) | **Monetary** — current advance balance (৳) |
| status           | enum          | active, expired, terminated                |
| notes            | text          | nullable                                   |
| timestamps       |               |                                            |

### `lease_charges`

> Which charge components apply to this specific lease, and at what amount

| Column              | Type          | Notes                                                                |
| ------------------- | ------------- | -------------------------------------------------------------------- |
| id                  | bigint PK     |                                                                      |
| lease_id            | foreignId     | → leases                                                             |
| charge_component_id | foreignId     | → charge_components                                                  |
| amount              | decimal(12,2) | Overridden amount for this lease (e.g., ৳1,500 water for big tenant) |
| is_included_in_rent | boolean       | default false — if true, not added separately to the bill            |
| is_active           | boolean       | default true                                                         |
| timestamps          |               |                                                                      |

**Example for a unit with ৳11,000 rent:**

| charge_component | amount | is_included_in_rent |
| ---------------- | ------ | ------------------- |
| water            | 500    | false               |
| dusting          | 200    | false               |
| garbage          | 100    | false               |
| electricity      | null   | false               |
| gas              | —      | (not assigned)      |

**Example for বরিশাল হোটেল with ৳49,500 rent:**

| charge_component | amount | is_included_in_rent |
| ---------------- | ------ | ------------------- |
| water            | 6,500  | false               |
| electricity      | null   | false               |

### `rent_bills`

> One record per unit per billing month

| Column           | Type          | Notes                                            |
| ---------------- | ------------- | ------------------------------------------------ |
| id               | bigint PK     |                                                  |
| lease_id         | foreignId     | → leases                                         |
| unit_id          | foreignId     | → units (denormalized)                           |
| tenant_id        | foreignId     | → tenants (denormalized)                         |
| building_id      | foreignId     | → buildings (denormalized)                       |
| billing_month    | date          | First day of billing month (e.g., 2026-02-01)    |
| collection_month | date          | When rent is collected (e.g., 2026-03-01)        |
| base_rent        | decimal(12,2) | Snapshotted from lease                           |
| previous_due     | decimal(12,2) | Carried forward from prior month's balance       |
| subtotal         | decimal(12,2) | base_rent + Σ(line_items) — this month's charges |
| total            | decimal(12,2) | subtotal + previous_due                          |
| advance_used     | decimal(12,2) | Amount deducted from tenant's advance balance    |
| total_paid       | decimal(12,2) | Sum of all cash/bank payments                    |
| balance          | decimal(12,2) | total - total_paid - advance_used                |
| status           | enum          | draft, issued, partial, paid, overdue, void      |
| issued_at        | datetime      | nullable                                         |
| due_date         | date          | nullable                                         |
| notes            | text          | nullable                                         |
| timestamps       |               |                                                  |

### `bill_line_items`

> Individual charge lines within a rent bill

| Column              | Type          | Notes                                     |
| ------------------- | ------------- | ----------------------------------------- |
| id                  | bigint PK     |                                           |
| rent_bill_id        | foreignId     | → rent_bills                              |
| charge_component_id | foreignId     | nullable → charge_components              |
| label               | string        | Display name (snapshotted)                |
| amount              | decimal(12,2) | Charge amount                             |
| is_recurring        | boolean       | Whether auto-populated from lease_charges |
| notes               | string        | nullable                                  |
| timestamps          |               |                                           |

### `payments`

| Column         | Type          | Notes                                              |
| -------------- | ------------- | -------------------------------------------------- |
| id             | bigint PK     |                                                    |
| rent_bill_id   | foreignId     | → rent_bills                                       |
| tenant_id      | foreignId     | → tenants                                          |
| amount         | decimal(12,2) | Amount received                                    |
| payment_date   | date          | Date of payment                                    |
| payment_method | enum          | cash, bank_transfer, mobile_banking, cheque, other |
| reference      | string        | nullable — transaction ref                         |
| received_by    | foreignId     | → users (who collected)                            |
| notes          | text          | nullable                                           |
| timestamps     |               |                                                    |

### `expense_categories`

> Master data for categorizing expenses (used in financial reports)

| Column            | Type      | Notes                               |
| ----------------- | --------- | ----------------------------------- |
| id                | bigint PK |                                     |
| name              | string    | e.g., "Maintenance & Repair"        |
| slug              | string    | unique — e.g., "maintenance-repair" |
| description       | text      | nullable                            |
| is_tax_deductible | boolean   | default true — for tax report       |
| sort_order        | integer   | Display ordering                    |
| timestamps        |           |                                     |

**Seed data:** Maintenance & Repair, Municipal Tax, Insurance, Salary, Legal, Utility, Cleaning, Miscellaneous

### `expenses`

> Costs incurred for buildings/units — always linked to a building

| Column              | Type          | Notes                                      |
| ------------------- | ------------- | ------------------------------------------ |
| id                  | bigint PK     |                                            |
| building_id         | foreignId     | → buildings (**required**)                 |
| unit_id             | foreignId     | nullable → units (unit-specific expense)   |
| expense_category_id | foreignId     | → expense_categories                       |
| description         | string        | What the expense was for                   |
| amount              | decimal(12,2) | Expense amount                             |
| expense_date        | date          |                                            |
| is_recurring        | boolean       | default false                              |
| is_tax_deductible   | boolean       | Inherited from category, can be overridden |
| receipt_path        | string        | nullable — file upload path                |
| recorded_by         | foreignId     | → users                                    |
| notes               | text          | nullable                                   |
| timestamps          |               |                                            |
| softDeletes         |               |                                            |

### `advance_transactions`

> Audit log for advance balance changes (deposits and deductions)

| Column        | Type          | Notes                                     |
| ------------- | ------------- | ----------------------------------------- |
| id            | bigint PK     |                                           |
| lease_id      | foreignId     | → leases                                  |
| rent_bill_id  | foreignId     | nullable → rent_bills (null for deposits) |
| type          | enum          | deposit, deduction, refund, adjustment    |
| amount        | decimal(12,2) | Positive for deposits and deductions      |
| balance_after | decimal(12,2) | Advance balance after this transaction    |
| description   | string        | nullable — reason/notes                   |
| recorded_by   | foreignId     | → users                                   |
| timestamps    |               |                                           |

---

## Configurable Utility Charge System

### Design Principles

1. **Charge Components are Master Data** — defined once in `charge_components`, reusable across all leases
2. **Each charge has a type**: `fixed` (same amount every month) or `variable` (entered manually each billing cycle)
3. **Per-lease customization** via `lease_charges` — override amount, include/exclude, activate/deactivate
4. **"Included in rent" flag** — if `is_included_in_rent = true`, the charge is considered part of the base rent and NOT shown as a separate line item on the bill (useful for commercial tenants where water is "included")
5. **New charge types can be added anytime** without code changes — just add to `charge_components`

### How It Works

```
Step 1: Admin defines Charge Components (Master Data)
        ┌──────────────────────────────────────────┐
        │ Water      │ fixed    │ ৳500  (default)   │
        │ Gas        │ fixed    │ ৳550  (default)   │
        │ Dusting    │ fixed    │ ৳200  (default)   │
        │ Garbage    │ fixed    │ ৳100  (default)   │
        │ Electricity│ variable │ null  (no default) │
        └──────────────────────────────────────────┘

Step 2: When creating a Lease, select which charges apply
        Lease for "সুজন ভাগিনা" (Unit X, ৳11,000/mo):
        ┌──────────────────────────────────────────────┐
        │ ☑ Water       │ ৳500  │ ☐ Included in rent  │
        │ ☐ Gas         │ —     │                      │
        │ ☑ Dusting     │ ৳200  │ ☐ Included in rent  │
        │ ☑ Garbage     │ ৳100  │ ☐ Included in rent  │
        │ ☑ Electricity │ (var) │ ☐ Included in rent  │
        └──────────────────────────────────────────────┘

Step 3: Monthly Bill auto-populates fixed charges
        ┌──────────────────────────────────────┐
        │ Rent         │ ৳11,000 │ (auto)      │
        │ Water        │ ৳500    │ (auto)      │
        │ Dusting      │ ৳200    │ (auto)      │
        │ Garbage      │ ৳100    │ (auto)      │
        │ Electricity  │ ৳___    │ (MANUAL)    │
        │ Previous Due │ ৳2      │ (auto)      │
        │ ──────────────────────────────────── │
        │ Total        │ ৳12,222 │             │
        └──────────────────────────────────────┘
```

### Adding a New Charge Type

If a new utility (e.g., "Internet", "Security") needs to be added:

1. Go to Settings → Master Data → Charge Components
2. Add "Internet" as `fixed`, default ৳500
3. Go to each applicable lease and enable it
4. Next bill generation will include it automatically

---

## Advance Payment System (Monetary Balance)

### Design: Monetary Balance, Not Month Count

The advance system works as a **running monetary balance** per lease (not a month counter).

### Flow

```
1. Tenant pays ৳50,000 advance when signing lease
   → lease.advance_balance = ৳50,000
   → advance_transactions: { type: deposit, amount: 50000, balance_after: 50000 }

2. Month 1 bill is generated
   Known charges: rent(11000) + water(500) + dusting(200) + garbage(100) = ৳11,800
   Electricity not yet known → bill in draft

3. Electricity entered: ৳1,020 → total bill = ৳12,820

4. System auto-deducts from advance:
   → advance_used on bill = ৳12,820
   → lease.advance_balance = 50000 - 12820 = ৳37,180
   → advance_transactions: { type: deduction, amount: 12820, balance_after: 37180 }
   → bill.balance = ৳0 (fully covered by advance)

5. Month 2 bill is ৳13,100
   → advance_used = ৳13,100
   → lease.advance_balance = 37180 - 13100 = ৳24,080

6. Month 3 bill is ৳25,000 (but advance only has ৳24,080)
   → advance_used = ৳24,080 (entire remaining balance)
   → lease.advance_balance = ৳0
   → bill.balance = ৳920 (tenant owes this amount)

7. Tenant can top up advance at any time
   → new deposit transaction increases balance
```

### When to Apply Advance

The system should offer flexibility:

- **Auto-deduct**: Automatically apply advance balance to cover the full bill each month
- **Manual deduct**: User decides how much advance to use per bill
- **Configuration**: Per-lease setting for auto vs. manual advance usage

### Known vs. Unknown Charges

Since fixed charges (rent, water, gas, dusting, garbage) are known in advance:

- The system can show **"estimated months remaining"** = `advance_balance / monthly_fixed_total`
- Variable charges (electricity) are entered later, adjusting the actual deduction

---

## Module Breakdown

### Module 1: Master Data (Settings)

> Foundation configuration — set up before anything else

- **Buildings** CRUD — name, address, floors (inline floor management)
- **Charge Components** CRUD — define all utility charge types (water, gas, dusting, garbage, electricity, custom)
- **Expense Categories** CRUD — define expense types for financial reporting

### Module 2: Unit Management (Main → Properties)

> Units are the primary entity — the thing you rent out

- List all units with building filter dropdown
- Create/edit units (select building → floor → unit details)
- Unit status tracking (vacant/occupied/maintenance)
- Quick view: current tenant, rent amount, advance balance
- Unit detail page: full history (leases, bills, payments, expenses)

### Module 3: Tenant Management (Main → Properties)

- Register tenants with contact details and NID
- Tenant list with search/filter by status, building
- Tenant profile: current lease, billing history, payment history, advance balance
- Track active vs. inactive tenants

### Module 4: Lease Management (within Unit)

> Lease is created from the Unit detail page — "Assign Tenant"

- Assign tenant to unit with lease terms (rent, start date, advance deposit)
- **Configure charge components** for this lease (which apply, amounts, included in rent)
- Track advance balance (monetary)
- Terminate/expire leases; prevent overlapping active leases
- View lease history per unit

### Module 5: Monthly Billing (Main → Billing)

> The heart of the application — replaces the Excel workflow

- **Monthly Bills page** — the main view (replica of the Excel sheet with filters)
- **Bulk bill generation** — generate all bills for a month in one click
- Auto-populate: base rent + all fixed lease_charges
- Auto-calculate: previous due from last month's balance
- **Quick entry mode**: enter electricity (and other variable charges) for all units in a table
- Bill status workflow: Draft → Issued → Partial → Paid → Overdue
- Individual bill view/edit
- **Advance auto-deduction** per bill

### Module 6: Payment Recording (Main → Billing)

- Record payments against specific rent bills
- Partial payments supported
- Multiple payments per bill
- Payment receipt (printable)
- Record advance deposits (separate from bill payments)

### Module 7: Expense Tracking (Main → Expenses)

> Always connected to a building, optionally to a unit

- Record expenses with building dropdown (required) + unit dropdown (optional)
- Select expense category from master data
- Flag as tax-deductible (auto-copied from category, overridable)
- Receipt upload
- Monthly/yearly expense summaries

---

## Billing Logic & Automation

### Monthly Bill Generation Flow

```
1. User navigates to Billing → Generate Bills
2. Selects billing month (e.g., February 2026) and collection month (e.g., March 2026)
3. Optionally filters by building
4. System finds all active leases (or filtered subset)
5. For each active lease:
   a. Check if bill already exists for this month (skip if so)
   b. Create RentBill record
   c. Add BillLineItem for base_rent
   d. For each active lease_charge where is_included_in_rent = false:
      - If charge_component.type = "fixed": add line item with configured amount
      - If charge_component.type = "variable": add line item with amount = 0 (pending)
   e. Calculate previous_due from last month's bill balance
   f. Calculate subtotal = base_rent + Σ(fixed line items)
   g. total = subtotal + previous_due (variable charges will update later)
   h. Set status = "draft"
6. User sees all draft bills in a table view
7. User enters variable charges (electricity) per unit — table has inline edit
8. Totals recalculate automatically
9. User can apply advance balance (auto or manual)
10. User marks bills as "issued"
```

### Total Calculation Formula

```
subtotal      = base_rent + Σ(bill_line_items.amount)   // this month's new charges
total         = subtotal + previous_due                  // everything owed
advance_used  = min(advance_balance, total) or manual    // advance applied
total_paid    = Σ(payments.amount)                       // cash/bank received
balance       = total - advance_used - total_paid        // still owed
```

### Previous Due Carry-Forward

```php
$lastBill = RentBill::where('lease_id', $lease->id)
    ->where('billing_month', '<', $currentMonth)
    ->latest('billing_month')
    ->first();

$previousDue = $lastBill ? $lastBill->balance : 0;
```

### Advance Balance Operations

```php
// === Tenant deposits advance ===
$lease->advance_balance += $depositAmount;
AdvanceTransaction::create([
    'lease_id'      => $lease->id,
    'type'          => 'deposit',
    'amount'        => $depositAmount,
    'balance_after' => $lease->advance_balance,
]);

// === Auto-deduct advance on bill ===
$advanceToUse = min($lease->advance_balance, $bill->total - $bill->total_paid);
$bill->advance_used = $advanceToUse;
$lease->advance_balance -= $advanceToUse;
$bill->balance = $bill->total - $bill->total_paid - $bill->advance_used;

AdvanceTransaction::create([
    'lease_id'      => $lease->id,
    'rent_bill_id'  => $bill->id,
    'type'          => 'deduction',
    'amount'        => $advanceToUse,
    'balance_after' => $lease->advance_balance,
]);

// === Estimated months remaining (for display) ===
$monthlyFixed = $lease->base_rent + $lease->leaseCharges()
    ->whereHas('chargeComponent', fn ($q) => $q->where('type', 'fixed'))
    ->where('is_included_in_rent', false)
    ->where('is_active', true)
    ->sum('amount');

$estimatedMonths = $monthlyFixed > 0
    ? floor($lease->advance_balance / $monthlyFixed)
    : null;
```

---

## Financial Accounts Module

> Modeled after the lavloss project's Accounting module — with `BankAccount`, `AccountTransaction`, `CapitalTransaction` patterns adapted for property management.

### Architecture Reference (from lavloss)

The lavloss project uses these key patterns we'll adopt:

- **BankAccountService** — manages accounts, records transactions, updates balances
- **FinancialReportService** — generates P&L, Balance Sheet, Cash Flow with caching
- **AccountTransaction model** — polymorphic ledger (every money movement recorded)
- **CapitalTransaction model** — owner investments/withdrawals
- **ExportService** — PDF generation via Blade views + DomPDF
- **Dashboard with period filters** — This Month / Last Month / This Quarter / YTD

### New Database Tables for Financial Module

#### `bank_accounts`

| Column          | Type          | Notes                                      |
| --------------- | ------------- | ------------------------------------------ |
| id              | bigint PK     |                                            |
| name            | string        | e.g., "Main Cash", "Islami Bank", "bKash"  |
| account_type    | enum          | cash, bank, mobile_banking                 |
| account_number  | string        | nullable — bank account number             |
| bank_name       | string        | nullable — bank name                       |
| branch          | string        | nullable                                   |
| account_holder  | string        | nullable                                   |
| opening_balance | decimal(15,2) | Initial balance at setup                   |
| current_balance | decimal(15,2) | Running balance (updated via transactions) |
| opening_date    | date          | nullable                                   |
| is_default      | boolean       | default false — one default per type       |
| is_active       | boolean       | default true                               |
| notes           | text          | nullable                                   |
| created_by      | foreignId     | → users                                    |
| updated_by      | foreignId     | nullable → users                           |
| timestamps      |               |                                            |
| softDeletes     |               |                                            |

#### `account_transactions`

> Polymorphic ledger — every money movement goes through here (same pattern as lavloss)

| Column                 | Type          | Notes                                             |
| ---------------------- | ------------- | ------------------------------------------------- |
| id                     | bigint PK     |                                                   |
| bank_account_id        | foreignId     | → bank_accounts                                   |
| transaction_type       | enum          | See AccountTransactionType below                  |
| reference_type         | string        | nullable — morphable type (Payment, Expense, etc) |
| reference_id           | bigint        | nullable — morphable ID                           |
| amount                 | decimal(15,2) | Always positive                                   |
| balance_after          | decimal(15,2) | Account balance after transaction                 |
| description            | string        | Human-readable description                        |
| transaction_date       | date          |                                                   |
| transaction_ref        | string        | nullable — external reference number              |
| transfer_to_account_id | foreignId     | nullable — for inter-account transfers            |
| created_by             | foreignId     | → users                                           |
| timestamps             |               |                                                   |
| softDeletes            |               |                                                   |

**AccountTransactionType enum:**

```php
enum AccountTransactionType: string
{
    case OpeningBalance = 'opening_balance';
    case RentReceived = 'rent_received';        // Payment from tenant
    case AdvanceReceived = 'advance_received';   // Advance deposit
    case ExpensePayment = 'expense_payment';     // Building expense paid
    case CapitalInvestment = 'capital_investment';
    case CapitalWithdrawal = 'capital_withdrawal';
    case Transfer = 'transfer';                  // Inter-account
    case Adjustment = 'adjustment';              // Manual correction
    case Refund = 'refund';                     // Advance refund to tenant
}
```

#### `capital_transactions`

> Owner investment/withdrawal tracking (same as lavloss CapitalTransaction)

| Column           | Type          | Notes                             |
| ---------------- | ------------- | --------------------------------- |
| id               | bigint PK     |                                   |
| transaction_type | enum          | investment, withdrawal            |
| bank_account_id  | foreignId     | → bank_accounts                   |
| amount           | decimal(15,2) |                                   |
| transaction_date | date          |                                   |
| owner_name       | string        | Name of investor (property owner) |
| description      | string        | nullable                          |
| notes            | text          | nullable                          |
| created_by       | foreignId     | → users                           |
| timestamps       |               |                                   |
| softDeletes      |               |                                   |

### Update `payments` Table

> Add bank_account_id to link rent payments to financial accounts

| Column              | Type      | Notes (additions in **bold**)                |
| ------------------- | --------- | -------------------------------------------- |
| ...                 | ...       | (all existing fields from v2.0)              |
| **bank_account_id** | foreignId | nullable → bank_accounts (where money lands) |

### Update `expenses` Table

> Add bank_account_id to link expenses to financial accounts

| Column              | Type      | Notes (additions in **bold**)                     |
| ------------------- | --------- | ------------------------------------------------- |
| ...                 | ...       | (all existing fields from v2.0)                   |
| **bank_account_id** | foreignId | nullable → bank_accounts (where money comes from) |

### Financial Dashboard (Financial → Dashboard)

> Modeled after lavloss `Accounting/Reports/Dashboard.vue` — the central financial overview

**Period filter pills:** This Month | Last Month | This Quarter | YTD

**4 Key Metric Cards (KPI):**

| Card                     | Source                                          | Color                     | With Change %          |
| ------------------------ | ----------------------------------------------- | ------------------------- | ---------------------- |
| **Rent Revenue**         | Sum of all payments received in period          | Emerald gradient          | ↑↓ vs. previous period |
| **Total Expenses**       | Sum of all expenses in period                   | Red gradient              | ↑↓ vs. previous period |
| **Net Operating Income** | Revenue - Expenses                              | Blue/Orange (profit/loss) | Net margin % badge     |
| **Cash Balance**         | Sum of all active bank_accounts.current_balance | Purple gradient           | —                      |

**Charts Row (2 charts side by side):**

1. **6-Month Revenue Trend** — Line chart: rent collected + expenses + net income per month (last 6 months)
2. **Revenue Breakdown** — Donut/bar chart: rent vs. utility income, or per-building breakdown

**Key Metrics Row (4 summary cards):**

| Metric                    | Source                                             |
| ------------------------- | -------------------------------------------------- |
| **Collection Rate**       | (total_paid / total_billed) × 100 for period       |
| **Occupancy Rate**        | (occupied_units / total_units) × 100               |
| **Total Receivables**     | Sum of all outstanding bill balances (tenant dues) |
| **Total Advance Balance** | Sum of all active leases' advance_balance          |

**Quick Links:** P&L, Balance Sheet, Cash Flow, Bank Accounts, Capital

### Profit & Loss Statement (Financial → Profit & Loss)

> Adapted from lavloss `FinancialReportService::getProfitLossStatement()`

```
╔══════════════════════════════════════════════════════════╗
║              PROFIT & LOSS STATEMENT                     ║
║         For the period 2026-02-01 to 2026-02-28         ║
╠══════════════════════════════════════════════════════════╣
║                                                          ║
║  REVENUE                                                 ║
║  ──────────────────────────────────────────────────       ║
║  Rental Income (base_rent from bills).... ৳3,96,200      ║
║  Utility Income (water, gas, dusting).... ৳   22,200     ║
║  Other Income (late fees, etc.).......... ৳        0     ║
║  ──────────────────────────────────────────────────       ║
║  Total Revenue .......................... ৳4,18,400      ║
║                                                          ║
║  OPERATING EXPENSES                                      ║
║  ──────────────────────────────────────────────────       ║
║  Maintenance & Repair ................... ৳   15,000     ║
║  Municipal Tax .......................... ৳    8,000     ║
║  Insurance .............................. ৳    5,000     ║
║  Salary ................................. ৳   12,000     ║
║  Utility ................................ ৳    3,000     ║
║  ──────────────────────────────────────────────────       ║
║  Total Expenses ......................... ৳   43,000     ║
║                                                          ║
║  ══════════════════════════════════════════════════       ║
║  NET OPERATING INCOME ................... ৳3,75,400      ║
║  Gross Margin: 89.7%   Net Margin: 89.7%                ║
║  ══════════════════════════════════════════════════       ║
╚══════════════════════════════════════════════════════════╝
```

**Features:**
- Date range filter (defaults to fiscal year start → today)
- Fiscal year display (July–June)
- Revenue split: Rental Income vs. Utility Income
- Expenses grouped by `expense_category`
- Print button (window.print() with print-optimized CSS)
- Per-building breakdown (expandable detail)

### Balance Sheet (Financial → Balance Sheet)

> Adapted from lavloss `FinancialReportService::getBalanceSheet()`

```
ASSETS
  Current Assets
    Cash & Bank Balances ......... ৳X,XX,XXX  (sum of bank_accounts.current_balance)
    Tenant Receivables ........... ৳X,XX,XXX  (sum of outstanding rent_bills.balance)
    Advance Deposits Held ........ ৳X,XX,XXX  (sum of all lease.advance_balance — this is liability, not asset)
  ─────────────────────────────
  Total Assets ................... ৳X,XX,XXX

LIABILITIES
  Current Liabilities
    Tenant Advance Deposits ...... ৳X,XX,XXX  (sum of leases.advance_balance — owed back to tenants)
    Tax Payable .................. ৳X,XX,XXX  (estimated tax liability)
  ─────────────────────────────
  Total Liabilities .............. ৳X,XX,XXX

EQUITY
  Owner's Capital ................ ৳X,XX,XXX  (capital investments - withdrawals)
  Retained Earnings .............. ৳X,XX,XXX  (cumulative P&L to date)
  Opening Balance Equity ......... ৳X,XX,XXX  (plug to balance A = L + E)
  ─────────────────────────────
  Total Equity ................... ৳X,XX,XXX

  ═════════════════════════════
  Total Liabilities & Equity ..... ৳X,XX,XXX  (should equal Total Assets)
  Is Balanced: ✅
```

### Cash Flow Statement (Financial → Cash Flow)

> Adapted from lavloss `FinancialReportService::getCashFlowStatement()`

```
OPENING CASH BALANCE .......................... ৳X,XX,XXX

OPERATING ACTIVITIES
  + Cash from tenants (rent payments) .......... ৳X,XX,XXX
  + Cash from advance deposits ................. ৳X,XX,XXX
  - Cash for building expenses ................. ৳X,XX,XXX
  - Cash for advance refunds ................... ৳X,XX,XXX
  ─────────────────────────────
  Net Cash from Operations ..................... ৳X,XX,XXX

FINANCING ACTIVITIES
  + Capital investments ........................ ৳X,XX,XXX
  - Capital withdrawals ........................ ৳X,XX,XXX
  ─────────────────────────────
  Net Cash from Financing ...................... ৳X,XX,XXX

NET CASH CHANGE ................................ ৳X,XX,XXX

CLOSING CASH BALANCE .......................... ৳X,XX,XXX
```

### Bank Account Management (Financial → Accounts)

> Same as lavloss BankAccounts — CRUD + transaction ledger

**Account Index Page:**
- List all accounts grouped by type (Cash / Bank / Mobile Banking)
- Summary bar: Total Cash | Total Bank | Total Mobile | Grand Total
- Each account card: name, type, current balance, default badge
- "+Create" button per type

**Account Statement Page (Show):**
- Date range filter
- Transaction ledger table: Date | Description | Type | Debit | Credit | Balance
- Running balance after each transaction
- Opening balance shown at top
- Source of each transaction linked (e.g., "Rent #234 — সুজন ভাগিনা")

### Capital Management (Financial → Capital)

> Same as lavloss Capital — investment/withdrawal tracking

**Capital Index:**
- List all capital transactions: date, type (Investment/Withdrawal), amount, owner, bank account, description
- Summary: Total Invested | Total Withdrawn | Net Capital
- "+Investment" / "+Withdrawal" buttons

### Service Layer Architecture (from lavloss patterns)

```
app/Services/
├── BankAccountService.php       # Account CRUD, record transactions, update balances
│   ├── getActiveAccounts()
│   ├── create(BankAccountData)
│   ├── recordTransaction(AccountTransactionData)
│   ├── recordRentPayment(accountId, payment)
│   ├── recordExpensePayment(accountId, expense)
│   ├── recordCapitalTransaction(accountId, capital)
│   ├── getAccountStatement(accountId, startDate, endDate)
│   └── transferBetweenAccounts(fromId, toId, amount)
│
├── CapitalService.php           # Owner investment/withdrawal operations
│   ├── createInvestment(data)
│   └── createWithdrawal(data)
│
├── BillingService.php           # Bill generation engine (existing plan)
│
├── Reports/
│   ├── FinancialReportService.php   # P&L, Balance Sheet, Cash Flow, Dashboard
│   │   ├── getFiscalYearDates()
│   │   ├── getProfitLossStatement(startDate, endDate)
│   │   ├── getBalanceSheet(asOfDate)
│   │   ├── getCashFlowStatement(startDate, endDate)
│   │   ├── getEnhancedDashboardData(period)     # 4 KPIs + charts + trends
│   │   ├── getMonthlyTrendChart()               # Last 6 months line chart
│   │   ├── getCollectionRateByPeriod(period)
│   │   └── calculateRetainedEarnings(asOfDate)
│   │
│   ├── RentReportService.php        # Rent-specific reports
│   │   ├── getMonthlyCollectionReport(month, building?)
│   │   ├── getArrearsReport(asOfDate)
│   │   ├── getAdvanceLedger(startDate, endDate)
│   │   └── getTenantStatement(tenant, startDate, endDate)
│   │
│   ├── PropertyReportService.php    # Building & unit reports
│   │   ├── getBuildingSummary(startDate, endDate)
│   │   ├── getUnitProfitability(startDate, endDate)
│   │   └── getOccupancyReport()
│   │
│   └── ExportService.php           # PDF generation (DomPDF via Blade views)
│       ├── generatePDF(view, data, filename, orientation)
│       ├── exportCollectionReport(data)
│       ├── exportTenantStatement(data)
│       ├── exportProfitLoss(data)
│       └── exportTaxReport(data)
│
└── DashboardService.php         # Main dashboard metrics
    ├── getMetrics(period)           # 6 KPI cards with change %
    ├── getCharts(period)            # Revenue vs. Expense, Collection Rate, etc.
    └── getRecentActivity()          # Recent payments, bills, expenses
```

### Enum Files (from lavloss patterns)

```
app/Enums/
├── AccountType.php              # cash, bank, mobile_banking
├── AccountTransactionType.php   # opening_balance, rent_received, expense_payment, etc.
├── CapitalTransactionType.php   # investment, withdrawal
├── UnitType.php                 # residential, commercial, mixed
├── UnitStatus.php               # vacant, occupied, maintenance, reserved
├── LeaseStatus.php              # active, expired, terminated
├── BillStatus.php               # draft, issued, partial, paid, overdue, void
├── ChargeType.php               # fixed, variable
├── AdvanceType.php              # deposit, deduction, refund, adjustment
├── PaymentMethod.php            # cash, bank_transfer, mobile_banking, cheque, other
├── TenantStatus.php             # active, inactive
└── BuildingStatus.php           # active, inactive
```

---

## Reports & Analytics

> Modeled after lavloss Reports module — with date filters, print support, PDF export, quick action cards, and summary snapshots. Each report page follows the same pattern: Header + DateFilter + Summary Cards + Data Table + Footer Totals + Print/Export buttons.

### Reports Overview Page (Reports → Overview)

> Similar to lavloss `Reports/Overview.vue` — a daily/date-range snapshot of all key metrics

**Quick Action Cards (grid at top):**

| Card               | Links To                | Variant |
| ------------------ | ----------------------- | ------- |
| Monthly Collection | /reports/collection     | default |
| Arrears            | /reports/arrears        | danger  |
| Buildings          | /reports/buildings      | info    |
| Unit Profit        | /reports/units          | success |
| Occupancy          | /reports/occupancy      | info    |
| Tenant Ledger      | /reports/tenant-ledger  | default |
| Expenses           | /reports/expenses       | warning |
| Advance Ledger     | /reports/advance-ledger | success |

**Date Range Filter** (start_date + end_date + Apply button)

**Snapshot Grid (2×2 boxes with summary numbers):**

| Box           | Content                                                       |
| ------------- | ------------------------------------------------------------- |
| **Billing**   | Total billed, Total collected, Collection rate %, Outstanding |
| **Payments**  | Number of payments, Total received, Advance deposits received |
| **Expenses**  | Number of expenses, Total amount, By top category             |
| **Occupancy** | Occupied units, Vacant units, Occupancy rate %                |

**Print button** — prints the entire snapshot as a one-page business summary

### 1. Monthly Collection Report (THE Excel Replacement — Reports → Collection)

> This is the **primary operational report** — the exact replica of the user's Bengali Excel sheet with enhanced filtering and export.

**Filters:**
- **Building** dropdown (required or "All Buildings")
- **Billing Month** dropdown (e.g., "February 2026")
- **Payment Status** dropdown (All / Paid / Partial / Unpaid / Overdue)

**Table Columns (matching original Excel):**

| #   | Column (English) | Column (Bangla) | Source                                  |
| --- | ---------------- | --------------- | --------------------------------------- |
| 1   | Date             | তারিখ             | payment_date or collection_month        |
| 2   | Unit             | ইনিট             | unit.unit_number                        |
| 3   | Tenant Name      | নাম              | tenant.name                             |
| 4   | Rent             | ভাড়া              | bill_line_items (rent component)        |
| 5   | Water            | পানি              | bill_line_items (water component)       |
| 6   | Gas              | গ্যাস             | bill_line_items (gas component)         |
| 7   | Dusting          | ঝাড়ু              | bill_line_items (dusting component)     |
| 8   | Garbage          | ময়লা             | bill_line_items (garbage component)     |
| 9   | Previous Due     | পিছনে বাকি          | rent_bills.previous_due                 |
| 10  | Electricity      | কারেন্ট            | bill_line_items (electricity component) |
| 11  | Total            | মোট              | rent_bills.total                        |
| 12  | Advance Deducted | আডভান্স কাটা        | rent_bills.advance_used                 |
| 13  | Paid             | জমা              | rent_bills.total_paid                   |
| 14  | Due              | বাকি              | rent_bills.balance                      |

**Footer Summary Row:**
- Column totals for all monetary columns
- Collection rate badge: `(paid / total) × 100%`

**Export Options:**
- **Print** (window.print with print-optimized CSS — same as lavloss pattern)
- **PDF Export** (via ExportService → DomPDF, Blade template, A4 landscape)
- **Excel Export** (via Maatwebsite/Laravel-Excel)

**Title on export:** "২০২৬ সালের ফেব্রুয়ারি মাসের ভাড়া মার্চ মাসে উঠানো হলো" (Bangla)

### 2. Arrears / Overdue Report (Reports → Arrears)

> Tenants with outstanding balances, organized by aging buckets

**Aging Buckets (columns):**

| Bucket    | Definition               | Color  |
| --------- | ------------------------ | ------ |
| Current   | Due this month           | Green  |
| 1 Month   | Due last month           | Yellow |
| 2 Months  | Due 2 months ago         | Orange |
| 3+ Months | Due 3 or more months ago | Red    |

**Table:**
| Tenant | Unit | Building | Current | 1 Month | 2 Months | 3+ Months | Total Due | Phone |
| ------ | ---- | -------- | ------- | ------- | -------- | --------- | --------- | ----- |

**Features:**
- Sorted by highest total due (descending)
- Contact info for follow-up calls
- Click tenant name → Tenant Statement
- Filter by building
- Export PDF / Excel

### 3. Advance Ledger Report (Reports → Advance Ledger)

> All advance money movements across all tenants — deposit, deduction, refund, adjustment

**Table:**
| Date | Tenant | Unit | Type | Amount | Balance After | Bill # | Recorded By |
| ---- | ------ | ---- | ---- | ------ | ------------- | ------ | ----------- |

**Filters:** Date range, Tenant, Type (deposit/deduction/refund)
**Summary:** Total Deposits | Total Deductions | Total Refunds | Current Total Advance Held

### 4. Building Summary Report (Reports → Buildings)

> Per-building comparison — similar to lavloss Overview but for properties

**Table/Cards:**

| Building | Units | Occupied | Vacant | Revenue (period) | Expenses (period) | Net Income | Occupancy % |
| -------- | ----- | -------- | ------ | ---------------- | ----------------- | ---------- | ----------- |

**Chart:** Horizontal bar chart — Revenue vs. Expenses per building (side by side)

### 5. Unit Profitability Report (Reports → Units)

> Per-unit net profit analysis

**Table:**
| Unit | Building | Tenant | Rent/mo | Utility Income | Total Revenue | Total Expenses | Net Profit | Profit Margin |
| ---- | -------- | ------ | ------- | -------------- | ------------- | -------------- | ---------- | ------------- |

**Features:**
- Ranked by net profit (highest first)
- Color-coded: green (profitable), red (loss-making)
- Filter by building, date range
- Identifies units with negative profitability

### 6. Occupancy Report (Reports → Occupancy)

> Visual grid / heatmap of all units across buildings

**Visual Layout:**
```
Building A          Building B          Building C
┌─────┬─────┐      ┌─────┬─────┐      ┌─────┬─────┐
│ 🟢  │ 🟢  │ L3   │ 🔴  │ 🟢  │ L3   │ 🟡  │ 🟢  │ L2
├─────┼─────┤      ├─────┼─────┤      ├─────┼─────┤
│ 🟢  │ 🔴  │ L2   │ 🟢  │ 🟢  │ L2   │ 🟢  │ 🟢  │ L1
├─────┼─────┤      ├─────┼─────┤      ├─────┼─────┤
│ 🟢  │ 🟢  │ L1   │ 🟢  │ 🟡  │ L1   │ 🟢  │ 🔴  │ G
└─────┴─────┘      └─────┴─────┘      └─────┴─────┘
🟢 Occupied   🔴 Vacant   🟡 Maintenance
```

**Summary:** Total Occupied / Total Units = Occupancy Rate %

### 7. Tenant Ledger Report (Reports → Tenant Ledger)

> All tenants with their current balance — like lavloss Customer Ledger

**Table:**
| Tenant | Unit | Building | Total Billed | Total Paid | Advance Balance | Outstanding Due | Status |
| ------ | ---- | -------- | ------------ | ---------- | --------------- | --------------- | ------ |

**Features:**
- Sortable by any column
- Filter: Building, Status (Active/Inactive), Has Due (yes/no)
- Click tenant → Tenant Statement
- Export PDF / Excel

### 8. Tenant Statement (Reports → Tenant Statement)

> Printable PDF statement for a specific tenant — like lavloss Customer Statement

**Header:** Tenant name, NID, phone, unit, building
**Table:**
| Date | Description | Debit (Charge) | Credit (Payment/Advance) | Balance |
| ---- | ----------- | -------------- | ------------------------ | ------- |

**Includes:**
- All rent bills as debit entries
- All payments as credit entries
- All advance deposits/deductions
- Running balance after each transaction
- Opening & closing balance
- Print-optimized layout (A4 portrait)
- Company header + footer

### 9. Expense Report (Reports → Expenses)

> All expenses with filters — like lavloss Expenses report

**Filters:** Date range, Building, Category, Unit (optional)

**Table:**
| Date | Building | Unit | Category | Description | Amount | Tax Deductible | Recorded By |
| ---- | -------- | ---- | -------- | ----------- | ------ | -------------- | ----------- |

**Summary:**
- Total by category (pie chart)
- Total by building (bar chart)
- Grand total

**Export:** PDF / Excel

### Export Architecture (from lavloss ExportService pattern)

```
app/Services/Reports/ExportService.php
├── generatePDF(view, data, filename, orientation)
├── streamPDF(view, data, orientation)         // Preview in browser
├── exportCollectionReportPDF(reportData)
├── exportTenantStatementPDF(reportData)
├── exportArrearsReportPDF(reportData)
├── exportProfitLossPDF(reportData)
├── exportTaxReportPDF(reportData)
└── exportExpenseReportPDF(reportData)

resources/views/reports/pdf/
├── collection.blade.php        // Monthly collection (A4 landscape)
├── tenant-statement.blade.php  // Per-tenant statement (A4 portrait)
├── arrears.blade.php           // Overdue aging report
├── profit-loss.blade.php       // P&L statement
├── balance-sheet.blade.php     // Balance sheet
├── tax-report.blade.php        // Tax filing summary
└── expense.blade.php           // Expense report
```

### Report Controllers (from lavloss pattern — dedicated controller per report type)

```
app/Http/Controllers/Reports/
├── OverviewController.php            // Reports overview snapshot
├── CollectionReportController.php    // Monthly collection (Excel replica)
├── ArrearsReportController.php       // Overdue aging
├── AdvanceLedgerController.php       // Advance deposits/deductions
├── BuildingReportController.php      // Building summary
├── UnitReportController.php          // Unit profitability
├── OccupancyReportController.php     // Visual occupancy
├── TenantLedgerController.php        // All tenants balance
├── TenantStatementController.php     // Per-tenant statement
├── ExpenseReportController.php       // Expense breakdown
└── ExportController.php              // PDF/Excel export endpoints
```

### Chart Components (from lavloss Dashboard pattern)

```
resources/js/Components/Dashboard/
├── TrendChart.vue              // Line chart (6-month revenue trend)
├── ColumnChart.vue             // Bar chart (revenue vs. expenses per building)
├── DonutChart.vue              // Pie chart (expense breakdown by category)
├── SplineChart.vue             // Smooth line (cash flow: collections vs. payments)
├── CollectionRateGauge.vue     // Circular progress (collection rate %)
└── OccupancyGrid.vue           // Visual unit grid (green/red/yellow)
```

---

## Tax Calculation Module

### Bangladesh Rental Income Tax Rules

| Item                          | Source                  | Calculation                          |
| ----------------------------- | ----------------------- | ------------------------------------ |
| **Gross Rental Income**       | Sum of base_rent billed | Σ(rent_bills.base_rent) for FY       |
| **Service/Utility Income**    | Utility charge items    | Σ(line_items where component ≠ rent) |
| **Total Gross Income**        | Above combined          | Gross Rent + Utility Income          |
| **Allowable Deductions**      | Tax-deductible expenses | Σ(expenses where is_tax_deductible)  |
| **Standard Deduction**        | 30% of gross            | 0.30 × Total Gross Income            |
| **Deduction Used**            | Higher of the two       | max(actual expenses, 30% standard)   |
| **Net Taxable Rental Income** | After deductions        | Total Gross - Deduction Used         |

### Tax Report Features

- Filter by fiscal year (July–June)
- Per-building breakdown
- Combined portfolio summary
- Side-by-side: actual expenses vs. 30% standard deduction
- Recommendation: which deduction method is more beneficial
- Export as PDF for tax filing

---

## Permission Structure

> Extends existing Spatie Permission system — Gate-checked in every controller (from lavloss pattern)

| Permission            | Group      | Description                           |
| --------------------- | ---------- | ------------------------------------- |
| building_access       | Properties | View buildings                        |
| building_create       | Properties | Create new building                   |
| building_update       | Properties | Edit building details                 |
| building_delete       | Properties | Delete building                       |
| unit_access           | Properties | View units                            |
| unit_create           | Properties | Create units                          |
| unit_update           | Properties | Edit units                            |
| unit_delete           | Properties | Delete units                          |
| tenant_access         | Tenants    | View tenants                          |
| tenant_create         | Tenants    | Create tenants                        |
| tenant_update         | Tenants    | Edit tenants                          |
| tenant_delete         | Tenants    | Delete tenants                        |
| lease_access          | Leases     | View leases                           |
| lease_create          | Leases     | Create/assign leases                  |
| lease_update          | Leases     | Edit lease terms                      |
| lease_terminate       | Leases     | Terminate leases                      |
| rent_bill_access      | Billing    | View rent bills                       |
| rent_bill_create      | Billing    | Generate rent bills                   |
| rent_bill_update      | Billing    | Edit bills, enter variable charges    |
| rent_bill_delete      | Billing    | Delete/void bills                     |
| rent_bill_issue       | Billing    | Issue bills to tenants                |
| payment_access        | Payments   | View payments                         |
| payment_create        | Payments   | Record payments                       |
| payment_update        | Payments   | Edit payments                         |
| payment_delete        | Payments   | Delete/void payments                  |
| advance_manage        | Payments   | Manage advance deposits/deductions    |
| expense_access        | Expenses   | View expenses                         |
| expense_create        | Expenses   | Record expenses                       |
| expense_update        | Expenses   | Edit expenses                         |
| expense_delete        | Expenses   | Delete expenses                       |
| bank_account_access   | Financial  | View bank accounts                    |
| bank_account_create   | Financial  | Create bank accounts                  |
| bank_account_update   | Financial  | Edit bank accounts                    |
| bank_account_delete   | Financial  | Delete bank accounts                  |
| capital_access        | Financial  | View capital transactions             |
| capital_create        | Financial  | Record capital investment/withdrawal  |
| capital_update        | Financial  | Edit capital transactions             |
| financial_dashboard   | Financial  | View financial dashboard              |
| financial_statements  | Financial  | View P&L, Cash Flow, Balance Sheet    |
| report_access         | Reports    | View reports overview                 |
| report_collection     | Reports    | View monthly collection reports       |
| report_arrears        | Reports    | View arrears/overdue reports          |
| report_advance_ledger | Reports    | View advance ledger report            |
| report_buildings      | Reports    | View building summary reports         |
| report_units          | Reports    | View unit profitability reports       |
| report_occupancy      | Reports    | View occupancy reports                |
| report_tenant_ledger  | Reports    | View tenant ledger/statements         |
| report_expenses       | Reports    | View expense reports                  |
| report_financial      | Reports    | View financial/profit reports         |
| report_tax            | Reports    | View tax reports                      |
| report_export         | Reports    | Export reports to PDF/Excel           |
| master_data_manage    | Settings   | Manage charge components & categories |

---

## Dashboard (Main → Dashboard)

> Inspired by lavloss HomeController + Accounting Dashboard — the first page users see after login

### Period Filter Pills

| Filter             | Description                         |
| ------------------ | ----------------------------------- |
| Today              | Today's collections and activity    |
| 7 Days             | Last 7 days                         |
| This Month         | Current billing month (default)     |
| Last Month         | Previous billing month              |
| This Quarter       | Last 3 months                       |
| YTD (Year to Date) | Since start of fiscal year (July 1) |

### KPI Metric Cards (Grid of 6)

> Each card shows: Value, Label, Change % vs. previous period, Trend arrow up/down

| #   | Card                | Value                                 | Change vs. Previous       |
| --- | ------------------- | ------------------------------------- | ------------------------- |
| 1   | **Rent Billed**     | Σ(rent_bills.total) for period        | % change vs. prior period |
| 2   | **Rent Collected**  | Σ(payments.amount) for period         | % change vs. prior period |
| 3   | **Collection Rate** | (collected / billed) × 100%           | Difference in % points    |
| 4   | **Total Expenses**  | Σ(expenses.amount) for period         | % change vs. prior period |
| 5   | **Net Income**      | Collected − Expenses                  | % change vs. prior period |
| 6   | **Occupancy Rate**  | (occupied units / total units) × 100% | Change in units           |

### Charts Section (3 charts)

**Chart 1: Revenue vs. Expenses (ColumnChart)**
- X-axis: Last 6 months (labels)
- Y-axis: Amount in ৳
- Two series: Revenue (green bars) and Expenses (red bars)
- Source: `DashboardService::getRevenueTrend(6)`

**Chart 2: Collection Rate Trend (SplineChart / TrendChart)**
- X-axis: Last 6 months
- Y-axis: Collection rate %
- Single smooth line showing monthly collection efficiency
- Source: `DashboardService::getCollectionRateTrend(6)`

**Chart 3: Expense Breakdown (DonutChart)**
- Categories as slices (Maintenance, Utilities, Cleaning, Taxes, etc.)
- Percentage labels
- Source: `DashboardService::getExpenseBreakdown()`

### Summary Sections Below Charts

**Occupancy Overview (small grid):**
| Building   | Occupied  | Vacant | Rate    |
| ---------- | --------- | ------ | ------- |
| Building A | 8/10      | 2      | 80%     |
| ...        | ...       | ...    | ...     |
| **Total**  | **36/40** | **4**  | **90%** |

**Recent Activity Feed (last 10 items):**
- Payment received: Tenant X paid ৳5,000 for Unit A3 — 5m ago
- Bill generated: February 2026 bills generated (38 bills) — 2h ago
- Expense recorded: Plumbing repair ৳3,500 (Building B) — 1d ago
- Tenant assigned: New lease for Unit C2 — 2d ago

### Quick Links
- Generate Monthly Bills → /billing/generate
- Record Payment → /payments
- Monthly Collection Report → /reports/collection
- Financial Dashboard → /financial/dashboard

### Dashboard Service Architecture

```php
app/Services/DashboardService.php
├── getMetrics(period)                    // 6 KPI values with change %
├── getRevenueTrend(months)               // ColumnChart data
├── getCollectionRateTrend(months)        // SplineChart data
├── getExpenseBreakdown(period)           // DonutChart data
├── getOccupancySummary()                 // Per-building occupancy
├── getRecentActivity(limit)             // Recent transactions/events
└── calculateChange(current, previous)   // % change helper
```

### Caching Strategy (from lavloss pattern)

```php
// Cache dashboard data for 15 minutes — busted on new payment/bill
Cache::remember("dashboard_{$period}", 900, function () use ($period) {
    return [
        'metrics' => $this->getMetrics($period),
        'charts' => [
            'revenue' => $this->getRevenueTrend(6),
            'collection' => $this->getCollectionRateTrend(6),
            'expenses' => $this->getExpenseBreakdown($period),
        ],
    ];
});

// Cache busting: Event listeners on PaymentCreated, BillGenerated, ExpenseCreated
// to clear relevant cache keys
```

---

## Route & URL Design

```
# ── Dashboard ────────────────────────────────────────
GET    /dashboard                           → dashboard.index (portfolio overview)

# ── Main: Properties ─────────────────────────────────
GET    /units                               → units.index (all units, building filter)
GET    /units/create                        → units.create
POST   /units                               → units.store
GET    /units/{unit}                        → units.show (detail + lease + history)
GET    /units/{unit}/edit                   → units.edit
PUT    /units/{unit}                        → units.update
DELETE /units/{unit}                        → units.destroy

# ── Main: Tenants ────────────────────────────────────
GET    /tenants                             → tenants.index
GET    /tenants/create                      → tenants.create
POST   /tenants                             → tenants.store
GET    /tenants/{tenant}                    → tenants.show
GET    /tenants/{tenant}/edit               → tenants.edit
PUT    /tenants/{tenant}                    → tenants.update
DELETE /tenants/{tenant}                    → tenants.destroy

# ── Main: Leases (nested under unit) ─────────────────
POST   /units/{unit}/leases                 → units.leases.store
GET    /leases/{lease}                      → leases.show
GET    /leases/{lease}/edit                 → leases.edit
PUT    /leases/{lease}                      → leases.update
POST   /leases/{lease}/terminate            → leases.terminate

# ── Main: Billing ────────────────────────────────────
GET    /billing                             → billing.index (monthly overview)
GET    /billing/generate                    → billing.generate (wizard)
POST   /billing/generate                    → billing.store (execute generation)
GET    /billing/{month}                     → billing.month (all bills for month)
GET    /rent-bills/{rentBill}               → rent-bills.show
GET    /rent-bills/{rentBill}/edit          → rent-bills.edit
PUT    /rent-bills/{rentBill}               → rent-bills.update
POST   /rent-bills/{rentBill}/issue         → rent-bills.issue
DELETE /rent-bills/{rentBill}               → rent-bills.destroy

# ── Main: Payments ───────────────────────────────────
GET    /payments                            → payments.index
POST   /rent-bills/{rentBill}/payments      → rent-bills.payments.store
PUT    /payments/{payment}                  → payments.update
DELETE /payments/{payment}                  → payments.destroy

# ── Main: Advance ────────────────────────────────────
POST   /leases/{lease}/advance/deposit      → leases.advance.deposit
POST   /leases/{lease}/advance/deduct       → leases.advance.deduct

# ── Main: Expenses ───────────────────────────────────
GET    /expenses                            → expenses.index
GET    /expenses/create                     → expenses.create
POST   /expenses                            → expenses.store
GET    /expenses/{expense}/edit             → expenses.edit
PUT    /expenses/{expense}                  → expenses.update
DELETE /expenses/{expense}                  → expenses.destroy

# ── Reports ──────────────────────────────────────────
GET    /reports                             → reports.overview (snapshot dashboard)
GET    /reports/collection                  → reports.collection (Excel replica)
GET    /reports/arrears                     → reports.arrears (aging buckets)
GET    /reports/advance-ledger              → reports.advance-ledger
GET    /reports/buildings                   → reports.buildings (building summary)
GET    /reports/units                       → reports.units (unit profitability)
GET    /reports/occupancy                   → reports.occupancy (visual grid)
GET    /reports/tenant-ledger               → reports.tenant-ledger (all tenants)
GET    /reports/tenant-statement/{tenant}   → reports.tenant-statement (per-tenant)
GET    /reports/expenses                    → reports.expenses (expense breakdown)

# ── Report Exports ───────────────────────────────────
GET    /reports/export/collection           → reports.export.collection (PDF)
GET    /reports/export/arrears              → reports.export.arrears (PDF)
GET    /reports/export/tenant-statement/{tenant} → reports.export.tenant-statement (PDF)
GET    /reports/export/expenses             → reports.export.expenses (PDF)
GET    /reports/export/collection/excel     → reports.export.collection-excel (Excel)
GET    /reports/export/arrears/excel        → reports.export.arrears-excel (Excel)
GET    /reports/export/expenses/excel       → reports.export.expenses-excel (Excel)

# ── Financial: Accounts ──────────────────────────────
GET    /financial/accounts                  → financial.accounts.index
GET    /financial/accounts/create           → financial.accounts.create
POST   /financial/accounts                  → financial.accounts.store
GET    /financial/accounts/{account}        → financial.accounts.show (statement)
GET    /financial/accounts/{account}/edit   → financial.accounts.edit
PUT    /financial/accounts/{account}        → financial.accounts.update
GET    /financial/accounts/{account}/statement → financial.accounts.statement

# ── Financial: Capital ───────────────────────────────
GET    /financial/capital                   → financial.capital.index
POST   /financial/capital                   → financial.capital.store
GET    /financial/capital/{transaction}/edit → financial.capital.edit
PUT    /financial/capital/{transaction}     → financial.capital.update

# ── Financial: Reports ───────────────────────────────
GET    /financial/dashboard                 → financial.dashboard (financial KPIs)
GET    /financial/profit-loss               → financial.profit-loss
GET    /financial/cash-flow                 → financial.cash-flow
GET    /financial/balance-sheet             → financial.balance-sheet

# ── Financial: Tax ───────────────────────────────────
GET    /financial/tax                       → financial.tax
GET    /financial/tax/export                → financial.tax-export (PDF)

# ── Financial: Exports ───────────────────────────────
GET    /financial/export/profit-loss        → financial.export.profit-loss (PDF)
GET    /financial/export/cash-flow          → financial.export.cash-flow (PDF)
GET    /financial/export/balance-sheet      → financial.export.balance-sheet (PDF)
GET    /financial/export/tax                → financial.export.tax (PDF)

# ── Settings: Master Data ────────────────────────────
GET    /settings/buildings                  → settings.buildings.index
POST   /settings/buildings                  → settings.buildings.store
GET    /settings/buildings/create           → settings.buildings.create
GET    /settings/buildings/{building}/edit  → settings.buildings.edit
PUT    /settings/buildings/{building}       → settings.buildings.update
DELETE /settings/buildings/{building}       → settings.buildings.destroy

# Floors (inline within building)
POST   /settings/buildings/{building}/floors           → settings.floors.store
PUT    /settings/floors/{floor}                        → settings.floors.update
DELETE /settings/floors/{floor}                        → settings.floors.destroy

GET    /settings/charge-components          → settings.charge-components.index
POST   /settings/charge-components          → settings.charge-components.store
PUT    /settings/charge-components/{cc}     → settings.charge-components.update
DELETE /settings/charge-components/{cc}     → settings.charge-components.destroy

GET    /settings/expense-categories          → settings.expense-categories.index
POST   /settings/expense-categories          → settings.expense-categories.store
PUT    /settings/expense-categories/{ec}     → settings.expense-categories.update
DELETE /settings/expense-categories/{ec}     → settings.expense-categories.destroy
```

---

## Frontend Page Structure

```
resources/js/Pages/
├── Dashboard.vue                  # Portfolio overview (6 KPI cards, 3 charts, activity feed)
│
├── Units/
│   ├── Index.vue                  # All units, building dropdown filter
│   ├── Create.vue                 # New unit (select building → floor)
│   ├── Show.vue                   # Unit detail: tenant, lease, bills, history
│   └── Edit.vue                   # Edit unit
│
├── Tenants/
│   ├── Index.vue                  # Tenant list with search/filter
│   ├── Create.vue                 # New tenant form
│   ├── Show.vue                   # Tenant profile: lease, payments, advance, dues
│   └── Edit.vue                   # Edit tenant
│
├── Leases/
│   ├── Create.vue                 # Assign tenant to unit + configure charges
│   ├── Show.vue                   # Lease details + charge config + advance log
│   └── Edit.vue                   # Edit lease terms + charges
│
├── Billing/
│   ├── Index.vue                  # Monthly billing overview (THE Excel replacement)
│   ├── Generate.vue               # Bulk bill generation wizard
│   ├── Month.vue                  # All bills for a month (table with inline edit)
│   ├── Show.vue                   # Individual bill detail + payments
│   ├── Edit.vue                   # Edit bill (enter variable charges)
│   └── Partials/
│       ├── BillTable.vue          # Reusable bill data table
│       ├── VariableChargeInput.vue # Quick entry for electricity etc.
│       └── PaymentModal.vue       # Record payment inline
│
├── Payments/
│   └── Index.vue                  # Payment history with filters
│
├── Expenses/
│   ├── Index.vue                  # Expense list (filterable by building/category)
│   ├── Create.vue                 # New expense (building + optional unit + bank account)
│   └── Edit.vue                   # Edit expense
│
├── Reports/
│   ├── Overview.vue               # Report hub (quick action cards + snapshot grid)
│   ├── Collection.vue             # Monthly collection (full Excel replica)
│   ├── Arrears.vue                # Overdue/aging report with color buckets
│   ├── AdvanceLedger.vue          # Advance deposit/deduction ledger
│   ├── Buildings.vue              # Building comparison summary
│   ├── Units.vue                  # Unit profitability ranking
│   ├── Occupancy.vue              # Visual occupancy grid (colored blocks)
│   ├── TenantLedger.vue           # All tenants with balance summary
│   ├── TenantStatement.vue        # Printable per-tenant statement
│   ├── Expenses.vue               # Expense breakdown (table + category chart)
│   └── Partials/
│       ├── DateFilter.vue         # Reusable date range filter component
│       ├── PrintHeader.vue        # Company header for print layouts
│       ├── PrintReportLayout.vue  # Print-optimized wrapper (@media print)
│       ├── QuickActionCard.vue    # Clickable card linking to a report
│       └── SummaryFooter.vue      # Totals row for report tables
│
├── Financial/
│   ├── Accounts/
│   │   ├── Index.vue              # Bank account list with balances
│   │   ├── Create.vue             # New bank account form
│   │   ├── Show.vue               # Account statement (filtered transactions)
│   │   └── Edit.vue               # Edit account
│   ├── Capital/
│   │   └── Index.vue              # Capital transactions (investment/withdrawal)
│   ├── Dashboard.vue              # Financial KPIs (Revenue, Expenses, Net Profit, Cash)
│   ├── ProfitLoss.vue             # P&L statement (table format, period filter)
│   ├── CashFlow.vue               # Cash flow: collections vs. payments over time
│   ├── BalanceSheet.vue           # Assets vs. Liabilities snapshot
│   ├── Tax.vue                    # Tax calculation + standard deduction comparison
│   └── TaxExport.vue              # PDF preview before export
│
├── Settings/
│   ├── Buildings/
│   │   ├── Index.vue              # Building list (with inline floor management)
│   │   ├── Create.vue             # New building form
│   │   └── Edit.vue               # Edit building + manage floors
│   ├── ChargeComponents/
│   │   └── Index.vue              # Charge component list (inline CRUD)
│   └── ExpenseCategories/
│       └── Index.vue              # Expense category list (inline CRUD)
│
└── Components/                    # Shared components
    ├── Dashboard/
    │   ├── MetricCard.vue         # KPI card (value, label, change %, trend arrow)
    │   ├── TrendChart.vue         # Line chart (ApexCharts wrapper)
    │   ├── ColumnChart.vue        # Bar chart (revenue vs. expenses)
    │   ├── DonutChart.vue         # Pie/donut chart (expense breakdown)
    │   ├── SplineChart.vue        # Smooth line (cash flow trend)
    │   ├── CollectionRateGauge.vue # Circular progress (collection rate)
    │   ├── OccupancyGrid.vue      # Visual unit grid (green/red/yellow)
    │   ├── RecentActivity.vue     # Activity feed (last N events)
    │   └── QuickLinks.vue         # Common action shortcut buttons
    ├── Table/
    │   ├── DataTable.vue          # Reusable sortable table with pagination
    │   ├── PrintableTable.vue     # Table wrapped in print layout
    │   └── ExportButtons.vue      # PDF + Excel export button group
    └── UI/
        ├── PeriodFilter.vue       # Period filter pills (Today/7d/Month/Quarter/YTD)
        ├── BuildingFilter.vue     # Building dropdown filter component
        ├── StatusBadge.vue        # Color-coded status badge
        ├── MoneyDisplay.vue       # Formatted ৳ amount display
        └── SkeletonLoader.vue     # Pulsing skeleton for deferred props
```

---

## Implementation Phases

### Phase 1: Foundation & Master Data (Week 1–2)

> Set up the foundation: sidebar, buildings, floors, units, charge components, enums

- [ ] Create enum files: BuildingStatus, UnitStatus, UnitType, FloorType, LeaseStatus, BillStatus, PaymentMethod, PaymentStatus, AdvanceTransactionType, ExpenseCategory, AccountType, AccountTransactionType, CapitalTransactionType, TaxPaymentType
- [ ] Create migrations: buildings, floors, units, charge_components, expense_categories
- [ ] Create Models with relationships, casts, scopes, and factories
- [ ] Create Seeders (buildings, floors, units from Excel data + charge components + expense categories)
- [ ] Sidebar navigation: implement 4-rail menu config (Main, Reports, Financial, Settings)
- [ ] Settings → Buildings CRUD (with inline floor management)
- [ ] Settings → Charge Components CRUD (inline)
- [ ] Settings → Expense Categories CRUD (inline)
- [ ] Units CRUD (Main → Properties, with building dropdown filter)
- [ ] Add all permissions to RoleAndPermissionSeeder (properties, tenants, billing, financial, reports groups)
- [ ] Shared UI components: StatusBadge, MoneyDisplay, BuildingFilter, PeriodFilter
- [ ] Tests for all CRUD operations + enum methods

### Phase 2: Tenants & Leases (Week 3)

> Tenant management and lease assignment with configurable charges

- [ ] Create migrations: tenants, leases, lease_charges, advance_transactions
- [ ] Create Models with relationships, factories, and seeders
- [ ] Tenant CRUD (controller, form requests, Vue pages)
- [ ] Lease management (assign tenant to unit from unit detail page)
- [ ] Lease charge configuration UI (select components, set amounts, include-in-rent toggle)
- [ ] Advance deposit recording
- [ ] Advance transaction log with running balance
- [ ] Advance deduction and refund workflows
- [ ] Tests for tenant, lease, advance operations

### Phase 3: Billing Core (Week 4–5)

> Monthly bill generation and payment recording — the heart of the app

- [ ] Create migrations: rent_bills, bill_line_items, payments
- [ ] Create Models with relationships, factories, status enums
- [ ] Create BillingService class (bill generation engine)
- [ ] Monthly bulk bill generation with auto-population of fixed charges
- [ ] Previous due carry-forward logic
- [ ] Billing month overview page (the Excel replacement table)
- [ ] Variable charge entry (electricity) — inline table editing
- [ ] Bill status workflow (draft → issued → partial → paid → overdue)
- [ ] Payment recording against bills (PaymentModal)
- [ ] Advance auto-deduction on bill generation
- [ ] Payment receipt (printable A4)
- [ ] Tests for billing logic, advance deduction, payment recording, status transitions

### Phase 4: Expenses & Financial Accounts (Week 6–7)

> Track costs, manage bank accounts, record capital transactions

- [ ] Create migrations: bank_accounts, account_transactions, capital_transactions
- [ ] Update expenses table with bank_account_id, update payments table with bank_account_id
- [ ] Create Models: BankAccount, AccountTransaction, CapitalTransaction
- [ ] Create BankAccountService (CRUD, balance updates, transaction recording)
- [ ] Create CapitalService (investment/withdrawal recording)
- [ ] Expense CRUD (with building dropdown, optional unit, category selector, bank account selector)
- [ ] Receipt upload support (if needed)
- [ ] Financial → Accounts CRUD (bank account list, create, edit)
- [ ] Financial → Account Statement page (filtered transaction list per account)
- [ ] Financial → Capital transactions page (investment/withdrawal)
- [ ] Auto-record AccountTransaction on: payment received, expense created, capital invested/withdrawn
- [ ] Tests for expense tracking, bank account operations, balance accuracy

### Phase 5: Dashboard (Week 8)

> Rich portfolio dashboard with KPIs, charts, and activity feed

- [ ] Create DashboardService (getMetrics, getRevenueTrend, getCollectionRateTrend, getExpenseBreakdown, getOccupancySummary, getRecentActivity)
- [ ] Dashboard.vue page with 6 KPI MetricCard components
- [ ] Chart components: ColumnChart, SplineChart, DonutChart (ApexCharts wrappers)
- [ ] Period filter integration (Today / 7d / This Month / Last Month / Quarter / YTD)
- [ ] Occupancy overview summary grid
- [ ] Recent activity feed
- [ ] Quick links section
- [ ] Cache layer for dashboard data (15-min TTL, event-based cache busting)
- [ ] Tests for DashboardService metric calculations

### Phase 6: Reports — Core Reports (Week 9–10)

> The 9 business reports with filters, print support, and export

- [ ] Report service layer: RentReportService, PropertyReportService, ExportService
- [ ] Reports Overview page (quick action cards + snapshot grid)
- [ ] **Monthly Collection Report** — full Excel replica with all 14 columns, summary footer, Bangla headers, building/month/status filters
- [ ] Arrears Report — aging buckets (Current / 1mo / 2mo / 3+mo), sorted by highest due
- [ ] Advance Ledger Report — all advance movements with running balance
- [ ] Building Summary Report — per-building comparison with revenue vs. expenses bar chart
- [ ] Unit Profitability Report — ranked by net profit, color-coded
- [ ] Occupancy Report — visual grid with green/red/yellow blocks per unit
- [ ] Tenant Ledger Report — all tenants with balance summary, sortable
- [ ] Tenant Statement — per-tenant running balance, printable (A4 portrait)
- [ ] Expense Report — filterable table with category pie chart + building bar chart
- [ ] Shared report components: DateFilter, PrintHeader, PrintReportLayout, QuickActionCard, SummaryFooter
- [ ] Print support (@media print CSS) for all reports
- [ ] Tests for all report service calculations

### Phase 7: Reports — Export & Financial Statements (Week 11)

> PDF/Excel exports plus P&L, Cash Flow, Balance Sheet

- [ ] ExportService (generatePDF, streamPDF via DomPDF)
- [ ] Blade PDF templates: collection, tenant-statement, arrears, expenses, profit-loss, balance-sheet, cash-flow, tax
- [ ] Excel export integration (Maatwebsite/Laravel-Excel) for Collection, Arrears, Expenses reports
- [ ] ExportController with all export endpoints
- [ ] Create FinancialReportService (getProfitLossStatement, getCashFlowStatement, getBalanceSheet)
- [ ] Financial Dashboard page (4 KPI cards: Revenue, Expenses, Net Profit, Cash Balance + charts + quick links)
- [ ] Profit & Loss Statement page (income vs. deductions, per-building breakdown)
- [ ] Cash Flow Statement page (inflows vs. outflows over time)
- [ ] Balance Sheet page (assets: bank balances + receivables; liabilities: advance deposits held)
- [ ] Tests for export generation, financial statement calculations

### Phase 8: Tax Module (Week 12)

> Personal tax filing support with Bangladeshi tax rules

- [ ] Financial → Taxable Income calculation per building
- [ ] Standard deduction (30%) vs. actual expense comparison
- [ ] Fiscal year configuration and filtering (July–June)
- [ ] Combined portfolio tax summary
- [ ] Tax report PDF export (Blade template)
- [ ] Recommendation engine: which deduction method is more beneficial
- [ ] Tests for tax calculations

### Phase 9: Polish & Production (Week 13–14)

> Production readiness, i18n, and UX polish

- [ ] Bangla language support (i18n for all labels, menus, bill items, report headers)
- [ ] Print-friendly bill and receipt layouts (A4 paper)
- [ ] Bulk variable charge entry (quick form for all units in a month)
- [ ] Data import tool (migrate existing Excel data into the app)
- [ ] Mobile-responsive layouts for all pages (desktop table → mobile card pattern from lavloss)
- [ ] Performance optimization: eager loading, query optimization, report caching
- [ ] SkeletonLoader for deferred props on all report/dashboard pages
- [ ] Comprehensive feature & unit tests across all modules
- [ ] Run Pint for code formatting, Larastan for static analysis
- [ ] SMS/notification for due payments (optional)

---

## Future Enhancements

1. **Meter Reading Module** — Track electricity meter readings per unit, auto-calculate consumption × rate
2. **Tenant Portal** — Self-service portal where tenants can view bills, make payments online
3. **Maintenance Request System** — Tenants submit repair requests, tracked to resolution
4. **Multi-owner Support** — Manage properties for multiple owners with separate financial views
5. **Automated Reminders** — SMS/WhatsApp reminders for upcoming/overdue payments
6. **Cheque Management** — Track post-dated cheques and their clearing status
7. **Receipt Scanning (OCR)** — Scan utility bills to auto-populate variable charges
8. **Bank Reconciliation** — Match bank deposits to recorded payments (from lavloss AccountTransaction pattern)
9. **Vacancy Loss Tracking** — Calculate revenue lost from vacant units per month
10. **Audit Trail** — Full activity log on all financial transactions (using spatie/laravel-activitylog)
11. **API for Mobile App** — REST API for a future mobile companion app
12. **Customizable Dashboard Widgets** — Drag-and-drop dashboard with user-selected widgets
13. **Loan Tracking** — Property loans/mortgages with amortization schedules (from lavloss Loan model)
14. **Multi-currency Support** — For properties with foreign tenants
15. **Automated Bill Notification** — Email/SMS bill to tenant on generation
16. **Comparison Analytics** — Year-over-year and month-over-month revenue/expense comparison charts
17. **Tenant Rating System** — Payment timeliness scoring for tenant evaluation

---

## Notes

- All monetary values use `decimal(12,2)` for precision
- The system supports **Bangla (বাংলা)** labels throughout via existing i18n setup (`lang/en/`, extensible to `lang/bn/`)
- Currency: **৳** (Bangladeshi Taka / BDT)
- Fiscal year: **July 1 – June 30** (Bangladesh standard)
- Billing month and collection month are tracked separately (as per current Excel practice)
- The PDF bill/receipt should match local conventions and be printable on **A4 paper**
- Advance is tracked as **monetary balance** (৳), not month count
- Charge components are **master data** — configurable without code changes
- Expenses are **always linked to a building**, optionally to a specific unit
- The sidebar uses the existing 3-column pattern: **Icon Rail → Detail Menu → Content**
