# Users and Permissions Module

The Users and Permissions module provides the first authentication and access
control foundation for the modular farm platform.

## Implemented Entities

- `Role`: system role definitions such as owner, system-admin, farm-manager, and
  auditor.
- `Permission`: granular access flags grouped by Core/Admin,
  Users/Permissions, Modules, and Reports.
- `OrganizationMembership`: extends the existing `organization_user` foundation
  table with `role_id`, optional `farm_id`, and membership status.

The module does not duplicate organizations or farms. User role assignment is
stored through organization membership scope, with an optional farm scope.

## Routes

- `GET /login`: login form.
- `POST /login`: authenticate active users.
- `POST /logout`: log out.
- `GET /admin/access`: access dashboard.
- Resource routes for `/admin/access/users`, except hard delete.
- `POST /admin/access/users/{user}/deactivate`: soft deactivation by status.
- `GET /admin/access/roles`: role list.
- `GET /admin/access/roles/{role}`: role detail and permissions.

All `/admin/access` routes require authentication and the `access.manage`
permission. `/admin/core` routes require authentication and the `core.view`
permission.

## Seeded Roles

- owner
- system-admin
- farm-manager
- agronomist
- livestock-officer
- storekeeper
- finance-officer
- farm-hand
- contractor
- auditor

## Seeded Permission Groups

- Core/Admin: `core.view`, `core.manage`
- Users/Permissions: `access.view`, `access.manage`
- Modules: `modules.view`, `modules.manage`
- Reports: `reports.view`, `reports.manage`

The `owner` and `system-admin` roles receive all permissions. Other roles receive
limited starter permissions and can be adjusted later through role management
flows.

## Local Demo User

`UsersPermissionsSeeder` creates a local demo administrator:

- Email: `admin@smartshamba.test`
- Password: `password`

## Known Limitations

- Password reset and registration flows are not implemented yet.
- Role-permission editing screens are read-only for now; seeding owns default
  permissions.
- Membership editing currently manages the first organization membership for a
  user.
- No crop, livestock, inventory, task, finance, irrigation, sales, or reports
  business features are implemented here.
