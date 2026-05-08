# Codex Prompt — Reports / Analytics / Farm Performance Module

## Project

You are working on a Laravel 13 modular monolith mixed-farm management platform.

This project is being built one module per Git branch. The completed branches/modules so far include:

1. `farm-platform-foundation`
2. `core-admin-module`
3. `users-permissions-module`
4. `workers-labour-module`
5. `tasks-work-orders-module`
6. `inventory-inputs-module`
7. `crops-module`
8. `livestock-module`
9. `irrigation-module`
10. `assets-maintenance-module`
11. `finance-costing-module`
12. `sales-produce-revenue-module`

You are now implementing the next module:

```text
reports-analytics-module
```

This module must be built as a **read-only reporting and farm performance analytics layer**.

It must not create operational side effects. It must not mutate data in Finance, Sales, Crops, Livestock, Inventory, Irrigation, Assets, Tasks, Workers, or Core/Admin.

---

## Required branch workflow

Before writing code, confirm the current branch.

Expected current branch:

```text
reports-analytics-module
```

Recommended setup if the branch does not exist yet:

```powershell
git checkout sales-produce-revenue-module
git pull origin sales-produce-revenue-module
git checkout -b reports-analytics-module
```

Start by running:

```powershell
git branch --show-current
git status --short
```

If the current branch is not `reports-analytics-module`, stop and report the mismatch.

---

## Preflight inspection requirement

Before implementing, inspect the current codebase and confirm the expected previous modules exist.

Check for the existence and conventions of these areas:

```text
app/Modules/Core
app/Modules/Access or existing users/permissions module location
app/Modules/Labour or workers/labour module location
app/Modules/Tasks or work-orders module location
app/Modules/Inventory
app/Modules/Crops
app/Modules/Livestock
app/Modules/Irrigation
app/Modules/Assets
app/Modules/Finance
app/Modules/Sales
resources/views/layouts/app.blade.php
bootstrap/app.php
database/seeders/CoreFoundationSeeder.php
database/seeders/UsersPermissionsSeeder.php
database/seeders/DatabaseSeeder.php
tests/TestCase.php
```

Also inspect the actual models, table names, enum/status patterns, permission middleware patterns, route naming conventions, and Blade layout conventions before adding code.

Do **not** guess model names if they differ from this prompt. Use the actual project conventions.

After inspection, summarize what you found before editing.

---

## Main goal

Create a Reports / Analytics module that gives farm owners and managers a single place to understand performance across the whole platform.

The module should answer management questions such as:

```text
- What is the farm spending?
- What is the farm earning?
- What is the gross margin?
- Which crop cycles are profitable?
- Which livestock lines are profitable?
- Which buyers/customers are generating revenue?
- What are the biggest cost drivers?
- What are the monthly cost/revenue trends?
- What operational issues need attention?
```

This is a **management reporting module**, not a full BI platform and not an accounting module.

---

## Critical scope boundary

This module must be **read-only**.

Allowed:

```text
- Reading existing records
- Aggregating totals
- Filtering by date range, organisation, farm, crop cycle, livestock target, customer, category, status, and similar fields where available
- Showing dashboards, summary cards, and tables
- Resolving readable labels for linked records
- Providing conservative calculations based on existing data
```

Forbidden:

```text
- Creating cost entries
- Creating sales records
- Creating payments
- Updating crop cycles
- Updating harvests
- Updating livestock animals/groups
- Updating inventory quantities
- Updating irrigation events
- Updating asset status
- Updating task/work-order status
- Posting to Finance
- Posting to Sales
- Deducting inventory
- Generating invoices
- Generating accounting journals
- Bank reconciliation
- VAT/tax filing
- Payroll reports or payroll calculations
- M-Pesa/payment gateway integration
- PDF/Excel export unless the existing app already has a safe export pattern
- Charting libraries unless already used in the project
```

Prefer simple Blade cards and tables. Do not introduce heavy frontend/reporting dependencies.

---

## Suggested module structure

Create a new module under:

```text
app/Modules/Reports
```

Suggested structure:

