# Crops Module — Developer Specification

## 1. Purpose of This Module

The Crops module manages crop production from planning through planting, field activities, scouting, treatments, irrigation references, harvest, crop losses, and crop-cycle closure.

This module is the crop-side operational heart of the mixed-farm management platform.

The system should help the farm answer questions such as:

- Which crops are currently planted?
- Which fields are under which crop cycle?
- What stage is each crop cycle in?
- When was the crop planted?
- Which activities have been carried out?
- Which fertilisers, chemicals, or treatments were applied?
- What scouting observations were made?
- What crop losses occurred?
- What was harvested?
- What was the yield by field, crop, season, and variety?
- Which crop activities are pending, overdue, or completed?
- Which future reports can show crop history and input usage?

For this branch, the Crops module should create the crop-production foundation. It should not yet implement automatic inventory deduction, finance cost posting, sales, traceability batches, advanced irrigation automation, satellite imagery, AI recommendations, or mobile/offline workflows.

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
- Product categories.
- Products.
- Suppliers.
- Stock lots.
- Inventory movements.

The next logical step is the Crops module because the system now has fields, workers, tasks, and inventory products that crop operations will eventually use.

---

## 3. Branching Note

Before starting this module, make sure the branch includes the Inventory / Inputs work.

### If Inventory / Inputs has been merged into main

```bash
git checkout main
git pull origin main
git checkout -b crops-module
```

### If Inventory / Inputs has not yet been merged

```bash
git checkout inventory-inputs-module
git pull origin inventory-inputs-module
git checkout -b crops-module
```

Do not create this branch from a base that lacks Core/Admin, Users/Permissions, Workers/Labour, Tasks/Work Orders, and Inventory/Inputs.

---

## 4. Module Objective

The objective of this module is to create the crop-production foundation for managing crop masters, varieties, seasons, crop cycles, crop activities, scouting, crop treatments, harvests, and crop losses.

The module should allow authorised users to:

1. Create crop master records.
2. Create crop varieties.
3. Create seasons.
4. Create crop cycles assigned to fields/blocks.
5. Track crop-cycle status from planning to closure.
6. Record crop activities.
7. Record planting/transplanting details.
8. Record scouting observations.
9. Record fertiliser applications.
10. Record spray/treatment applications.
11. Record crop losses.
12. Record harvests and yield.
13. View crop cycle timelines.
14. View crop activity history.
15. Prepare crop records for future inventory deduction, finance costing, sales/traceability, and reporting.

---

## 5. Scope of This Branch

### In scope

This branch should implement:

- Crops module provider/routes.
- Crop master model/table.
- Crop variety model/table.
- Season model/table.
- Crop cycle model/table.
- Crop activity model/table.
- Scouting observation model/table.
- Crop treatment/application model/table.
- Harvest record model/table.
- Crop loss model/table.
- Crop dashboard.
- Crop master list/create/view/edit/deactivate.
- Variety list/create/view/edit/deactivate.
- Season list/create/view/edit/deactivate.
- Crop cycle list/create/view/edit/close.
- Activity creation and timeline.
- Planting activity record.
- Fertiliser activity record.
- Spray/treatment activity record.
- Scouting observation record.
- Harvest record.
- Crop loss record.
- Auth and permission protection.
- Navigation entry.
- Seeder updates for crop permissions.
- Local/testing demo crop data only.
- Tests.
- README update.

### Out of scope

Do not implement:

- Automatic inventory stock deduction.
- Finance cost posting.
- Sales dispatch.
- Traceability batch/lot creation.
- Packhouse workflows.
- Advanced irrigation automation.
- Satellite imagery.
- AI crop recommendations.
- Weather API integrations.
- Pest/disease prediction models.
- Full agronomy recommendation engine.
- Mobile/offline scouting.
- Complex compliance exports.
- Livestock features.

This branch should create crop operational records only.

---

## 6. Key Design Principle

A crop cycle is the central object of crop production.

A field can grow different crops over time. Therefore, crop records should not be attached only to the field. They should be attached to a crop cycle.

Example:

```text
Field: Tomato Block B
Crop Cycle 1: Tomato, Jan-Apr 2026
Crop Cycle 2: Kale, May-Jul 2026
Crop Cycle 3: Onion, Aug-Nov 2026
```

Activities, scouting, treatments, harvests, and losses should belong to the crop cycle, not just the field.

This makes field history and crop performance much easier to track.

---

## 7. Core Entities

## 7.1 Crop

Represents a crop master record.

Examples:

