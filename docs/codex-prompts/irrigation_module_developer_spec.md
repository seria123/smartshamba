# Irrigation / Water Management Module — Developer Specification

## 1. Purpose of This Module

The Irrigation / Water Management module manages farm water sources, irrigation zones, irrigation schedules, actual irrigation events, water readings, and irrigation issues.

This module is important because irrigation affects crop health, labour planning, pump/equipment usage, fuel/energy consumption, and future cost of production.

The system should help the farm answer questions such as:

- Which water sources are available on the farm?
- Which fields or crop areas are covered by which irrigation zones?
- What irrigation was scheduled?
- What irrigation actually happened?
- Which worker/team performed irrigation?
- Which task or work order was related to the irrigation?
- How long did irrigation run?
- What estimated water volume was used?
- Which irrigation events were missed?
- Which fields have recent irrigation history?
- Which issues occurred, such as pump breakdown, blocked lines, low pressure, or water shortage?

For this branch, the module should create the irrigation operational foundation only.

It should not yet implement automatic crop-stage decisions, sensor integrations, IoT, pump asset management, fuel/energy inventory deduction, finance cost posting, advanced weather integrations, or mobile/offline irrigation workflows.

---

## 2. Current Project Position

The project already has:

- Laravel 13 foundation.
- Core/Admin module.
- Organisation and farm hierarchy.
- Sites/zones.
- Fields/blocks.
- Paddocks/pens.
- Warehouses/stores.
- Module registry.
- Users / Roles / Permissions module.
- Login/logout.
- Auth-protected admin routes.
- Role/permission foundation.
- Organisation/farm membership scoping.
- Workers / Labour module.
- Worker profiles.
- Labour teams.
- Simple attendance.
- Tasks / Work Orders module.
- Work orders.
- Tasks.
- Task assignments.
- Task updates.
- Checklist items.
- Inventory / Inputs module.
- Products, stock lots, and inventory movements.
- Crops module.
- Crop cycles and crop operational records.
- Livestock module.
- Animal and animal group operational records.

The next logical step is Irrigation / Water Management because irrigation connects strongly to crop operations, tasks, workers, future assets, and future finance/costing.

---

## 3. Branching Note

Before starting this module, make sure the branch includes the Livestock work.

### If Livestock has been merged into main

```bash
git checkout main
git pull origin main
git checkout -b irrigation-module
```

### If Livestock has not yet been merged

```bash
git checkout livestock-module
git pull origin livestock-module
git checkout -b irrigation-module
```

Do not create this branch from a base that lacks Core/Admin, Users/Permissions, Workers/Labour, Tasks/Work Orders, Inventory/Inputs, Crops, and Livestock.

---

## 4. Module Objective

The objective of this module is to create the foundation for planning, recording, and reviewing farm irrigation activities.

The module should allow authorised users to:

1. Create water sources.
2. Create irrigation zones.
3. Link irrigation zones to farms, sites, and fields.
4. Create irrigation schedules.
5. Record actual irrigation events.
6. Record water readings.
7. Record irrigation issues.
8. Link irrigation events to tasks, workers, teams, fields, and crop cycles where appropriate.
9. View irrigation history by farm, field, zone, date, and crop cycle.
10. Prepare irrigation data for future assets, sensors, fuel/energy, and finance/costing workflows.

---

## 5. Scope of This Branch

### In scope

This branch should implement:

- Irrigation module provider/routes.
- Water source model/table.
- Irrigation zone model/table.
- Irrigation schedule model/table.
- Irrigation event model/table.
- Water reading model/table.
- Irrigation issue model/table.
- Irrigation dashboard.
- Water source list/create/view/edit/deactivate.
- Irrigation zone list/create/view/edit/deactivate.
- Schedule list/create/view/edit/cancel/complete.
- Event list/create/view/edit/cancel.
- Water reading list/create.
- Issue list/create/resolve.
- Auth and permission protection.
- Navigation entry.
- Seeder updates for irrigation permissions.
- Local/testing demo irrigation data only.
- Tests.
- README update.

### Out of scope

Do not implement:

- Automatic crop-stage irrigation recommendations.
- Sensor/IoT integrations.
- Weather API integrations.
- Pump/equipment asset management.
- Fuel/energy inventory deduction.
- Finance cost posting.
- Advanced water rights/compliance.
- Mobile/offline field sync.
- Automated notifications/reminders beyond simple records.
- Satellite imagery.
- AI water stress prediction.
- Payroll.
- Sales/traceability.

This branch should create irrigation records and relationships only.

---

## 6. Key Design Principle

Irrigation must be recordable as both planned and actual work.

A schedule answers:

```text
What should happen?
```

An event answers:

```text
What actually happened?
```

Do not treat irrigation as only a calendar item. Farms need actual irrigation history for crop performance and future costing.

---

## 7. Core Entities

## 7.1 Water Source

Represents a farm water source.

Examples:

- Borehole.
- River intake.
- Dam.
- Tank.
- Municipal supply.
- Well.
- Rainwater harvesting tank.

### Suggested table name

```text
irrigation_water_sources
```

### Suggested fields

```text
id
organization_id
farm_id
name
code
source_type
capacity nullable
capacity_unit nullable
location_description nullable
latitude nullable
longitude nullable
status
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `source_type` values

```text
borehole
river
dam
tank
well
municipal
rainwater
pond
other
```

### Suggested statuses

```text
active
inactive
under_maintenance
archived
```

### Rules

- Water source belongs to organisation and farm.
- Code should be unique within farm.
- Deactivate by status, not hard delete.

---

## 7.2 Irrigation Zone

Represents an area served by irrigation.

Examples:

- Tomato Block B Drip Zone.
- Greenhouse 1 Zone.
- Lower Farm Sprinkler Zone.
- Maize Field A Furrow Zone.

### Suggested table name

```text
irrigation_zones
```

### Suggested fields

```text
id
organization_id
farm_id
site_id nullable
field_id nullable
water_source_id nullable
name
code
zone_type
irrigation_method
area nullable
area_unit nullable
status
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `zone_type` values

```text
field_zone
greenhouse_zone
nursery_zone
orchard_zone
general_zone
other
```

### Suggested `irrigation_method` values

```text
drip
sprinkler
furrow
flood
manual
pivot
micro_sprinkler
other
```

### Rules

- Zone belongs to organisation and farm.
- Site must belong to selected farm where provided.
- Field must belong to selected farm where provided.
- Water source must belong to selected farm where provided.
- Code should be unique within farm.

---

## 7.3 Irrigation Schedule

Represents planned irrigation.

### Suggested table name

```text
irrigation_schedules
```

### Suggested fields

```text
id
organization_id
farm_id
irrigation_zone_id
field_id nullable
crop_cycle_id nullable
related_task_id nullable
schedule_number
scheduled_date
scheduled_start_time nullable
scheduled_end_time nullable
planned_duration_minutes nullable
planned_water_volume nullable
water_volume_unit nullable
priority
status
assigned_user_id nullable
assigned_worker_id nullable
assigned_team_id nullable
instructions nullable
created_by nullable
updated_by nullable
cancelled_by nullable
cancelled_at nullable
cancellation_reason nullable
completed_at nullable
created_at
updated_at
soft_deletes
```

### Suggested statuses

```text
planned
assigned
in_progress
completed
missed
cancelled
```

### Suggested priorities

```text
low
normal
high
urgent
```

### Rules

- Schedule belongs to organisation/farm/zone.
- Zone must belong to selected farm.
- Crop cycle must belong to selected farm and field where provided.
- Related task must belong to selected farm where provided.
- Worker/team must belong to selected farm where provided.
- Do not create recurring schedule engine yet.

---

## 7.4 Irrigation Event

Represents actual irrigation performed.

### Suggested table name

```text
irrigation_events
```

### Suggested fields

```text
id
organization_id
farm_id
irrigation_zone_id
water_source_id nullable
field_id nullable
crop_cycle_id nullable
schedule_id nullable
related_task_id nullable
event_number
irrigation_date
start_time nullable
end_time nullable
duration_minutes nullable
water_volume nullable
water_volume_unit nullable
method nullable
status
performed_by_user_id nullable
performed_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
updated_by nullable
cancelled_by nullable
cancelled_at nullable
cancellation_reason nullable
created_at
updated_at
soft_deletes
```

### Suggested statuses

