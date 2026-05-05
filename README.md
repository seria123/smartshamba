# SmartShamba Farm Platform Foundation

Blank-slate Laravel foundation for a modular mixed-farm management platform.

This branch is intentionally isolated on `farm-platform-foundation`. Do not
merge or push this work to `main` until the main developer approves adoption.

## Architecture

The platform is planned as a Laravel modular monolith. Each domain module lives
inside `app/Modules` and should own its application services, policies, requests,
actions, data objects, and module-specific routes where needed.

Initial module boundaries:

- `Core`: shared primitives, tenancy/farm boundary, base contracts, cross-cutting
  services.
- `UsersPermissions`: users, teams, roles, permissions, access control.
- `Workers`: farm workers and staff operations.
- `Tasks`: work planning and operational tasks.
- `Inventory`: stock, inputs, storage, and movement tracking.
- `Crops`: crop production workflows. Not implemented yet.
- `Livestock`: livestock workflows. Not implemented yet.
- `Irrigation`: water sources, zones, schedules, and logs.
- `Finance`: expenses, budgets, payroll, and accounting summaries.
- `SalesTraceability`: sales, customers, batches, and traceability records.
- `Reports`: reporting, exports, and dashboards.
- `Settings`: platform and organization configuration.

## Current Scope

This commit contains foundation only:

- Laravel application skeleton and dependency definitions.
- Authentication-ready user model, auth config, and auth view folders.
- Basic Blade app layout and welcome page.
- Module folder structure.
- Environment example.
- Core database migration placeholders for users, organizations, farms,
  memberships, and audit events.

No crop, livestock, inventory, finance, or other business features are
implemented yet.

## Local Setup

PHP and Composer are required before dependencies can be installed. The Laravel
13 documentation recommends installing PHP, Composer, and the Laravel installer
before creating or running applications.

After PHP and Composer are available:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

For frontend assets:

```bash
npm install
npm run dev
```