- Maize.
- Tomato.
- Onion.
- Kale.
- Beans.
- Banana.
- Napier grass.
- Lucerne.

### Suggested table name

```text
crop_crops
```

### Suggested fields

```text
id
organization_id nullable
name
code
crop_type
scientific_name nullable
description nullable
default_growing_days nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `crop_type` values

```text
cereal
vegetable
fruit
legume
root_crop
fodder
herb
cash_crop
other
```

### Suggested statuses

```text
active
inactive
archived
```

### Notes

- Some crops may be system-level defaults, but organisation-level crops allow customisation.
- Crop code should be unique within organisation where organisation_id is present.

---

## 7.2 Crop Variety

Represents a variety/cultivar of a crop.

Examples:

- Anna F1 tomato.
- Hybrid 614 maize.
- Red Creole onion.
- Sukuma wiki local variety.

### Suggested table name

```text
crop_varieties
```

### Suggested fields

```text
id
organization_id nullable
crop_id
name
code nullable
description nullable
expected_growing_days nullable
seed_rate nullable
seed_rate_unit nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Relationship

- Variety belongs to crop.
- Crop has many varieties.

---

## 7.3 Season

Represents a production season or period.

Examples:

- 2026 Long Rains.
- 2026 Short Rains.
- Jan-Apr 2026 Greenhouse Cycle.
- Dry Season 2026.

### Suggested table name

```text
crop_seasons
```

### Suggested fields

```text
id
organization_id
farm_id nullable
name
code
start_date nullable
end_date nullable
season_type
status
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `season_type` values

```text
rainy
dry
greenhouse
irrigated
custom
other
```

### Suggested statuses

```text
planned
active
closed
archived
```

---

## 7.4 Crop Cycle

Represents a specific production cycle for a crop in a field/block.

This is the most important crop entity.

### Suggested table name

```text
crop_cycles
```

### Suggested fields

```text
id
organization_id
farm_id
field_id
season_id nullable
crop_id
variety_id nullable
cycle_number
name
planned_start_date nullable
actual_planting_date nullable
expected_harvest_date nullable
actual_end_date nullable
area_planted nullable
area_unit nullable
plant_population nullable
spacing nullable
seed_source nullable
status
manager_user_id nullable
notes nullable
created_by nullable
updated_by nullable
closed_by nullable
closed_at nullable
created_at
updated_at
soft_deletes
```

### Suggested statuses

```text
planned
land_preparation
planted
growing
flowering
fruiting
harvesting
completed
failed
closed
cancelled
```

### Notes

- Crop cycle belongs to field.
- Field must belong to selected farm.
- A field may have multiple crop cycles over time.
- For version 1, overlapping crop cycles in the same field can be allowed or warned, but do not overbuild conflict logic unless simple.

---

## 7.5 Crop Activity

Represents an activity carried out against a crop cycle.

Examples:

- Land preparation.
- Planting.
- Transplanting.
- Fertiliser application.
- Spraying.
- Weeding.
- Scouting.
- Pruning.
- Irrigation note.
- Harvest.
- Crop loss.
- General observation.

### Suggested table name

```text
crop_activities
```

### Suggested fields

```text
id
organization_id
farm_id
crop_cycle_id
field_id
related_task_id nullable
activity_number
activity_type
activity_date
status
performed_by_user_id nullable
performed_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `activity_type` values

```text
land_preparation
planting
transplanting
fertilizer_application
spray_application
scouting
weeding
pruning
irrigation_note
harvest
loss
general_observation
other
```

### Suggested statuses

```text
draft
recorded
approved
cancelled
```

### Notes

- Crop activities are records of what happened.
- They should not yet deduct inventory or post cost in this branch.
- `related_task_id` prepares connection to Tasks module.

---

## 7.6 Planting Detail

Optional specialised detail for planting/transplanting activities.

### Suggested table name

```text
crop_planting_details
```

### Suggested fields

```text
id
crop_activity_id
planting_method
seed_quantity nullable
seed_unit nullable
plant_population nullable
spacing nullable
nursery_source nullable
notes nullable
created_at
updated_at
```

### Suggested `planting_method` values

```text
direct_seed
transplanting
cuttings
seedlings
tubers
other
```

### Important note

Do not automatically deduct seed inventory yet. That will be added later through Inventory integration.

---

## 7.7 Scouting Observation

Represents field observations made during crop inspection.

### Suggested table name

```text
crop_scouting_observations
```

### Suggested fields

