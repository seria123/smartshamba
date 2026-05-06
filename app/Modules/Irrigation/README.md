# Irrigation / Water Management Module

The Irrigation module provides the operational foundation for farm water sources, irrigation zones, planned schedules, actual irrigation events, manual water readings, and irrigation issues.

It intentionally does not implement crop-stage recommendations, sensor/IoT integrations, weather APIs, pump/equipment assets, fuel or energy inventory deduction, finance posting, payroll, sales/traceability, AI, or mobile/offline sync.

## Entities

- `irrigation_water_sources`: farm-specific water sources such as boreholes, rivers, dams, tanks, and wells.
- `irrigation_zones`: farm irrigation areas linked to optional sites, fields, and water sources.
- `irrigation_schedules`: planned irrigation work.
- `irrigation_events`: actual irrigation performed.
- `irrigation_water_readings`: manual readings for sources or zones.
- `irrigation_issues`: operational problems such as low pressure, blocked lines, leaks, or water shortages.

## Routes

All routes use the `/admin/irrigation` prefix, `irrigation.*` route names, `auth`, and the relevant permission middleware.

- Dashboard: `irrigation.dashboard`
- Water sources: `irrigation.water-sources.*`
- Zones: `irrigation.zones.*`
- Schedules: `irrigation.schedules.*`
- Events: `irrigation.events.*`
- Readings: `irrigation.readings.*`
- Issues: `irrigation.issues.*`

## Permissions

Seeded starter permissions:

- `irrigation.view`, `irrigation.manage`
- `water-sources.view/create/update/deactivate`
- `irrigation-zones.view/create/update/deactivate`
- `irrigation-schedules.view/create/update/cancel`
- `irrigation-events.view/create/update/cancel`
- `water-readings.view/create`
- `irrigation-issues.view/create/update/resolve`

Default role assignments:

- `owner` and `system-admin`: all permissions.
- `farm-manager`: irrigation operational management permissions.
- `agronomist`: irrigation operational permissions.
- `auditor`: view-only permissions.
- `storekeeper` and `finance-officer`: view-only irrigation access where useful.

## Business Rules

- All irrigation records are organization and farm scoped.
- Water source and zone codes are unique within a farm.
- Sources and zones are deactivated by status, not hard deleted.
- Zone site, field, and water source references must belong to the selected farm.
- Schedule zone, field, crop cycle, task, worker, team, and assigned user references must match the selected farm/scope.
- Event zone, source, field, crop cycle, schedule, task, worker, team, and performed user references must match the selected farm/scope.
- Readings must reference a water source or irrigation zone.
- Issues can be created and resolved by status.
- Schedules represent planned work; events represent actual work.
- No costs are posted and no inventory is deducted.

## Demo Data

`IrrigationModuleSeeder` creates demo sources, zones, a schedule, an event, a reading, and an open issue only in `local` and `testing` environments.

## Known Limitations

- Forms use simple selects; dynamic filtering by farm/source/zone/field/crop cycle can be added later.
- No recurring schedule engine or notification/reminder system.
- No sensor, weather, asset, fuel/energy, finance, AI, payroll, sales, traceability, or mobile/offline workflows.

## Follow-Up Work

- Dynamic dependent selects and better schedule-to-event shortcuts.
- Recurring irrigation schedules.
- Notifications/reminders.
- Sensor/IoT and weather integration.
- Pump/equipment asset linkage.
- Fuel/energy stock deduction.
- Irrigation cost posting to Finance.
- Crop-stage recommendations and issue-to-maintenance workflows.
