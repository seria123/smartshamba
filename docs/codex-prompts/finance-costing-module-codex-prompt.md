# Codex Prompt: Finance / Costing Module

You are implementing the next branch of a Laravel 13 modular monolith mixed-farm management platform.

## Branch and base safety

Create and work on this branch:

```powershell
git checkout assets-maintenance-module
git pull origin assets-maintenance-module
git checkout -b finance-costing-module
git branch
```

Before making changes, inspect the codebase and confirm the previous modules exist and are usable:

- Foundation/module structure.
- Core/Admin module.
- Users/Permissions module.
- Workers/Labour module.
- Tasks/Work Orders module.
- Inventory/Inputs module.
- Crops module.
- Livestock module.
- Irrigation module.
- Assets/Maintenance module.

If the expected branch base or module structure is missing, stop and report the mismatch before editing.

## Important product boundary

This module is **Finance / Costing**, not full accounting.

Build a management costing foundation for farm owners and managers to understand operational cost, cost allocation, and cost summaries.

Do **not** build:

- Double-entry accounting.
- General ledger.
- Chart of accounts.
- Trial balance.
- Balance sheet.
- Profit and loss statement.
- Bank reconciliation.
- Tax/VAT filing.
- Payroll engine.
- Payslips.
- Procurement approvals.
- Supplier payment workflows.
- Invoicing.
- Customer sales.
- QuickBooks replacement logic.
- Automatic audited accounting postings.

This branch should make operational costs visible and reportable, while leaving full accounting, payroll, procurement, and sales for later modules.

## Module goal

Add a Finance / Costing admin module under:

```text
/admin/finance
```

The module should allow authorised users to:

1. Define cost categories.
2. Define cost centres.
3. Record actual farm costs manually.
4. Allocate costs to farm operations such as crop cycles, livestock, irrigation, assets, tasks, inventory-related work, or general farm overhead.
5. View cost summaries by farm, category, month, source module, and operational target.
6. Maintain clean audit-friendly management records without creating accounting ledger entries.

## Permissions

Add first-party permissions, following the existing Users/Permissions module conventions:

- `finance.view`
- `finance.manage`
- `finance.reports`

Suggested access rules:

- `/admin/finance` dashboard: `finance.view`
- Cost categories: `finance.manage`
- Cost centres: `finance.manage`
- Cost entries create/edit/delete: `finance.manage`
- Cost reports: `finance.reports` or `finance.view` if the current permission system works better that way.

Do not use Spatie.

Follow the existing permission seeding and middleware patterns.

## Navigation / module registration

Register Finance / Costing in the existing module registry / admin navigation pattern if one exists.

Suggested display values:

- Module name: `Finance / Costing`
- Slug/key: `finance-costing`
- Admin path: `/admin/finance`
- Description: `Management costing, cost allocation, and farm cost summaries.`

## Suggested module structure

Follow the existing modular monolith conventions already used by previous modules.

Use the current project naming style after inspecting the codebase. Suggested structure, adapt if the project uses a slightly different convention:

```text
app/Modules/Finance/
  Controllers/
  Models/
  Requests/
  Services/
  Policies/        # only if existing modules use policies
  routes.php       # only if existing modules use per-module routes
resources/views/admin/finance/
database/migrations/
database/seeders/
tests/Feature/Finance/
```

Do not introduce a new architectural style.

## Data model

Use the existing naming conventions for table names, timestamps, soft deletes, IDs, factories, and model namespaces.

### 1. Cost categories

Create `finance_cost_categories`.

Purpose: classify costs consistently.

Suggested columns:

- `id`
- `organisation_id` nullable if the project allows global seed categories; otherwise required.
- `farm_id` nullable, for farm-specific categories when needed.
- `name`
- `code` nullable.
- `description` nullable.
- `cost_nature` enum/string:
  - `variable`
  - `fixed`
  - `capital`
  - `overhead`
  - `other`
- `default_source_module` nullable string:
  - `manual`
  - `labour`
  - `inventory`
  - `crop`
  - `livestock`
  - `irrigation`
  - `asset`
  - `maintenance`
  - `task`
  - `other`
- `is_active` boolean default true.
- `sort_order` integer default 0.
- timestamps.
- soft deletes if existing modules commonly use them.

Constraints:

- Within the same organisation/farm scope, category names should not duplicate active records.
- Keep codes optional because many farms will not have formal codes.