```text
id
organization_id
farm_id
crop_cycle_id
field_id
related_activity_id nullable
observation_date
observation_type
severity nullable
affected_area nullable
affected_area_unit nullable
pest_or_disease nullable
symptoms nullable
recommendation nullable
observed_by_user_id nullable
observed_by_worker_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `observation_type` values

```text
pest
disease
nutrient_deficiency
water_stress
weed_pressure
crop_health
growth_stage
weather_damage
other
```

### Suggested severity values

```text
low
medium
high
critical
```

---

## 7.8 Crop Treatment / Application

Represents fertiliser, chemical, spray, or treatment application record.

### Suggested table name

```text
crop_treatment_applications
```

### Suggested fields

```text
id
organization_id
farm_id
crop_cycle_id
field_id
related_activity_id nullable
related_task_id nullable
application_type
application_date
product_id nullable
product_name_snapshot nullable
quantity_used nullable
quantity_unit nullable
application_rate nullable
application_rate_unit nullable
target_problem nullable
method nullable
weather_notes nullable
pre_harvest_interval_days nullable
re_entry_interval_hours nullable
applied_by_user_id nullable
applied_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `application_type` values

```text
fertilizer
chemical_spray
fungicide
insecticide
herbicide
foliar_feed
organic_treatment
soil_amendment
other
```

### Suggested `method` values

```text
broadcast
foliar_spray
drip_fertigation
soil_drench
spot_application
manual
mechanized
other
```

### Important note

`product_id` may reference Inventory products if available, but this branch must not deduct stock automatically.

The product snapshot fields protect history if the product name changes later.

---

## 7.9 Harvest Record

Represents crop output harvested from a crop cycle.

### Suggested table name

```text
crop_harvest_records
```

### Suggested fields

```text
id
organization_id
farm_id
crop_cycle_id
field_id
related_activity_id nullable
related_task_id nullable
harvest_date
quantity
unit_of_measure
grade nullable
destination nullable
harvested_by_user_id nullable
harvested_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested grade examples

```text
grade_a
grade_b
grade_c
rejects
ungraded
```

### Suggested destination examples

```text
store
market
processing
feed
waste
other
```

### Important note

Do not create sales batches or traceability lots yet. That belongs to Sales/Traceability later.

---

## 7.10 Crop Loss Record

Represents crop loss, damage, failure, or destruction.

### Suggested table name

```text
crop_loss_records
```

### Suggested fields

```text
id
organization_id
farm_id
crop_cycle_id
field_id
related_activity_id nullable
loss_date
loss_type
estimated_quantity nullable
quantity_unit nullable
affected_area nullable
affected_area_unit nullable
cause nullable
severity nullable
notes nullable
recorded_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `loss_type` values

```text
pest_damage
disease_damage
drought
flood
hail
fire
theft
animal_damage
poor_germination
crop_failure
other
```

---

## 8. Relationships

### Crop model

Should have:

```php
organization()
varieties()
cropCycles()
```

### CropVariety model

Should have:

```php
organization()
crop()
cropCycles()
```

### CropSeason model

Should have:

```php
organization()
farm()
cropCycles()
```

### CropCycle model

Should have:

```php
organization()
farm()
field()
season()
crop()
variety()
activities()
scoutingObservations()
treatmentApplications()
harvestRecords()
lossRecords()
manager()
createdBy()
closedBy()
```

### CropActivity model

Should have:

```php
organization()
farm()
cropCycle()
field()
relatedTask()
performedByUser()
performedByWorker()
team()
plantingDetail()
scoutingObservation()
treatmentApplication()
harvestRecord()
lossRecord()
```

### ScoutingObservation model

Should have:

```php
organization()
farm()
cropCycle()
field()
relatedActivity()
observedByUser()
observedByWorker()
```

### TreatmentApplication model

Should have:

```php
organization()
farm()
cropCycle()
field()
relatedActivity()
relatedTask()
product()
appliedByUser()
appliedByWorker()
team()
```

### HarvestRecord model

Should have:

```php
organization()
farm()
cropCycle()
field()
relatedActivity()
relatedTask()
harvestedByUser()
harvestedByWorker()
team()
```

### CropLossRecord model

Should have:

```php
organization()
farm()
cropCycle()
field()
relatedActivity()
recordedBy()
```

---

## 9. Permissions

Add starter permissions for the Crops module.

### Suggested permissions

```text
crops.view
crops.manage
crop-master.view
crop-master.create
crop-master.update
crop-master.deactivate
crop-seasons.view
crop-seasons.create
crop-seasons.update
crop-seasons.close
crop-cycles.view
crop-cycles.create
crop-cycles.update
crop-cycles.close
crop-activities.view
crop-activities.create
crop-activities.update
crop-activities.approve
crop-scouting.view
crop-scouting.create
crop-treatments.view
crop-treatments.create
crop-harvests.view
crop-harvests.create
crop-losses.view
crop-losses.create
```