```text
recorded
approved
cancelled
```

### Rules

- Event belongs to organisation/farm/zone.
- Zone must belong to selected farm.
- Water source must belong to selected farm where provided.
- Field must belong to selected farm where provided.
- Crop cycle must belong to selected farm where provided.
- Related schedule/task must belong to selected farm where provided.
- Do not post costs.
- Do not deduct fuel/energy stock.

---

## 7.5 Water Reading

Represents a manual water reading from a source, meter, tank, canal, or sensor placeholder.

### Suggested table name

```text
irrigation_water_readings
```

### Suggested fields

```text
id
organization_id
farm_id
water_source_id nullable
irrigation_zone_id nullable
reading_date
reading_type
value
unit_of_measure
recorded_by_user_id nullable
recorded_by_worker_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `reading_type` values

```text
meter_reading
tank_level
flow_rate
pressure
soil_moisture
rainfall
manual_observation
other
```

### Rules

- Water source or irrigation zone should be present.
- Do not integrate sensors yet.

---

## 7.6 Irrigation Issue

Represents an irrigation-related problem.

Examples:

- Pump failure.
- Low water pressure.
- Blocked drip line.
- Broken pipe.
- Water shortage.
- Over-irrigation.
- Under-irrigation.

### Suggested table name

```text
irrigation_issues
```

### Suggested fields

```text
id
organization_id
farm_id
irrigation_zone_id nullable
water_source_id nullable
field_id nullable
related_task_id nullable
issue_number
issue_date
issue_type
severity
status
description
reported_by_user_id nullable
reported_by_worker_id nullable
resolved_by nullable
resolved_at nullable
resolution_notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `issue_type` values

```text
pump_failure
blocked_line
broken_pipe
low_pressure
water_shortage
over_irrigation
under_irrigation
leakage
power_failure
other
```

### Suggested severity values

```text
low
medium
high
critical
```

### Suggested statuses

```text
open
in_progress
resolved
cancelled
```

---

## 8. Relationships

### WaterSource model

Should have:

```php
organization()
farm()
zones()
events()
readings()
issues()
createdBy()
```

### IrrigationZone model

Should have:

```php
organization()
farm()
site()
field()
waterSource()
schedules()
events()
readings()
issues()
createdBy()
```

### IrrigationSchedule model

Should have:

```php
organization()
farm()
zone()
field()
cropCycle()
relatedTask()
assignedUser()
assignedWorker()
assignedTeam()
events()
createdBy()
```

### IrrigationEvent model

Should have:

```php
organization()
farm()
zone()
waterSource()
field()
cropCycle()
schedule()
relatedTask()
performedByUser()
performedByWorker()
team()
createdBy()
```

### WaterReading model

Should have:

```php
organization()
farm()
waterSource()
zone()
recordedByUser()
recordedByWorker()
createdBy()
```

### IrrigationIssue model

Should have:

```php
organization()
farm()
zone()
waterSource()
field()
relatedTask()
reportedByUser()
reportedByWorker()
resolvedBy()
createdBy()
```

---

## 9. Permissions

Add starter permissions for the Irrigation module.

### Suggested permissions

```text
irrigation.view
irrigation.manage
water-sources.view
water-sources.create
water-sources.update
water-sources.deactivate
irrigation-zones.view
irrigation-zones.create
irrigation-zones.update
irrigation-zones.deactivate
irrigation-schedules.view
irrigation-schedules.create
irrigation-schedules.update
irrigation-schedules.cancel
irrigation-events.view
irrigation-events.create
irrigation-events.update
irrigation-events.cancel
water-readings.view
water-readings.create
irrigation-issues.view
irrigation-issues.create
irrigation-issues.update
irrigation-issues.resolve
```

### Suggested default role assignments

Owner:

```text
all irrigation permissions
```

System Admin:

```text
all irrigation permissions
```

Farm Manager:

```text
irrigation operational management permissions
```

Agronomist / Technical Officer:

```text
irrigation view, schedule, event, readings, issues
```

Irrigation Worker / Farm Hand:

```text
no admin screens yet; future assigned-task views only
```

Auditor:

```text
view-only permissions
```

Storekeeper / Finance Officer:

```text
view-only where useful
```

---