Seed sensible starter categories, guarded to local/testing/demo conventions if that is the pattern:

- Seeds & Planting Material
- Fertiliser & Soil Amendments
- Crop Protection Chemicals
- Labour
- Machinery & Equipment
- Irrigation & Water
- Fuel & Energy
- Animal Feed
- Veterinary & Medicines
- Breeding
- Maintenance & Repairs
- Transport
- Packaging
- General Farm Overheads
- Other

### 2. Cost centres

Create `finance_cost_centres`.

Purpose: allow farms to group costs into management buckets without forcing accounting departments.

Suggested columns:

- `id`
- `organisation_id`
- `farm_id` nullable.
- `name`
- `code` nullable.
- `centre_type` enum/string:
  - `farm`
  - `field`
  - `crop`
  - `livestock`
  - `irrigation`
  - `asset`
  - `labour`
  - `admin`
  - `project`
  - `other`
- `description` nullable.
- `is_active` boolean default true.
- timestamps.
- soft deletes if existing modules commonly use them.

Cost centres are management grouping labels. They should not create accounting accounts.

### 3. Cost entries

Create `finance_cost_entries`.

Purpose: the main record of a cost incurred.

Suggested columns:

- `id`
- `organisation_id`
- `farm_id` nullable.
- `cost_category_id`
- `cost_centre_id` nullable.
- `entry_no` nullable unique per organisation if the project has numbering patterns.
- `entry_date` date.
- `title`
- `description` nullable.
- `source_module` string default `manual`:
  - `manual`
  - `labour`
  - `task`
  - `inventory`
  - `crop`
  - `livestock`
  - `irrigation`
  - `asset`
  - `maintenance`
  - `other`
- `reference_type` nullable string.
- `reference_id` nullable unsigned bigint.
- `reference_label` nullable string.
- `quantity` nullable decimal.
- `unit` nullable string.
- `unit_cost` nullable decimal.
- `amount` decimal, required, positive.
- `currency` string default `KES`.
- `status` string default `draft`:
  - `draft`
  - `confirmed`
  - `void`
- `payment_state` nullable string:
  - `not_tracked`
  - `unpaid`
  - `part_paid`
  - `paid`
- `notes` nullable text.
- `created_by` nullable user id.
- `confirmed_by` nullable user id.
- `confirmed_at` nullable timestamp.
- `voided_by` nullable user id.
- `voided_at` nullable timestamp.
- `void_reason` nullable text.
- timestamps.
- soft deletes if existing modules commonly use them.

Rules:

- `amount` must be greater than zero.
- If `quantity` and `unit_cost` are both provided, compute or validate `amount = quantity * unit_cost`. Use a reasonable decimal tolerance if needed.
- Draft entries can be edited.
- Confirmed entries should not be freely edited except for limited notes/admin correction if existing modules allow it.
- Void should preserve the record but exclude it from normal totals.
- Do not create accounting postings when an entry is confirmed.
- Do not create inventory movements, payroll records, invoices, supplier payments, or bank transactions.

### 4. Cost allocations

Create `finance_cost_allocations`.

Purpose: assign one cost entry to one or more operational targets. This is the foundation for cost per crop cycle, herd/group, asset, irrigation, etc.

Suggested columns:

- `id`
- `cost_entry_id`
- `organisation_id`
- `farm_id` nullable.
- `allocation_type` string:
  - `general_farm`
  - `field`
  - `crop_cycle`
  - `crop_activity`
  - `livestock_animal`
  - `livestock_group`
  - `livestock_event`
  - `irrigation_event`
  - `asset`
  - `maintenance_record`
  - `work_order`
  - `task`
  - `inventory_movement`
  - `worker`
  - `team`
  - `other`
- `allocatable_type` nullable string.
- `allocatable_id` nullable unsigned bigint.
- `allocation_label` nullable string.
- `allocation_percent` nullable decimal.
- `amount` decimal, required, positive.
- `notes` nullable text.
- timestamps.

Rules:

- The sum of allocation amounts for a cost entry must equal the entry amount before confirmation.
- For a simple entry, create one 100% allocation by default.
- If using allocation percentages, percentages should total 100%.
- Allocations should support future automated costing, but this branch should remain manual/management-oriented.
- Validate farm/organisation scope for linked records where practical.
- If the selected linked model/table does not exist in the current branch, do not invent it. Use `other` with label text instead.