### Suggested default role assignments

Owner:

```text
all crop permissions
```

System Admin:

```text
all crop permissions
```

Farm Manager:

```text
crop operational management permissions
```

Agronomist:

```text
crop view, crop cycles, activities, scouting, treatments, harvest/loss view/create
```

Farm Hand:

```text
crop-activities.view limited later through assigned tasks only
```

Storekeeper:

```text
crop view only, if needed
```

Finance Officer:

```text
crop view only, future costing reports later
```

Auditor:

```text
crop view permissions
```

### Practical starter defaults

- `owner` and `system-admin`: all.
- `farm-manager`: all crop operational permissions.
- `agronomist`: crop master view, seasons view, crop cycles view/create/update, activities view/create/update, scouting create, treatments create, harvest/loss create.
- `livestock-officer`: no crop permissions by default or crop view only, depending on management decision.
- `storekeeper`: crop view only if useful.
- `farm-hand`: no admin crop screens yet; farm-hand task screens come later.
- `auditor`: view-only.

---

## 10. Routes

Suggested route prefix:

```text
/admin/crops
```

Route names:

```text
crops.*
```

### Suggested routes

```text
GET    /admin/crops                              Crops dashboard

GET    /admin/crops/master                       Crop list
GET    /admin/crops/master/create                Create crop form
POST   /admin/crops/master                       Store crop
GET    /admin/crops/master/{crop}                Crop detail
GET    /admin/crops/master/{crop}/edit           Edit crop form
PUT    /admin/crops/master/{crop}                Update crop
POST   /admin/crops/master/{crop}/deactivate     Deactivate crop

GET    /admin/crops/varieties                    Variety list
GET    /admin/crops/varieties/create             Create variety form
POST   /admin/crops/varieties                    Store variety
GET    /admin/crops/varieties/{variety}          Variety detail
GET    /admin/crops/varieties/{variety}/edit     Edit variety form
PUT    /admin/crops/varieties/{variety}          Update variety
POST   /admin/crops/varieties/{variety}/deactivate Deactivate variety

GET    /admin/crops/seasons                      Season list
GET    /admin/crops/seasons/create               Create season form
POST   /admin/crops/seasons                      Store season
GET    /admin/crops/seasons/{season}             Season detail
GET    /admin/crops/seasons/{season}/edit        Edit season form
PUT    /admin/crops/seasons/{season}             Update season
POST   /admin/crops/seasons/{season}/close       Close season

GET    /admin/crops/cycles                       Crop cycle list
GET    /admin/crops/cycles/create                Create crop cycle form
POST   /admin/crops/cycles                       Store crop cycle
GET    /admin/crops/cycles/{cycle}               Crop cycle detail
GET    /admin/crops/cycles/{cycle}/edit          Edit crop cycle form
PUT    /admin/crops/cycles/{cycle}               Update crop cycle
POST   /admin/crops/cycles/{cycle}/close         Close crop cycle
POST   /admin/crops/cycles/{cycle}/cancel        Cancel crop cycle

POST   /admin/crops/cycles/{cycle}/activities    Store crop activity
POST   /admin/crops/cycles/{cycle}/planting      Store planting detail/activity
POST   /admin/crops/cycles/{cycle}/scouting      Store scouting observation
POST   /admin/crops/cycles/{cycle}/treatments    Store treatment/application
POST   /admin/crops/cycles/{cycle}/harvests      Store harvest record
POST   /admin/crops/cycles/{cycle}/losses        Store crop loss record
```

### Middleware

All routes should require:

```text
auth
appropriate crop permission
```

Use the existing permission middleware pattern.

---

## 11. Controllers

Suggested controllers:

```text
CropDashboardController
CropController
CropVarietyController
CropSeasonController
CropCycleController
CropActivityController
CropScoutingController
CropTreatmentController
CropHarvestController
CropLossController
```

Keep controllers readable.

Do not place inventory stock deduction or finance posting here.

---

## 12. Validation Rules

### Crop validation

Required:

- name.
- code.
- crop_type.
- status.

Rules:

- code unique within organisation where applicable.

### Variety validation

Required:

- crop_id.
- name.
- status.

Rules:

- crop must exist.
- crop must belong to same organisation where applicable.

### Season validation

Required:

- organization_id.
- name.
- code.
- season_type.
- status.

Optional:

- farm_id.
- start_date.
- end_date.

