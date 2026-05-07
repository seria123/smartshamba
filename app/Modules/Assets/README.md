# Assets / Maintenance Module

This module adds the operational foundation for farm assets, maintenance schedules, maintenance records, breakdowns, and usage history.

## Entities

- `AssetCategory`: system or organization-level grouping for assets.
- `Asset`: farm-scoped physical asset with location, assignment, status, condition, and service dates.
- `AssetMaintenanceSchedule`: planned maintenance metadata without recurring generation.
- `AssetMaintenanceRecord`: actual service work, optional product reference, and service date updates.
- `AssetBreakdownRecord`: failure records that can mark assets broken down and resolve them.
- `AssetUsageRecord`: simple usage history linked to tasks, irrigation events, workers, teams, and locations.

## Routes

All routes live under `/admin/assets` and use `assets.*` names. They are protected by `auth` and `admin.access:*` permissions.

## Permissions

Starter permissions are seeded for asset viewing, management, category CRUD, asset CRUD/status, schedules, records, breakdowns, and usage. Owner and system admin receive all permissions. Farm manager receives operational management permissions. Auditor receives view permissions, while storekeeper, agronomist, and livestock officer receive limited asset visibility.

## Business Rules

- Assets and all operational records are organization and farm scoped.
- Asset code is unique within a farm.
- Linked farm locations, workers, teams, tasks, and irrigation events must belong to the selected farm.
- Assigned users must have active organization membership with matching farm access or organization-level access.
- Assets and categories are deactivated by status, not hard deleted.
- Maintenance records may update service dates.
- Open breakdowns mark assets broken down; resolution returns them to active when simple.

## Known Limitations

This branch does not deduct inventory, post finance costs, calculate depreciation, manage procurement, track fuel, integrate GPS/IoT, run payroll, generate QR/barcodes, or provide advanced analytics.