## Linking approach

Prefer simple, stable implementation over clever over-engineering.

Recommended approach:

- Store `source_module`, `reference_type`, `reference_id`, and `reference_label` on cost entries.
- Store `allocation_type`, `allocatable_type`, `allocatable_id`, and `allocation_label` on allocation rows.
- Use optional model resolution services/helpers for known modules.
- Avoid hard foreign-key coupling to every operational table unless the existing project already uses that style safely.

This keeps Finance flexible without making every previous module depend on Finance.

## Services

Add services/helpers only where useful. Suggested:

### `CostEntryService`

Responsibilities:

- Create cost entry with default allocation.
- Update draft cost entry.
- Confirm cost entry after allocation validation.
- Void cost entry with reason.
- Recalculate/validate allocation totals.
- Apply consistent scope checks.

### `CostReportService`

Responsibilities:

- Cost dashboard totals.
- Monthly totals.
- Cost by category.
- Cost by farm.
- Cost by source module.
- Cost by allocation type.
- Cost by crop cycle where crop cycle allocations exist.
- Cost by livestock group/animal where allocations exist.
- Cost by asset/maintenance where allocations exist.

Keep report queries simple and readable. Avoid premature optimization.

## Controllers and screens

Build Blade/admin screens consistent with existing modules.

### Finance dashboard

Route:

```text
GET /admin/finance
```

Show:

- Total confirmed costs for selected period.
- Draft costs count/value.
- Voided costs count/value, separated from normal totals.
- Costs by category.
- Costs by source module.
- Recent cost entries.
- Links to categories, cost centres, entries, and reports.

Filters:

- Organisation/farm scope according to existing access patterns.
- Date range.
- Farm if the current user can access more than one farm.

### Cost categories

Routes under:

```text
/admin/finance/categories
```

CRUD:

- List.
- Create.
- Edit.
- Show optional.
- Soft delete/archive if existing modules use that.
- Activate/deactivate.

### Cost centres

Routes under:

```text
/admin/finance/cost-centres
```

CRUD:

- List.
- Create.
- Edit.
- Show optional.
- Soft delete/archive if existing modules use that.
- Activate/deactivate.

### Cost entries

Routes under:

```text
/admin/finance/cost-entries
```

CRUD/actions:

- List with filters.
- Create.
- Store.
- Show.
- Edit only while draft.
- Update only while draft.
- Confirm.
- Void with reason.
- Delete only draft entries if existing modules support deletes; otherwise soft delete/archive.

List filters:

- Date range.
- Farm.
- Category.
- Cost centre.
- Source module.
- Status.
- Allocation type.

Create/edit form fields:

- Organisation/farm scope using existing conventions.
- Entry date.
- Title.
- Category.
- Cost centre.
- Source module.
- Reference label.
- Quantity/unit/unit cost/amount.
- Currency default KES.
- Payment state optional.
- Notes.
- Allocation section:
  - Simple mode: one allocation target and amount.
  - At minimum support one default allocation.
  - If multiple allocation rows are easy within existing Blade patterns, implement them.
  - If multiple allocation UI is too heavy, implement backend support and a single-allocation UI for this branch. State this in Codex summary.

### Reports

Routes under:

```text
/admin/finance/reports
```

Suggested pages:

- `GET /admin/finance/reports/summary`
- `GET /admin/finance/reports/categories`
- `GET /admin/finance/reports/allocations`
- `GET /admin/finance/reports/crop-cycles`
- `GET /admin/finance/reports/livestock`
- `GET /admin/finance/reports/assets`

Only show report pages that can be implemented cleanly with existing data.

Reports should use confirmed entries only by default.

Make draft and voided records visible only where useful, but do not include voided entries in normal totals.

## Farm/organisation scoping

Respect the existing multi-organisation / farm-aware access model.

Rules:

- A user should only see finance records for organisations/farms they are authorised to access.
- If the current project has helper methods/scopes for accessible organisations/farms, use them.
- Validate selected farms belong to the selected organisation.
- Validate categories and cost centres belong to the same organisation/farm scope as the cost entry.
- Validate linked operational references belong to the same organisation/farm scope where the linked model has those fields.

Do not add a broad bypass that exposes all records.

## Operational integrations for this branch

This branch may reference existing operational data manually, but must not create side effects.

Allowed:

- A cost entry can reference or allocate to:
  - Crop cycle.
  - Crop activity.
  - Livestock animal.
  - Livestock group.
  - Livestock event.
  - Irrigation event.
  - Asset.
  - Maintenance record.
  - Work order.
  - Task.
  - Inventory movement.
  - Worker.
  - Team.
  - General farm overhead.
- Reports can summarize costs by these references.

Forbidden in this branch:

- Do not deduct inventory stock.
- Do not create inventory movements.
- Do not change crop activity status.
- Do not change livestock treatment/feed records.
- Do not change irrigation events.
- Do not change asset service/breakdown state.
- Do not calculate payroll.
- Do not generate invoices or sales.
- Do not create supplier bills or payments.
- Do not create accounting journal entries.

## Demo / seed data

If the project has local/testing-only demo seeding conventions, add Finance demo data guarded the same way.

Demo data should include:

- Starter cost categories.
- A few cost centres.
- Sample confirmed cost entries.
- Sample draft cost entry.
- Allocations to at least:
  - General farm.
  - Crop cycle if demo crop data exists.
  - Livestock group/animal if demo livestock data exists.
  - Asset/maintenance if demo asset data exists.

Do not seed demo data in production.

## Validation

Add Form Requests or existing project validation pattern.

Validate:

- Required fields.
- Positive amounts.
- Valid enum/string choices.
- Category scope.
- Cost centre scope.
- Farm scope.
- Allocation total equals entry amount before confirmation.
- Confirm action only allowed from draft.
- Void action only allowed from draft or confirmed, with reason.
- Draft-only edit/update.

## Tests

Add feature tests matching existing style.

Minimum tests:

1. Guest cannot access `/admin/finance`.
2. User without `finance.view` cannot access finance dashboard.
3. User with `finance.view` can access finance dashboard.
4. User with `finance.manage` can create a cost category.
5. User with `finance.manage` can create a cost centre.
6. User with `finance.manage` can create a draft cost entry with one allocation.
7. Cost entry amount must be positive.
8. Allocation total must equal cost entry amount before confirmation.
9. Confirming a draft cost entry changes status to confirmed and records confirmer/timestamp where possible.
10. Confirmed entries are included in reports.
11. Voided entries are excluded from normal report totals.
12. User cannot view/manage finance records outside their organisation/farm scope.
13. Finance creation does not create inventory movements.
14. Finance creation does not create payroll, sales, supplier payment, or accounting ledger records. If those tables do not exist yet, assert the module did not add such tables or logic.

Use factories if existing modules use them. Otherwise follow current test setup style.

## Routes to verify

After implementation, run and report:

```powershell
php artisan route:list --path=admin/finance
```

Expected route groups should include:

- Finance dashboard.
- Categories.
- Cost centres.
- Cost entries.
- Reports.
- Confirm/void actions.

## Required validation commands

Run:

```powershell
php artisan migrate:fresh --seed
php artisan test
npm.cmd run build
php artisan route:list --path=admin/finance
git add --dry-run .
```

If any command fails, fix if practical. If not practical, stop and summarize the failure clearly.

## Forbidden files / git hygiene

Before commit, inspect changes carefully.

Do not commit:

- `.env`
- `vendor/`
- `node_modules/`
- Local SQLite databases.
- Logs.
- `bootstrap/cache/*.php`
- `storage/framework/views/*.php`
- `public/build/`

Always run:

```powershell
git branch
git status --short
git add --dry-run .
```

Confirm the branch is:

```text
finance-costing-module
```

## Codex final response requirements

When complete, summarize:

1. Branch used.
2. Files changed/created.
3. Database tables/migrations added.
4. Routes added.
5. Permissions added.
6. Tests added/updated.
7. Validation command results.
8. Any implementation compromises.
9. Risks/follow-ups.
10. Confirmation that forbidden logic was not added:
    - No full accounting ledger.
    - No bank reconciliation.
    - No tax/VAT filing.
    - No payroll engine.
    - No supplier payment workflow.
    - No sales/invoicing.
    - No inventory deduction.
    - No operational side effects in crops/livestock/irrigation/assets/tasks.

## Suggested commit message

Only after validation passes and branch is confirmed:

```powershell
git add .
git commit -m "Add finance costing module"
git push origin finance-costing-module
```

Do not commit if validation fails or if the branch is wrong.