Rules:

- farm must belong to selected organisation.
- end_date should not be before start_date.

### Crop cycle validation

Required:

- organization_id.
- farm_id.
- field_id.
- crop_id.
- name.
- status.

Optional:

- season_id.
- variety_id.
- planned_start_date.
- actual_planting_date.
- expected_harvest_date.
- area_planted.
- plant_population.
- manager_user_id.

Rules:

- field must belong to selected farm.
- season must belong to selected organisation/farm where applicable.
- variety must belong to selected crop.
- area_planted must be numeric if provided.

### Crop activity validation

Required:

- crop_cycle_id.
- activity_type.
- activity_date.
- status.

Rules:

- crop cycle must belong to selected organisation/farm.
- field should be derived from crop cycle where possible.
- related task must belong to same organisation/farm if provided.

### Scouting validation

Required:

- observation_date.
- observation_type.

Optional:

- severity.
- pest_or_disease.
- symptoms.
- recommendation.

### Treatment validation

Required:

- application_type.
- application_date.

Optional:

- product_id.
- quantity_used.
- quantity_unit.
- target_problem.
- method.
- pre_harvest_interval_days.
- re_entry_interval_hours.

Rules:

- product_id must belong to selected organisation if provided.
- Do not deduct inventory.

### Harvest validation

Required:

- harvest_date.
- quantity.
- unit_of_measure.

Rules:

- quantity must be greater than zero.

### Loss validation

Required:

- loss_date.
- loss_type.

Optional:

- estimated_quantity.
- affected_area.
- cause.
- severity.

---

## 13. Views / UI

Keep UI simple and operational.

### 13.1 Crops dashboard

Should show links/cards for:

- Crop master.
- Varieties.
- Seasons.
- Crop cycles.
- Activities.
- Scouting.
- Treatments.
- Harvests.
- Losses.

Optional simple metrics:

- Active crop cycles.
- Crop cycles by status.
- Expected harvests.
- Recent activities.

Do not build advanced analytics yet.

### 13.2 Crop master list

Columns:

- Code.
- Name.
- Crop type.
- Default growing days.
- Status.
- Actions.

### 13.3 Variety list

Columns:

- Crop.
- Variety name.
- Expected growing days.
- Status.
- Actions.

### 13.4 Season list

Columns:

- Code.
- Name.
- Farm.
- Season type.
- Start date.
- End date.
- Status.
- Actions.

### 13.5 Crop cycle list

Columns:

- Cycle number.
- Name.
- Crop.
- Variety.
- Farm.
- Field.
- Season.
- Status.
- Expected harvest.
- Actions.

### 13.6 Crop cycle detail

Sections:

- Summary.
- Field and season.
- Activity timeline.
- Scouting observations.
- Treatments/applications.
- Harvest records.
- Loss records.
- Notes.

### 13.7 Crop cycle create/edit form

Fields:

- Organisation.
- Farm.
- Field.
- Season.
- Crop.
- Variety.
- Name.
- Planned start date.
- Actual planting date.
- Expected harvest date.
- Area planted.
- Plant population.
- Spacing.
- Seed source.
- Status.
- Manager.
- Notes.

### 13.8 Activity form

Fields:

- Activity type.
- Activity date.
- Related task optional.
- Performed by user/worker/team optional.
- Notes.

### 13.9 Scouting form

Fields:

- Observation date.
- Observation type.
- Severity.
- Pest/disease.
- Symptoms.
- Recommendation.
- Notes.

### 13.10 Treatment/application form

Fields:

- Application type.
- Application date.
- Product optional.
- Product name snapshot.
- Quantity used.
- Unit.
- Application rate.
- Target problem.
- Method.
- Weather notes.
- PHI days.
- REI hours.
- Applied by.
- Notes.

### 13.11 Harvest form

Fields:

- Harvest date.
- Quantity.
- Unit.
- Grade.
- Destination.
- Harvested by.
- Notes.

### 13.12 Loss form

Fields:

- Loss date.
- Loss type.
- Estimated quantity.
- Affected area.
- Cause.
- Severity.
- Notes.

---

## 14. Navigation

Add Crops to the admin/module navigation if the user has relevant permissions.

Suggested label:

```text
Crops
```

Suggested left menu inside module:

```text
Crops Dashboard
Crop Master
Varieties
Seasons
Crop Cycles
Activities
Scouting
Treatments
Harvests
Losses
```

For this branch, a simple navigation link is enough.

Do not show crop links to users without crop permissions.

Backend routes must still enforce permissions.

---

## 15. Business Rules

### Rule 1: Crop cycles are central