```text
app/Modules/Reports/
  Http/
    Controllers/
      ReportsDashboardController.php
      FarmPerformanceReportController.php
      CropProfitabilityReportController.php
      LivestockProfitabilityReportController.php
      CostRevenueReportController.php
      CustomerRevenueReportController.php
      CostDriversReportController.php
      MonthlyTrendsReportController.php
      OperationalActivityReportController.php
      InventorySnapshotReportController.php
      AssetHealthReportController.php
  Providers/
    ReportsServiceProvider.php
  Services/
    ReportsAccessContext.php
    FarmPerformanceReportService.php
    CropProfitabilityReportService.php
    LivestockProfitabilityReportService.php
    CostRevenueReportService.php
    CustomerRevenueReportService.php
    CostDriversReportService.php
    MonthlyTrendsReportService.php
    OperationalActivityReportService.php
    InventorySnapshotReportService.php
    AssetHealthReportService.php
    ReportLabelResolver.php
  routes/
    web.php
```

Models are probably not needed because this module should not own operational data. If a model is required for a report configuration table, stop and justify it first. The preferred implementation is **no new database tables**.

---

## Permissions

Add reporting permissions using the existing first-party RBAC pattern.

Suggested permissions:

```text
reports.view
reports.analytics
```

Use the current permission style from `UsersPermissionsSeeder.php`.

Minimum behavior:

```text
- A user must be authenticated.
- A user must have reporting permission before accessing `/admin/reports`.
- Respect existing organisation/farm access rules.
```

If the project has a standard permission middleware convention, follow it exactly.

Do not give ordinary users global access to all organisations/farms.

---

## Navigation

Update the main admin layout navigation to include:

```text
Reports
```

Suggested route:

```text
/admin/reports
```

Suggested placement:

```text
After Sales / Revenue, or near Finance / Costing
```

Only show the navigation item to users who have the relevant reporting permission if the current layout already supports permission-aware nav.

---

## Routes

Create GET-only report routes.

Suggested route group:

```php
Route::prefix('admin/reports')
    ->name('admin.reports.')
    ->middleware(['auth', /* existing permission middleware */])
    ->group(function () {
        Route::get('/', [ReportsDashboardController::class, 'index'])->name('dashboard');

        Route::get('/farm-performance', [FarmPerformanceReportController::class, 'index'])->name('farm-performance');
        Route::get('/crop-profitability', [CropProfitabilityReportController::class, 'index'])->name('crop-profitability');
        Route::get('/livestock-profitability', [LivestockProfitabilityReportController::class, 'index'])->name('livestock-profitability');
        Route::get('/cost-vs-revenue', [CostRevenueReportController::class, 'index'])->name('cost-vs-revenue');
        Route::get('/customers', [CustomerRevenueReportController::class, 'index'])->name('customers');
        Route::get('/cost-drivers', [CostDriversReportController::class, 'index'])->name('cost-drivers');
        Route::get('/monthly-trends', [MonthlyTrendsReportController::class, 'index'])->name('monthly-trends');
        Route::get('/operations', [OperationalActivityReportController::class, 'index'])->name('operations');
        Route::get('/inventory-snapshot', [InventorySnapshotReportController::class, 'index'])->name('inventory-snapshot');
        Route::get('/asset-health', [AssetHealthReportController::class, 'index'])->name('asset-health');
    });
```

Adapt controller names and middleware to match the existing codebase.

Do not add POST, PUT, PATCH, or DELETE routes for this module unless absolutely necessary. For this branch, the expected implementation is GET-only.

---

## Filters

Every report should support a practical set of filters where applicable:

```text
- organisation_id
- farm_id
- date_from
- date_to
```

Additional filters where useful:

```text
- crop_cycle_id
- customer_id
- cost_category_id
- target_type
- status
```

Validation requirements:

```text
- Date filters must be valid dates.
- date_to must be same or after date_from.
- organisation/farm filters must respect user access.
- If the user has access only to one organisation/farm, default filters should be safely scoped.
```

Use existing organisation/farm access helpers if available. If no reusable helper exists, create a small `ReportsAccessContext` service that derives the accessible organisation/farm IDs from the current membership model.

Do not leak data across organisations.

---

## Dashboard

Create:

```text
resources/views/reports/dashboard.blade.php
```