## 10. Routes

Suggested route prefix:

```text
/admin/irrigation
```

Route names:

```text
irrigation.*
```

### Suggested routes

```text
GET    /admin/irrigation                                      Irrigation dashboard

GET    /admin/irrigation/water-sources                        Water source list
GET    /admin/irrigation/water-sources/create                 Create water source form
POST   /admin/irrigation/water-sources                        Store water source
GET    /admin/irrigation/water-sources/{source}               Water source detail
GET    /admin/irrigation/water-sources/{source}/edit          Edit water source form
PUT    /admin/irrigation/water-sources/{source}               Update water source
POST   /admin/irrigation/water-sources/{source}/deactivate    Deactivate water source

GET    /admin/irrigation/zones                                Zone list
GET    /admin/irrigation/zones/create                         Create zone form
POST   /admin/irrigation/zones                                Store zone
GET    /admin/irrigation/zones/{zone}                         Zone detail
GET    /admin/irrigation/zones/{zone}/edit                    Edit zone form
PUT    /admin/irrigation/zones/{zone}                         Update zone
POST   /admin/irrigation/zones/{zone}/deactivate              Deactivate zone

GET    /admin/irrigation/schedules                            Schedule list
GET    /admin/irrigation/schedules/create                     Create schedule form
POST   /admin/irrigation/schedules                            Store schedule
GET    /admin/irrigation/schedules/{schedule}                 Schedule detail
GET    /admin/irrigation/schedules/{schedule}/edit            Edit schedule form
PUT    /admin/irrigation/schedules/{schedule}                 Update schedule
POST   /admin/irrigation/schedules/{schedule}/cancel          Cancel schedule
POST   /admin/irrigation/schedules/{schedule}/complete        Complete schedule

GET    /admin/irrigation/events                               Event list
GET    /admin/irrigation/events/create                        Create event form
POST   /admin/irrigation/events                               Store event
GET    /admin/irrigation/events/{event}                       Event detail
GET    /admin/irrigation/events/{event}/edit                  Edit event form
PUT    /admin/irrigation/events/{event}                       Update event
POST   /admin/irrigation/events/{event}/cancel                Cancel event

GET    /admin/irrigation/readings                             Reading list
GET    /admin/irrigation/readings/create                      Create reading form
POST   /admin/irrigation/readings                             Store reading

GET    /admin/irrigation/issues                               Issue list
GET    /admin/irrigation/issues/create                        Create issue form
POST   /admin/irrigation/issues                               Store issue
GET    /admin/irrigation/issues/{issue}                       Issue detail
GET    /admin/irrigation/issues/{issue}/edit                  Edit issue form
PUT    /admin/irrigation/issues/{issue}                       Update issue
POST   /admin/irrigation/issues/{issue}/resolve               Resolve issue
```

### Middleware

All routes should require:

```text
auth
appropriate irrigation permission
```

Use the existing permission middleware pattern.

---

## 11. Controllers

Suggested controllers:

```text
IrrigationDashboardController
WaterSourceController
IrrigationZoneController
IrrigationScheduleController
IrrigationEventController
WaterReadingController
IrrigationIssueController
```

Keep controllers readable.

Do not place finance, fuel, sensor, or crop recommendation logic here.

---

## 12. Validation Rules

### Water source validation

Required:

- organization_id.
- farm_id.
- name.
- code.
- source_type.
- status.

Rules:

- farm must belong to organisation.
- code unique within farm.

### Irrigation zone validation

Required:

- organization_id.
- farm_id.
- name.
- code.
- zone_type.
- irrigation_method.
- status.

Optional:

- site_id.
- field_id.
- water_source_id.

Rules:

- site must belong to selected farm.
- field must belong to selected farm.
- water source must belong to selected farm.
- code unique within farm.

### Irrigation schedule validation

Required:

- organization_id.
- farm_id.
- irrigation_zone_id.
- scheduled_date.
- priority.
- status.

Optional:

- field_id.
- crop_cycle_id.
- related_task_id.
- assigned_user_id.
- assigned_worker_id.
- assigned_team_id.
- scheduled_start_time.
- scheduled_end_time.
- planned_duration_minutes.

Rules:

- irrigation zone must belong to selected farm.
- field must belong to selected farm where provided.
- crop cycle must belong to selected farm where provided.
- related task must belong to selected farm where provided.
- worker/team must belong to selected farm where provided.
- assigned user must have active membership in organisation/farm or organisation-level access.

### Irrigation event validation

Required:

- organization_id.
- farm_id.
- irrigation_zone_id.
- irrigation_date.
- status.

Optional:

- water_source_id.
- field_id.
- crop_cycle_id.
- schedule_id.
- related_task_id.
- performed_by_user_id.
- performed_by_worker_id.
- team_id.
- start_time.
- end_time.
- duration_minutes.
- water_volume.

Rules:

- zone/source/field/crop cycle/schedule/task must belong to selected farm where provided.
- worker/team must belong to selected farm where provided.
- performed user must have active membership in organisation/farm or organisation-level access.
- water volume must be numeric if provided.
- duration must be numeric if provided.

### Water reading validation

Required:

- organization_id.
- farm_id.
- reading_date.
- reading_type.
- value.
- unit_of_measure.

Rules:

- water source or irrigation zone should be provided.
- source/zone must belong to selected farm where provided.
- value must be numeric.

### Irrigation issue validation

Required:

- organization_id.
- farm_id.
- issue_date.
- issue_type.
- severity.
- status.
- description.

Optional:

- irrigation_zone_id.
- water_source_id.
- field_id.
- related_task_id.

Rules:

- zone/source/field/task must belong to selected farm where provided.

---

## 13. Views / UI

Keep UI simple and operational.

### 13.1 Irrigation dashboard

Should show links/cards for:

- Water Sources.
- Irrigation Zones.
- Schedules.
- Events.
- Water Readings.
- Issues.

Optional simple metrics:

- Active water sources.
- Active zones.
- Schedules today.
- Missed schedules.
- Open issues.
- Recent irrigation events.

Do not build advanced analytics yet.

### 13.2 Water source list

Columns:

- Code.
- Name.
- Type.
- Farm.
- Capacity.
- Status.
- Actions.

### 13.3 Irrigation zone list

Columns:

- Code.
- Name.
- Farm.
- Field.
- Water source.
- Method.
- Status.
- Actions.

### 13.4 Schedule list

Columns:

- Schedule number.
- Date.
- Zone.
- Field.
- Crop cycle.
- Priority.
- Status.
- Assigned to.
- Actions.

### 13.5 Event list

Columns:

- Event number.
- Date.
- Zone.
- Field.
- Duration.
- Water volume.
- Performed by.
- Status.
- Actions.

### 13.6 Water reading list

Columns:

- Date.
- Source/Zone.
- Reading type.
- Value.
- Unit.
- Recorded by.
- Actions.

### 13.7 Issue list

Columns:

- Issue number.
- Date.
- Type.
- Severity.
- Status.
- Zone/source.
- Actions.

### 13.8 Forms

Forms should use simple selects for now.

Known later improvement: dynamic filtering by farm, field, zone, source, crop cycle, worker, and team.

---

## 14. Navigation

Add Irrigation / Water to the admin/module navigation if the user has relevant permissions.

Suggested label:

```text
Irrigation / Water
```

Suggested left menu inside module:

```text
Irrigation Dashboard
Water Sources
Zones
Schedules
Events
Readings
Issues
```

For this branch, a simple navigation link is enough.

Do not show irrigation links to users without irrigation permissions.

Backend routes must still enforce permissions.

---

## 15. Business Rules

### Rule 1: Irrigation records are organisation/farm scoped

Every source, zone, schedule, event, reading, and issue must belong to organisation and farm.

### Rule 2: Water sources and zones are farm-specific

Do not assign a zone to a water source from another farm.

### Rule 3: Fields and crop cycles must belong to the selected farm

Do not link irrigation events to crop cycles from another farm.

### Rule 4: Planned and actual irrigation are different

Schedules are plans. Events are actual irrigation records.

### Rule 5: Do not post costs yet

Water, labour, fuel, and electricity costs come later in Finance/Costing.

### Rule 6: Do not deduct fuel or other stock yet

Inventory usage for pumps or fuel comes later.

### Rule 7: Do not integrate sensors yet

Water readings are manual records for now.