Activities, treatments, scouting, harvests, and losses should belong to crop cycles.

### Rule 2: Crop cycles must belong to fields

A crop cycle must be attached to a field/block from Core/Admin.

### Rule 3: Field must belong to farm

Do not allow creating a crop cycle on a field from another farm.

### Rule 4: Variety must belong to crop

Do not allow selecting a tomato variety for a maize crop.

### Rule 5: Deactivate crop master records instead of deleting

Future crop cycles may reference them.

### Rule 6: Closing a crop cycle should prevent normal editing

Closed crop cycles should not receive new activities unless reopened by an authorised role later.

### Rule 7: Do not deduct inventory yet

Recording a fertiliser or spray application should not reduce stock in this branch.

### Rule 8: Do not post finance costs yet

Crop records may capture quantities and product references, but costing comes later.

### Rule 9: Do not create sales batches yet

Harvest records should not create sales/traceability batches in this branch.

### Rule 10: Demo crop data should be local/testing only

Do not seed demo crop cycles into production.

---

## 16. Integration with Other Modules

### 16.1 Core/Admin

Crops depend on:

- organisations,
- farms,
- fields,
- sites where useful.

### 16.2 Users/Permissions

Crop access depends on permissions and membership scope.

### 16.3 Workers/Labour

Crop activities may optionally reference workers and teams.

### 16.4 Tasks / Work Orders

Crop activities may optionally reference related tasks.

For now, task references are informational.

Do not auto-complete tasks or create tasks from crop activities yet.

### 16.5 Inventory / Inputs

Crop treatments may optionally reference inventory products.

Do not deduct stock yet.

### 16.6 Future Finance

Crop activities and input use will later support crop-cycle costing.

Do not post costs yet.

### 16.7 Future Sales / Traceability

Harvests will later create batches/lots for sales and traceability.

Do not create batches yet.

---

## 17. Seed Data

Add demo crop data only in local/testing environments.

Suggested demo data:

Crops:

```text
Tomato
Maize
Kale
Onion
Napier Grass
```

Varieties:

```text
Anna F1 Tomato
Hybrid Maize 614
Local Kale
Red Creole Onion
```

Season:

```text
2026 Long Rains
```

Crop cycle:

```text
Tomato Block B — Jan-Apr 2026
```

Activities:

```text
Planting activity
Scouting observation
Fertiliser application record
Harvest record
```

Important:

Do not seed demo crop data in production.

---

## 18. Tests Required

### Migration/model tests

- Crop can be created.
- Variety belongs to crop.
- Season belongs to organisation/farm.
- Crop cycle belongs to organisation/farm/field/crop.
- Crop activity belongs to crop cycle.
- Scouting observation belongs to crop cycle.
- Treatment belongs to crop cycle.
- Harvest belongs to crop cycle.
- Loss belongs to crop cycle.

### Route protection tests

- Guest cannot access `/admin/crops`.
- Authenticated user without crop permission cannot access `/admin/crops`.
- Admin/authorised user can access `/admin/crops`.

### CRUD tests

- Authorised user can create crop.
- Authorised user can create variety.
- Authorised user can create season.
- Authorised user can create crop cycle.
- Authorised user can update/close crop cycle.
- Authorised user can create crop activity.
- Authorised user can create scouting observation.
- Authorised user can create treatment/application.
- Authorised user can create harvest record.
- Authorised user can create loss record.

### Validation tests

- Crop cycle requires organisation/farm/field/crop.
- Field must belong to selected farm.
- Variety must belong to selected crop.
- Related task must belong to same farm where provided.
- Inventory product reference must belong to same organisation where provided.
- Harvest quantity must be greater than zero.

### Seeder tests

- Crop permissions are seeded.
- Demo crop data is only seeded in local/testing if included.

---

## 19. Acceptance Criteria

This branch is complete when:

1. Crops module exists under `app/Modules/Crops`.
2. Routes exist under `/admin/crops`.
3. Crop routes are protected by auth and permissions.
4. Crop master records can be managed without hard deletes.
5. Crop varieties can be managed.
6. Seasons can be managed.
7. Crop cycles can be created, updated, and closed.
8. Crop cycles are linked to fields from Core/Admin.
9. Crop activities can be recorded.
10. Scouting observations can be recorded.
11. Treatment/application records can reference inventory products without deducting stock.
12. Harvest records can be recorded without creating sales batches.
13. Crop loss records can be recorded.
14. Crop permissions are seeded.
15. Navigation appears only for authorised users.
16. Tests pass.
17. README/module documentation is updated.
18. No inventory stock deduction, finance posting, sales traceability, livestock, payroll, advanced irrigation, satellite, AI, or offline logic is implemented.