Dashboard summary cards should include, where data exists:

```text
- Total confirmed costs
- Total revenue
- Gross margin
- Gross margin %
- Outstanding customer payments
- Recent sales
- Recent cost entries
- Active crop cycles
- Livestock summary
- Low stock or inventory warning count
- Open tasks/work orders
- Open irrigation issues
- Asset breakdown count
```

If a data source is unavailable or empty, show a graceful zero/empty state.

Do not crash because one module has no data.

---

## Report 1: Farm Performance Summary

Route:

```text
/admin/reports/farm-performance
```

Purpose:

Show farm-level cost, revenue, margin, and operational summary.

Suggested fields:

```text
Farm
Total Costs
Total Revenue
Gross Margin
Gross Margin %
Outstanding Payments
Active Crop Cycles
Livestock Count / Group Count
Open Tasks
Asset Breakdowns
```

Calculation rules:

```text
Gross Margin = Total Revenue - Total Costs
Gross Margin % = Gross Margin / Total Revenue * 100
If revenue is zero, margin % should be null or displayed as —
```

Do not include depreciation, payroll accruals, taxes, or accounting adjustments unless they already exist as normal Finance cost entries.

---

## Report 2: Crop Cycle Profitability

Route:

```text
/admin/reports/crop-profitability
```

Purpose:

Compare crop-cycle costs, revenue, harvest quantities, and margins.

Data sources:

```text
- Crop cycles from Crops module
- Finance allocations targeting crop cycles where available
- Sales lines or sales records linked to crop cycles/harvests where available
- Harvest records where available
```

Suggested fields:

```text
Crop Cycle
Farm / Field
Crop / Variety
Status
Area
Total Cost Allocated
Total Revenue
Gross Margin
Gross Margin %
Harvest Quantity
Revenue per Harvest Unit
Cost per Harvest Unit
```

Important:

The Sales module follow-up says gross margin currently uses conservative target type/id labels. In this Reports module, improve label resolution where safe by resolving crop cycle names, crop names, field/block names, animal/group names, asset names, and customer names from actual models.

If a link is missing, show a conservative fallback like:

```text
crop_cycle #12
animal_group #5
asset #3
```

Do not mutate crop cycles or harvests.

---

## Report 3: Livestock Profitability

Route:

```text
/admin/reports/livestock-profitability
```

Purpose:

Compare livestock costs and revenue by individual animal, group, species, or farm where available.

Data sources:

```text
- Livestock animals and groups
- Finance allocations targeting livestock animals/groups/species where available
- Sales lines or sales records linked to livestock animals/groups where available
- Livestock yields where available
```

Suggested fields:

```text
Target
Type
Species / Breed
Farm / Paddock
Total Cost Allocated
Total Revenue
Gross Margin
Yield Quantity
Mortality / Disposal indicator where available
```

Do not update animal status, group counts, mortality, sales/disposal records, or yields.

---

## Report 4: Cost vs Revenue by Target

Route:

```text
/admin/reports/cost-vs-revenue
```

Purpose:

Show a unified table of cost/revenue/margin by target.

Suggested grouping:

```text
- crop_cycle
- livestock_animal
- livestock_group
- asset
- farm_overhead
- general
```

Use the actual target types used by Finance and Sales.

Suggested fields:

```text
Target Type
Target Label
Total Cost
Total Revenue
Gross Margin
Gross Margin %
```

This report is allowed to be conservative. It is better to show accurate target type/id labels than to guess wrong names.

---

## Report 5: Customer Revenue

Route:

```text
/admin/reports/customers
```

Purpose:

Show revenue and payment position by buyer/customer.

Data sources:

```text
- Sales customers
- Sale records
- Sale lines
- Sale payments
```

Suggested fields:

```text
Customer
Sales Count
Total Sales
Amount Paid
Outstanding Amount
Last Sale Date
Top Item / Produce Type where available
```

No payment reminders, messages, statements, or invoice sending in this branch.

---

## Report 6: Cost Drivers by Category

Route:

```text
/admin/reports/cost-drivers
```

Purpose:

Show what the farm spends money on.

Data sources:

```text
- finance_cost_categories
- finance_cost_entries
- finance_cost_allocations
```

Suggested fields:

```text
Cost Category
Total Amount
Entry Count
Average Entry Amount
Percentage of Total Costs
Top Farm / Target where available
```

Only include confirmed/active cost entries based on the Finance module’s actual status rules. Exclude voided/cancelled entries.

---

## Report 7: Monthly Trends

Route:

```text
/admin/reports/monthly-trends
```

Purpose:

Show month-by-month costs, revenue, margin, and payments.

Suggested fields:

```text
Month
Costs
Revenue
Payments Received
Outstanding Created
Gross Margin
```

No external chart library required. A table is acceptable. If the app already has simple chart patterns, use them lightly, but do not introduce a new dependency.

---

## Report 8: Operational Activity Report

Route:

```text
/admin/reports/operations
```

Purpose:

Summarize non-financial operations that affect farm performance.

Data sources:

```text
- Tasks/work orders
- Crop activities/treatments/harvests/losses
- Livestock treatments/feed/events/yields/mortality
- Irrigation schedules/events/issues
- Worker attendance where available
```

Suggested sections:

```text
- Open work orders/tasks
- Completed tasks in selected period
- Crop activities in selected period
- Livestock events in selected period
- Irrigation events and issues
- Attendance count/summary where available
```

This is a summary report only. Do not create, approve, reject, complete, or cancel any operational record.

---

## Report 9: Inventory Snapshot

Route:

```text
/admin/reports/inventory-snapshot
```

Purpose:

Show stock visibility for management.

Data sources:

```text
- Inventory products/categories
- Stock lots
- Inventory movements
```

Suggested fields:

```text
Product
Category
Farm / Store / Warehouse
Quantity on Hand
Unit
Unit Cost
Estimated Stock Value
Expiry Date
Low-stock indicator where available
```

Do not adjust stock. Do not deduct stock. Do not create movements.

---

## Report 10: Asset Health

Route:

```text
/admin/reports/asset-health
```

Purpose:

Show asset maintenance and breakdown visibility.

Data sources:

```text
- Assets
- Maintenance schedules
- Maintenance records
- Breakdowns
- Usage records
```

Suggested fields:

```text
Asset
Category
Farm / Site
Status
Open Breakdowns
Last Service Date
Next Service Date
Usage Count / Hours where available
Maintenance Cost where linked through Finance allocations
```

Do not update asset status or service dates.

---

## Services and calculation rules

Create service classes for report calculations instead of stuffing complex queries into controllers.

Controller responsibilities:

```text
- Validate filters
- Build user access context
- Call service
- Return Blade view
```

Service responsibilities:

```text
- Query existing tables/models
- Apply access filters
- Apply date filters
- Aggregate totals
- Resolve labels
- Return arrays/DTO-style structures for views
```

Prefer readable, maintainable Laravel query builder/Eloquent code over clever SQL.

Where status values differ from the prompt, inspect and use actual existing status constants/values.

Financial calculations:

```text
- Use decimal-safe handling where possible.
- Format money in Blade views.
- Do not assume all currencies are KES unless existing modules already do.
- If mixed currencies exist, either group by currency or display currency column.
```

Avoid inaccurate combined totals across currencies. If the project stores currency, include it in grouping or display.

---

## Label resolver

Create a small `ReportLabelResolver` service.

Purpose:

Convert target types and IDs into readable labels where safe.

Examples:

```text
crop_cycle + 12       => Tomato Cycle — Field A
livestock_group + 5   => Dairy Cows Group
livestock_animal + 8  => Cow #A-008 / tag number if available
asset + 3             => Tractor KAA 123A
farm + 2              => Main Farm
farm_overhead + null  => Farm Overhead
general + null        => General / Unallocated
```

Use actual model names and columns after inspection.

If a target cannot be resolved, return a safe fallback:

```text
{target_type} #{target_id}
```

Do not fail the report because a linked record was deleted or not found.

---

## Blade view requirements

Create views under:

```text
resources/views/reports/
```

Suggested views:

```text
dashboard.blade.php
partials/filters.blade.php
farm-performance.blade.php
crop-profitability.blade.php
livestock-profitability.blade.php
cost-vs-revenue.blade.php
customers.blade.php
cost-drivers.blade.php
monthly-trends.blade.php
operations.blade.php
inventory-snapshot.blade.php
asset-health.blade.php
```

View style:

```text
- Use the existing app layout.
- Use existing card/table styling conventions.
- Keep screens practical and lightweight.
- Include clear empty states.
- Include filter form at top where useful.
- Do not create a separate frontend app.
```

No JavaScript-heavy dashboard for this branch.

---

## Seeder requirements

Prefer no new demo operational data.

If module registration requires seeding:

```text
- Register the Reports module in the existing module registry.
- Add permissions to the existing users/permissions seeder.
```

Do not create fake sales/cost/crop/livestock data if the existing seeders already provide enough demo data.

If absolutely necessary for tests, add minimal local/testing-only seeded data following the existing demo-data guard pattern.

Demo data must remain guarded to local/testing only if that is the established convention.

---

## Tests

Add feature tests:

```text
tests/Feature/ReportsAnalyticsModuleTest.php
```

Test coverage should include:

```text
- Reports routes require authentication.
- A user with reports permission can access the dashboard.
- A user without reports permission cannot access reports.
- Dashboard loads successfully with seeded data.
- Farm performance report loads.
- Crop profitability report loads.
- Livestock profitability report loads.
- Cost vs revenue report loads.
- Customer revenue report loads.
- Cost drivers report loads.
- Monthly trends report loads.
- Operational activity report loads.
- Inventory snapshot report loads.
- Asset health report loads.
- Filters validate bad date ranges.
- Reports do not create or mutate cost entries, sales records, inventory movements, crop cycles, livestock records, or asset records.
```

Mutation safety test idea:

```text
Capture counts of key tables before loading reports.
Visit several report routes.
Assert counts remain unchanged.
```

Use actual model/table names.

Do not make brittle assertions against exact seeded amounts unless those seed values are controlled in the test.

---

## Validation commands

After implementation, run:

```powershell
php artisan migrate:fresh --seed
php artisan test
npm.cmd run build
php artisan route:list --path=admin/reports
git branch --show-current
git status --short
git add --dry-run .
```

If `npm.cmd run build` fails because of sandbox/Windows/esbuild spawn permission, rerun with the appropriate local/sandbox escalation as done in prior modules.

Do not commit generated build artifacts.

---

## Files that must not be committed

Do not commit:

```text
.env
vendor/
node_modules/
local SQLite databases
logs
bootstrap/cache/*.php
storage/framework/views/*.php
public/build/
```

Also be careful with unrelated prompt docs under:

```text
docs/codex-prompts/
```

Only stage the Reports prompt doc if it is intentionally part of this branch:

```text
docs/codex-prompts/reports-analytics-module-codex-prompt.md
```

Do not stage unrelated prompt docs from previous modules unless explicitly requested.

---

## Expected final Codex summary

When done, report:

```text
- Current branch
- Previous modules detected
- Files changed
- Routes added
- Permissions added
- Services/controllers/views/tests added
- Whether a migration was added or not, and why
- Validation command results
- Any risks or follow-ups
- Confirmation that reports are read-only
- Confirmation that no forbidden logic was added
- Confirmation that no unrelated prompt docs/build artifacts/env/vendor/node_modules files were staged
```

---

## Important follow-ups to note, but not implement now

Do not implement these in this branch. Just mention them as future work if relevant:

```text
- Rich chart visualizations
- PDF/Excel exports
- Scheduled reports
- Email reports
- Role-specific dashboards
- Drill-down pages from dashboard cards
- Multi-currency consolidation rules
- Profitability formulas that include depreciation/payroll/tax
- Improved Finance UI for splitting one cost entry across multiple allocation targets
- Richer Sales/Farm target label mapping if some links are not yet available
```

---

## Final instruction

Implement the Reports / Analytics / Farm Performance module carefully and conservatively.

The platform already has operational modules, Finance/Costing, and Sales/Revenue. Your task is to create the first useful management intelligence layer over those records.

Prioritize:

```text
correctness
read-only safety
access control
clear calculations
simple maintainable code
```

Do not overbuild.
