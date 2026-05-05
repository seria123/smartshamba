# Core Module

The Core module owns shared platform primitives for the modular mixed-farm
management platform. It currently provides organization/farm boundaries, farm
structure records, a module registry, simple admin routes, and relationship
tests.

## Implemented Entities

- `Organization`: root tenant/account boundary.
- `Farm`: belongs to an organization.
- `Site`: belongs to an organization and farm.
- `Field`: belongs to an organization, farm, and optional site.
- `Paddock`: belongs to an organization, farm, and optional site.
- `Warehouse`: belongs to an organization, farm, and optional site.
- `ModuleRegistry`: tracks planned/active modules.

Existing foundation tables for `organizations` and `farms` are reused. New
tables are added through additive migrations only.

## Relationships

- Organization has many farms, sites, fields, paddocks, and warehouses.
- Farm belongs to organization and has many sites, fields, paddocks, and
  warehouses.
- Site belongs to organization and farm, and has many fields, paddocks, and
  warehouses.
- Field, paddock, and warehouse belong to organization, farm, and optionally a
  site.

## Routes

All routes are prefixed with `/admin/core` and named with `core.`. These routes
require authentication and the `core.view` permission.

- `GET /admin/core`: core dashboard.
- `GET /admin/core/organizations`: organizations list.
- `GET /admin/core/organizations/{organization}`: organization detail.
- `GET /admin/core/farms`: farms list.
- `GET /admin/core/farms/{farm}`: farm detail.
- Resource routes, except destroy, for sites, fields, paddocks, and warehouses.
- `GET /admin/core/modules`: module registry list.

## Structure

- Models: `app/Modules/Core/Models`
- Controllers: `app/Modules/Core/Http/Controllers`
- Routes: `app/Modules/Core/routes/web.php`
- Views: `resources/views/core`
- Seeder: `database/seeders/CoreFoundationSeeder.php`
- Tests: `tests/Feature/CoreAdminModuleTest.php`

## Known Limitations

- No authentication or authorization middleware is applied yet.
- Organizations and farms are list/view only; creation workflows can be added
  after account ownership rules are approved.
- No crop cycles, animal records, inventory stock movement, task assignment, or
  finance/costing are implemented.
- Delete actions are intentionally omitted until audit and archival rules are
  defined.