---

## 20. Recommended Build Order

1. Confirm branch includes Core/Admin, Users/Permissions, Workers/Labour, Tasks, and Inventory.
2. Inspect current permission middleware and seeding structure.
3. Add crop permissions to seeder.
4. Create Crops module provider/routes.
5. Create migrations for crops, varieties, seasons, crop cycles, activities, planting details, scouting, treatments, harvests, losses.
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

## 21. Codex Prompt for Crops Branch

Use this prompt after confirming the branch is correctly based on Inventory / Inputs.

```text
You are working on the `crops-module` branch of a Laravel 13 modular monolith project for a mixed-farm management platform.

The Laravel foundation, Core/Admin module, Users/Permissions module, Workers/Labour module, Tasks/Work Orders module, and Inventory/Inputs module already exist. Do not rebuild them.

This branch is only for the Crops module foundation.

Do not implement automatic inventory stock deduction, finance/cost posting, sales/traceability batches, livestock features, payroll, advanced irrigation automation, satellite imagery, AI recommendations, weather API integrations, or mobile/offline sync.

First inspect the project and report:
1. Current Core/Admin models for organizations, farms, fields, and sites.
2. Current Users/Permissions models, middleware, roles, permissions, and seeding pattern.
3. Current Workers/Labour models for workers and teams.
4. Current Tasks/Work Orders module state.
5. Current Inventory/Inputs product models and stock movement state.
6. Existing app/Modules/Crops folder state.
7. Existing navigation pattern.
8. Existing seeder pattern and local/testing demo data safeguards.
9. Any risks or conflicts before implementation.

Then implement the Crops module foundation:

1. Module structure:
   - Use app/Modules/Crops.
   - Add provider/routes/controllers/models/views/tests/README as needed.

2. Migrations and models:
   - crop_crops
   - crop_varieties
   - crop_seasons
   - crop_cycles
   - crop_activities
   - crop_planting_details
   - crop_scouting_observations
   - crop_treatment_applications
   - crop_harvest_records
   - crop_loss_records

3. Crop master requirements:
   - Crop can be system-level or organization-level.
   - Has name, code, crop_type, scientific_name optional, default growing days, status.
   - Deactivate by status, not hard delete.

4. Variety requirements:
   - Variety belongs to crop.
   - Has name, code optional, expected growing days, seed rate fields, status.
   - Variety must belong to selected crop.
   - Deactivate by status, not hard delete.

5. Season requirements:
   - Season belongs to organization and optionally farm.
   - Has name, code, start/end dates, season_type, status.
   - Can be closed by status.

6. Crop cycle requirements:
   - Crop cycle belongs to organization, farm, field, crop, optional variety, optional season.
   - Field must belong to selected farm.
   - Variety must belong to selected crop.
   - Has cycle_number, name, planned_start_date, actual_planting_date, expected_harvest_date, area planted, plant population, spacing, seed source, manager, status, closure fields.
   - Crop cycle should be closed/cancelled by status, not hard deleted.

7. Crop activity requirements:
   - Activity belongs to organization, farm, crop cycle, and field.
   - Field should be derived from crop cycle where practical.
   - Activity can optionally reference related task, performed user, worker, or team.
   - Related task must belong to same organization/farm if provided.
   - Activity types include land_preparation, planting, transplanting, fertilizer_application, spray_application, scouting, weeding, pruning, irrigation_note, harvest, loss, general_observation, other.
   - Do not deduct inventory or post finance cost.

8. Planting detail requirements:
   - Planting detail belongs to crop activity.
   - Captures planting method, seed quantity/unit, plant population, spacing, nursery source, notes.
   - Do not deduct seed inventory.

9. Scouting requirements:
   - Scouting observation belongs to organization, farm, crop cycle, field, optional related activity.
   - Captures observation date/type, severity, affected area, pest/disease, symptoms, recommendation, observed by, notes.

10. Treatment/application requirements:
   - Treatment belongs to organization, farm, crop cycle, field, optional related activity/task.
   - Can optionally reference inventory product.
   - Product must belong to selected organization if provided.
   - Capture application type/date, product snapshot, quantity used/unit, application rate, target problem, method, weather notes, PHI days, REI hours, applied by, notes.
   - Do not deduct stock.

11. Harvest requirements:
   - Harvest belongs to organization, farm, crop cycle, field, optional related activity/task.
   - Captures harvest date, quantity, unit, grade, destination, harvested by, notes.
   - Quantity must be greater than zero.
   - Do not create sales/traceability batches.

12. Loss requirements:
   - Loss belongs to organization, farm, crop cycle, field, optional related activity.
   - Captures loss date, loss type, estimated quantity/unit, affected area/unit, cause, severity, notes.

13. Permissions:
   - Seed starter permissions:
     crops.view, crops.manage,
     crop-master.view, crop-master.create, crop-master.update, crop-master.deactivate,
     crop-seasons.view, crop-seasons.create, crop-seasons.update, crop-seasons.close,
     crop-cycles.view, crop-cycles.create, crop-cycles.update, crop-cycles.close,
     crop-activities.view, crop-activities.create, crop-activities.update, crop-activities.approve,
     crop-scouting.view, crop-scouting.create,
     crop-treatments.view, crop-treatments.create,
     crop-harvests.view, crop-harvests.create,
     crop-losses.view, crop-losses.create.
   - Assign reasonable defaults:
     owner and system-admin get all.
     farm-manager gets crop operational management permissions.
     agronomist gets crop cycle/activity/scouting/treatment/harvest/loss starter permissions.
     auditor gets view permissions.
   - Do not break existing permissions.

14. Routes:
   - Use /admin/crops prefix.
   - Use crops.* route names.
   - Protect all crop routes behind auth and appropriate permissions.

15. Views:
   - Add simple Crops dashboard.
   - Add crop master list/create/view/edit/deactivate.
   - Add variety list/create/view/edit/deactivate.
   - Add season list/create/view/edit/close.
   - Add crop cycle list/create/view/edit/close/cancel.
   - Add crop cycle detail page with activity timeline, scouting, treatments, harvests, and losses.
   - Add simple forms/actions for activity, planting, scouting, treatment, harvest, and loss records.
   - Keep UI simple and consistent with existing Blade layout.

16. Navigation:
   - Add Crops admin navigation link only when user has crop permissions.
   - Do not rely only on hidden links; enforce backend permission checks.

17. Demo data:
   - If demo crops/seasons/cycles/activities are seeded, only create them in local/testing environments.
   - Do not seed demo crop data in production.

18. Tests:
   - Guest cannot access /admin/crops.
   - User without crop permission cannot access /admin/crops.
   - Admin can access /admin/crops.
   - Crop permissions are seeded.
   - Crop can be created/updated/deactivated.
   - Variety can be created and must belong to crop.
   - Season can be created/closed.
   - Crop cycle can be created, updated, closed/cancelled.
   - Crop cycle rejects a field from another farm.
   - Crop cycle rejects a variety from another crop.
   - Activity can be recorded for crop cycle.
   - Activity rejects related task from another farm.
   - Treatment can reference an inventory product from same organization.
   - Treatment rejects product from another organization.
   - Harvest quantity must be greater than zero.
   - Harvest and loss can be recorded.

19. Documentation:
   - Update app/Modules/Crops/README.md with entities, routes, permissions, business rules, known limitations, and follow-up work.

Constraints:
- Do not implement automatic inventory stock deduction.
- Do not implement finance/cost posting.
- Do not implement sales/traceability batch creation.
- Do not implement livestock features.
- Do not implement payroll.
- Do not implement advanced irrigation automation.
- Do not implement satellite/weather/AI integrations.
- Do not add unnecessary packages.
- Do not commit .env, vendor, node_modules, local databases, logs, cache files, build output, or generated compiled views.

After implementation, run:
- composer install if needed
- npm install if needed
- php artisan migrate:fresh --seed
- php artisan test
- npm run build
- php artisan route:list --path=admin/crops

Then summarize exactly what changed, what tests passed, and any risks/follow-up work.
```

---

## 22. Commit Message

If validation passes, use:

```bash
git add --dry-run .
git add .
git commit -m "Build Crops module foundation"
git push origin crops-module
```

---

## 23. Follow-Up After This Branch

After this module is complete, the next module should be:

```text
livestock-module
```

Reason:

The platform is for mixed farms, so after crop operations are established, the animal-side production module should be built next.

Known future crop follow-ups:

- Dynamic farm/field/crop/variety filtering.
- Automatic inventory deduction for seed, fertiliser, and chemicals.
- Crop activity approval workflow refinement.
- Task-to-crop activity conversion.
- Crop costing integration with Finance.
- Harvest-to-sales/traceability batch creation.
- Crop reports and dashboards.
- Scouting photos/attachments.
- Weather and irrigation integration.
- Mobile/offline scouting.
- Compliance reports for spray records, PHI, REI, and input use.