### Rule 8: Deactivate sources and zones instead of deleting

Future records may reference them.

### Rule 9: Demo irrigation data should be local/testing only

Do not seed demo irrigation data into production.

### Rule 10: User assignment must respect membership scope

Assigned/performed users must belong to the task/event organisation and either the same farm or organisation-level access.

---

## 16. Integration with Other Modules

### 16.1 Core/Admin

Irrigation depends on:

- organisations,
- farms,
- sites,
- fields.

### 16.2 Users/Permissions

Irrigation access depends on permissions and membership scope.

### 16.3 Workers/Labour

Irrigation schedules/events may reference workers and teams.

### 16.4 Tasks / Work Orders

Irrigation schedules/events/issues may reference related tasks.

For now, task references are informational.

Do not auto-create or complete tasks yet.

### 16.5 Crops

Irrigation schedules/events may reference crop cycles.

Do not update crop growth stage or crop recommendations yet.

### 16.6 Inventory

Fuel or pump supplies may be handled later.

Do not deduct inventory in this branch.

### 16.7 Future Assets

Pumps, equipment, pipes, and maintenance will later connect to Assets/Maintenance.

Do not implement assets here.

### 16.8 Future Finance

Irrigation duration, water volume, energy, and labour can later support costing.

Do not post costs here.

---

## 17. Seed Data

Add demo irrigation data only in local/testing environments.

Suggested demo data:

Water sources:

```text
Main Borehole
River Intake
Storage Tank A
```

Zones:

```text
Tomato Block B Drip Zone
Maize Field A Furrow Zone
Greenhouse 1 Zone
```

Schedules/events:

```text
Today irrigation schedule for Tomato Block B
Recorded irrigation event for Tomato Block B
Open issue: Low pressure in Tomato Block B
```

Important:

Do not seed demo irrigation data in production.

---

## 18. Tests Required

### Migration/model tests

- Water source can be created.
- Irrigation zone belongs to farm/source/field.
- Schedule belongs to zone/farm.
- Event belongs to zone/farm.
- Reading belongs to source or zone.
- Issue belongs to source or zone.

### Route protection tests

- Guest cannot access `/admin/irrigation`.
- Authenticated user without irrigation permission cannot access `/admin/irrigation`.
- Admin/authorised user can access `/admin/irrigation`.

### CRUD tests

- Authorised user can create water source.
- Authorised user can create irrigation zone.
- Authorised user can create schedule.
- Authorised user can create event.
- Authorised user can create reading.
- Authorised user can create issue.
- Authorised user can resolve issue.

### Validation tests

- Zone rejects field from another farm.
- Zone rejects water source from another farm.
- Schedule rejects zone from another farm.
- Schedule rejects crop cycle from another farm.
- Schedule rejects related task from another farm.
- Schedule rejects worker/team from another farm.
- Schedule rejects user without matching membership.
- Event rejects zone/source/field/task/crop cycle from another farm.
- Reading requires source or zone.
- Issue validates linked source/zone/field/task farm scope.

### Seeder tests

- Irrigation permissions are seeded.
- Demo irrigation data is only seeded in local/testing if included.

---

## 19. Acceptance Criteria

This branch is complete when:

1. Irrigation module exists under `app/Modules/Irrigation`.
2. Routes exist under `/admin/irrigation`.
3. Irrigation routes are protected by auth and permissions.
4. Water sources can be managed without hard deletes.
5. Irrigation zones can be managed without hard deletes.
6. Irrigation schedules can be created, updated, cancelled, and completed.
7. Irrigation events can be recorded.
8. Water readings can be recorded.
9. Irrigation issues can be created and resolved.
10. Irrigation records validate farm scope for sources, zones, fields, crop cycles, tasks, workers, teams, and users.
11. Irrigation permissions are seeded.
12. Navigation appears only for authorised users.
13. Tests pass.
14. README/module documentation is updated.
15. No finance posting, inventory deduction, sensor integration, assets/equipment, weather integration, AI, payroll, or offline logic is implemented.

---

## 20. Recommended Build Order

1. Confirm branch includes Core/Admin, Users/Permissions, Workers/Labour, Tasks, Inventory, Crops, and Livestock.
2. Inspect current permission middleware and seeding structure.
3. Add irrigation permissions to seeder.
4. Create Irrigation module provider/routes.
5. Create migrations for water sources, zones, schedules, events, readings, issues.
6. Create models and relationships.
7. Create controllers.
8. Add validation.
9. Create basic views.
10. Add navigation link with permission check.
11. Add local/testing demo seed data if useful.
12. Add tests.
13. Run validation.
14. Update README.
15. Commit.

---

## 21. Codex Prompt for Irrigation Branch

Use this prompt after confirming the branch is correctly based on Livestock.

```text
You are working on the `irrigation-module` branch of a Laravel 13 modular monolith project for a mixed-farm management platform.

The Laravel foundation, Core/Admin module, Users/Permissions module, Workers/Labour module, Tasks/Work Orders module, Inventory/Inputs module, Crops module, and Livestock module already exist. Do not rebuild them.

This branch is only for the Irrigation / Water Management module foundation.

Do not implement automatic crop-stage irrigation recommendations, sensor/IoT integrations, weather API integrations, pump/equipment asset management, fuel/energy inventory deduction, finance/cost posting, payroll, sales/traceability, satellite imagery, AI water stress prediction, or mobile/offline sync.

First inspect the project and report:
1. Current Core/Admin models for organizations, farms, sites, fields, and warehouses.
2. Current Users/Permissions models, middleware, roles, permissions, and seeding pattern.
3. Current Workers/Labour models for workers and teams.
4. Current Tasks/Work Orders module state.
5. Current Inventory/Inputs module state.
6. Current Crops module state, especially crop cycles.
7. Current Livestock module state.
8. Existing app/Modules/Irrigation folder state.
9. Existing navigation pattern.
10. Existing seeder pattern and local/testing demo data safeguards.
11. Any risks or conflicts before implementation.

Then implement the Irrigation / Water Management module foundation:

1. Module structure:
   - Use app/Modules/Irrigation.
   - Add provider/routes/controllers/models/views/tests/README as needed.

2. Migrations and models:
   - irrigation_water_sources
   - irrigation_zones
   - irrigation_schedules
   - irrigation_events
   - irrigation_water_readings
   - irrigation_issues

3. Water source requirements:
   - Water source belongs to organization and farm.
   - Has name, code, source_type, capacity optional, capacity unit, location fields, status, notes.
   - Code must be unique within farm.
   - Deactivate by status, not hard delete.

4. Irrigation zone requirements:
   - Zone belongs to organization and farm.
   - Optional site, field, and water source.
   - Site, field, and water source must belong to selected farm where provided.
   - Has name, code, zone_type, irrigation_method, area, status, notes.
   - Code must be unique within farm.
   - Deactivate by status, not hard delete.

5. Irrigation schedule requirements:
   - Schedule belongs to organization, farm, and irrigation zone.
   - Optional field, crop cycle, related task, assigned user, worker, and team.
   - Zone/field/crop cycle/task/worker/team must belong to selected farm where provided.
   - Assigned user must have active membership in selected organization and either matching farm_id or null farm_id for organization-level access.
   - Has schedule_number, scheduled date/time, planned duration, planned water volume, priority, status, instructions.
   - Can be cancelled or completed by status.
   - Do not implement recurring schedule engine.

6. Irrigation event requirements:
   - Event belongs to organization, farm, and irrigation zone.
   - Optional water source, field, crop cycle, schedule, related task, performed user, worker, and team.
   - All linked records must belong to selected farm where applicable.
   - Performed user must have active membership in selected organization and either matching farm_id or null farm_id.
   - Captures event number, irrigation date, start/end time, duration, water volume, method, status, notes.
   - Do not post cost or deduct inventory.

7. Water reading requirements:
   - Reading belongs to organization and farm.
   - Must reference water source or irrigation zone.
   - Source/zone must belong to selected farm where provided.
   - Captures reading date, type, value, unit, recorded by, notes.

8. Irrigation issue requirements:
   - Issue belongs to organization and farm.
   - Optional zone, water source, field, related task.
   - Linked records must belong to selected farm where provided.
   - Captures issue number, date, type, severity, status, description, reported by, resolution fields.
   - Can be resolved by status.

9. Permissions:
   - Seed starter permissions:
     irrigation.view, irrigation.manage,
     water-sources.view, water-sources.create, water-sources.update, water-sources.deactivate,
     irrigation-zones.view, irrigation-zones.create, irrigation-zones.update, irrigation-zones.deactivate,
     irrigation-schedules.view, irrigation-schedules.create, irrigation-schedules.update, irrigation-schedules.cancel,
     irrigation-events.view, irrigation-events.create, irrigation-events.update, irrigation-events.cancel,
     water-readings.view, water-readings.create,
     irrigation-issues.view, irrigation-issues.create, irrigation-issues.update, irrigation-issues.resolve.
   - Assign reasonable defaults:
     owner and system-admin get all.
     farm-manager gets irrigation operational management permissions.
     agronomist gets irrigation operational permissions.
     auditor gets view permissions.
   - Do not break existing permissions.

10. Routes:
   - Use /admin/irrigation prefix.
   - Use irrigation.* route names.
   - Protect all irrigation routes behind auth and appropriate permissions.

11. Views:
   - Add simple Irrigation dashboard.
   - Add water source list/create/view/edit/deactivate.
   - Add irrigation zone list/create/view/edit/deactivate.
   - Add schedule list/create/view/edit/cancel/complete.
   - Add event list/create/view/edit/cancel.
   - Add water reading list/create.
   - Add issue list/create/view/edit/resolve.
   - Keep UI simple and consistent with existing Blade layout.

12. Navigation:
   - Add Irrigation / Water admin navigation link only when user has irrigation permissions.
   - Do not rely only on hidden links; enforce backend permission checks.

13. Demo data:
   - If demo sources/zones/schedules/events/readings/issues are seeded, only create them in local/testing environments.
   - Do not seed demo irrigation data in production.

14. Tests:
   - Guest cannot access /admin/irrigation.
   - User without irrigation permission cannot access /admin/irrigation.
   - Admin can access /admin/irrigation.
   - Irrigation permissions are seeded.
   - Water source can be created/updated/deactivated.
   - Zone can be created and rejects field/source from another farm.
   - Schedule can be created/updated/cancelled/completed.
   - Schedule rejects zone, crop cycle, task, worker, team, or user from wrong farm/scope.
   - Event can be recorded and rejects linked records from another farm.
   - Reading requires source or zone.
   - Issue can be created and resolved.

15. Documentation:
   - Update app/Modules/Irrigation/README.md with entities, routes, permissions, business rules, known limitations, and follow-up work.

Constraints:
- Do not implement automatic crop-stage irrigation recommendations.
- Do not implement sensor/IoT integrations.
- Do not implement weather API integrations.
- Do not implement pump/equipment asset management.
- Do not deduct fuel/energy inventory.
- Do not implement finance/cost posting.
- Do not implement payroll.
- Do not implement sales/traceability.
- Do not add unnecessary packages.
- Do not commit .env, vendor, node_modules, local databases, logs, cache files, build output, or generated compiled views.

After implementation, run:
- composer install if needed
- npm install if needed
- php artisan migrate:fresh --seed
- php artisan test
- npm run build
- php artisan route:list --path=admin/irrigation

Then summarize exactly what changed, what tests passed, and any risks/follow-up work.
```

---

## 22. Commit Message

If validation passes, use:

```bash
git add --dry-run .
git add .
git commit -m "Build Irrigation module foundation"
git push origin irrigation-module
```

---

## 23. Follow-Up After This Branch

After this module is complete, the next module should be:

```text
assets-maintenance-module
```

Reason:

Irrigation naturally leads into assets such as pumps, pipes, tanks, tractors, machinery, vehicles, tools, and maintenance schedules. Assets will later connect to tasks, irrigation, inventory spare parts, and finance/costing.

Known future irrigation follow-ups:

- Dynamic farm/source/zone/field/crop-cycle filtering.
- Recurring irrigation schedules.
- Notification/reminder engine.
- Sensor/IoT readings.
- Weather integration.
- Pump/equipment asset linkage.
- Fuel/energy inventory deduction.
- Water and irrigation cost posting to Finance.
- Crop-stage irrigation recommendations.
- Mobile/offline irrigation records.
- Issue-to-maintenance workflow.

