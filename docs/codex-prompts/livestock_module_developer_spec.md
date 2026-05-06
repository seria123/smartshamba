# Livestock Module — Developer Specification

## 1. Purpose of This Module

The Livestock module manages animal production, animal health, reproduction, feeding records, movement, mortality, and animal yields.

This module is the animal-side operational heart of the mixed-farm management platform.

The system should help the farm answer questions such as:

- Which animals or animal groups are currently on the farm?
- Where are the animals located?
- What species, breed, sex, and status are they?
- Which animals are pregnant, lactating, sick, under treatment, sold, dead, or active?
- Which treatments or vaccinations have been given?
- Which medicines were used?
- Are any animals or products under withdrawal?
- Which animals gave birth?
- What is the breeding and reproduction history?
- Which animals moved from one paddock/pen to another?
- What feed was given?
- What yields were recorded, such as milk, eggs, weight gain, or other outputs?
- Which animals died and why?
- What is the animal history by individual animal, group, paddock, species, or farm?

For this branch, the Livestock module should create the livestock operational foundation only.

It should not yet implement automatic inventory deduction, finance cost posting, sales, animal sales/disposals, payroll, advanced veterinary workflows, IoT/RFID integrations, or mobile/offline workflows.

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
- Crops module.
- Crop masters, varieties, seasons, crop cycles, crop activities, treatments, scouting, harvests, and losses.

The next logical step is the Livestock module because the platform is for mixed farms, meaning it needs both crop-side and animal-side production records.

---

## 3. Branching Note

Before starting this module, make sure the branch includes the Crops work.

### If Crops has been merged into main

```bash
git checkout main
git pull origin main
git checkout -b livestock-module
```

### If Crops has not yet been merged

```bash
git checkout crops-module
git pull origin crops-module
git checkout -b livestock-module
```

Do not create this branch from a base that lacks Core/Admin, Users/Permissions, Workers/Labour, Tasks/Work Orders, Inventory/Inputs, and Crops.

---

## 4. Module Objective

The objective of this module is to create the livestock-production foundation for managing animal species, breeds, individual animals, animal groups, animal events, health records, reproduction records, feed records, movement records, mortality records, yield records, and withdrawal periods.

The module should allow authorised users to:

1. Create species records.
2. Create breed records.
3. Register individual animals.
4. Register animal groups.
5. Assign animals/groups to farms and paddocks/pens.
6. Record animal health events.
7. Record treatments and vaccinations.
8. Record breeding events.
9. Record pregnancy checks.
10. Record births.
11. Record weights.
12. Record feed events.
13. Record animal movements.
14. Record mortality.
15. Record animal yields.
16. Record withdrawal periods after relevant treatment.
17. View animal or group history.
18. Prepare livestock records for future inventory deduction, finance costing, sales, traceability, and reporting.

---

## 5. Scope of This Branch

### In scope

This branch should implement:

- Livestock module provider/routes.
- Species model/table.
- Breed model/table.
- Individual animal model/table.
- Animal group model/table.
- Animal event model/table.
- Treatment/vaccination model/table.
- Breeding record model/table.
- Pregnancy check model/table.
- Birth record model/table.
- Weight record model/table.
- Feed record model/table.
- Movement record model/table.
- Mortality record model/table.
- Yield record model/table.
- Withdrawal period model/table.
- Livestock dashboard.
- Species list/create/view/edit/deactivate.
- Breed list/create/view/edit/deactivate.
- Animal list/create/view/edit/deactivate/status update.
- Animal group list/create/view/edit/deactivate/status update.
- Animal detail/profile view.
- Animal group detail/profile view.
- Record treatment/vaccination.
- Record breeding.
- Record pregnancy check.
- Record birth.
- Record weight.
- Record feed.
- Record movement.
- Record mortality.
- Record yield.
- Record withdrawal period where relevant.
- Auth and permission protection.
- Navigation entry.
- Seeder updates for livestock permissions.
- Local/testing demo livestock data only.
- Tests.
- README update.

### Out of scope

Do not implement:

- Automatic medicine/feed inventory deduction.
- Finance cost posting.
- Sales/disposal accounting.
- Animal sale workflows.
- Full veterinary diagnosis engine.
- Full herd book analytics.
- RFID scanning.
- IoT integrations.
- Milk collection payment workflows.
- Egg collection sales workflows.
- Payroll.
- Advanced mobile/offline animal records.
- Livestock insurance.
- Complex regulatory/compliance exports.
- AI animal health recommendations.

This branch should create livestock operational records only.

---

## 6. Key Design Principle

The module must support both individual animal tracking and group animal tracking.

Some farm animals need individual records:

- Dairy cows.
- Bulls.
- Breeding goats/sheep.
- High-value animals.
- Tagged animals.

Some animals are better managed as groups:

- Poultry flock.
- Fish pond batch.
- Broiler batch.
- Layers flock.
- Goat group where individual tracking is unnecessary.
- Sheep flock.

Therefore, the system should support:

```text
Individual animal records
Animal group records
```

Many livestock events should be able to apply to either an individual animal or an animal group.

Do not force every livestock operation into individual-animal tracking.

---

## 7. Core Entities

## 7.1 Species

Represents a livestock species.

Examples:

- Cattle.
- Goat.
- Sheep.
- Poultry.
- Pig.
- Fish.
- Rabbit.
- Bee.

### Suggested table name

```text
livestock_species
```

### Suggested fields

```text
id
organization_id nullable
name
code
species_type
description nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `species_type` values

```text
mammal
bird
fish
insect
other
```

### Suggested statuses

```text
active
inactive
archived
```

### Notes

- Some species may be system-level defaults.
- Organisation-level species allow customisation.

---

## 7.2 Breed

Represents a breed under a species.

Examples:

- Friesian.
- Ayrshire.
- Sahiwal.
- Boer goat.
- Dorper sheep.
- Kienyeji chicken.
- Broiler.
- Layer.

### Suggested table name

```text
livestock_breeds
```

### Suggested fields

```text
id
organization_id nullable
species_id
name
code nullable
description nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Relationship

- Breed belongs to species.
- Species has many breeds.

---

## 7.3 Animal

Represents an individually tracked animal.

### Suggested table name

```text
livestock_animals
```

### Suggested fields

```text
id
organization_id
farm_id
paddock_id nullable
species_id
breed_id nullable
animal_code
tag_number nullable
rfid_number nullable
name nullable
sex
date_of_birth nullable
source
status
health_status nullable
production_status nullable
dam_id nullable
sire_id nullable
current_weight nullable
weight_unit nullable
acquisition_date nullable
acquisition_cost nullable
currency nullable
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `sex` values

```text
male
female
unknown
```

### Suggested `source` values

```text
born_on_farm
purchased
transferred_in
donated
other
```

### Suggested `status` values

```text
active
sick
under_treatment
in_withdrawal
pregnant
lactating
sold
transferred_out
dead
culled
inactive
archived
```

### Suggested `health_status` values

```text
normal
watch
sick
critical
recovering
unknown
```

### Suggested `production_status` values

```text
growing
breeding
pregnant
lactating
dry
laying
finishing
retired
unknown
```

### Notes

- Animal code should be unique within farm.
- Tag number should be unique within farm where provided.
- Paddock must belong to selected farm where provided.
- Dam and sire should belong to the same organisation/farm where provided.

---

## 7.4 Animal Group

Represents animals tracked as a group.

Examples:

- Broiler Batch 001.
- Layers Flock A.
- Goat Group 2.
- Fish Pond Batch A.
- Calf Group 2026.

### Suggested table name

```text
livestock_animal_groups
```

### Suggested fields

```text
id
organization_id
farm_id
paddock_id nullable
species_id
breed_id nullable
group_code
name
group_type
start_date nullable
initial_count
current_count
sex_composition nullable
source
status
health_status nullable
production_status nullable
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `group_type` values

```text
flock
herd
batch
pond
pen_group
age_group
other
```

### Suggested statuses

```text
active
under_treatment
in_withdrawal
sold
transferred_out
closed
inactive
archived
```

### Notes

- Current count should be updated through events such as births, mortality, additions, sales, or closures later.
- For this branch, keep count logic simple and explicit.

---

## 7.5 Animal Event

A general livestock timeline record.

This table can record general events and support history views.

### Suggested table name

```text
livestock_events
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
paddock_id nullable
related_task_id nullable
event_number
event_type
event_date
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

### Suggested `event_type` values

```text
health_observation
treatment
vaccination
breeding
pregnancy_check
birth
weight
feed
movement
mortality
yield
general_observation
other
```

### Important rule

An event should apply to either an animal or an animal group.

At least one of `animal_id` or `animal_group_id` should be present.

---

## 7.6 Treatment / Vaccination Record

Represents health treatment, medication, vaccination, deworming, or other animal health action.

### Suggested table name

```text
livestock_treatment_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
related_event_id nullable
related_task_id nullable
treatment_type
treatment_date
condition_or_reason nullable
diagnosis nullable
product_id nullable
product_name_snapshot nullable
dosage nullable
dosage_unit nullable
route nullable
frequency nullable
duration_days nullable
withdrawal_meat_days nullable
withdrawal_milk_days nullable
withdrawal_egg_days nullable
treated_by_user_id nullable
treated_by_worker_id nullable
team_id nullable
follow_up_date nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `treatment_type` values

```text
treatment
vaccination
deworming
preventive
first_aid
surgery
other
```

### Suggested `route` values

```text
oral
injection
intramuscular
subcutaneous
topical
spray
water
feed
other
```

### Important notes

- `product_id` may reference Inventory products.
- Do not deduct medicine inventory in this branch.
- Withdrawal period records may be created where withdrawal days are provided.

---

## 7.7 Withdrawal Period

Represents a food-safety restriction period after treatment.

### Suggested table name

```text
livestock_withdrawal_periods
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
treatment_record_id
withdrawal_type
starts_on
ends_on
status
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `withdrawal_type` values

```text
meat
milk
eggs
other
```

### Suggested statuses

```text
active
completed
cancelled
```

### Important note

For this branch, withdrawal periods can be recorded and displayed. Do not implement automatic sale/dispatch blocking yet.

---

## 7.8 Breeding Record

Represents mating, insemination, or breeding event.

### Suggested table name

```text
livestock_breeding_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
related_event_id nullable
breeding_date
breeding_method
male_animal_id nullable
sire_name_snapshot nullable
expected_due_date nullable
status
performed_by_user_id nullable
performed_by_worker_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `breeding_method` values

```text
natural
artificial_insemination
controlled_mating
unknown
other
```

### Suggested statuses

```text
recorded
confirmed_pregnant
failed
cancelled
```

---

## 7.9 Pregnancy Check

Represents pregnancy diagnosis/check.

### Suggested table name

```text
livestock_pregnancy_checks
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
related_breeding_record_id nullable
check_date
result
expected_due_date nullable
checked_by_user_id nullable
checked_by_worker_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `result` values

```text
pregnant
not_pregnant
uncertain
recheck_needed
```

---

## 7.10 Birth Record

Represents animal birth event.

### Suggested table name

```text
livestock_birth_records
```

### Suggested fields

```text
id
organization_id
farm_id
mother_animal_id nullable
animal_group_id nullable
related_event_id nullable
birth_date
number_born
number_alive
number_dead
birth_type nullable
notes nullable
recorded_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `birth_type` values

```text
normal
assisted
complicated
unknown
```

### Important note

For individual animal births, the system may optionally create new animal records, but do not overbuild if not simple.

If automatic offspring creation is implemented, it must be explicit and tested.

---

## 7.11 Weight Record

Represents weight measurement.

### Suggested table name

```text
livestock_weight_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
weigh_date
weight
weight_unit
measurement_method nullable
recorded_by nullable
notes nullable
created_at
updated_at
soft_deletes
```

---

## 7.12 Feed Record

Represents feed given to an animal or animal group.

### Suggested table name

```text
livestock_feed_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
related_task_id nullable
feed_date
product_id nullable
feed_name_snapshot nullable
quantity
quantity_unit
feeding_method nullable
fed_by_user_id nullable
fed_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Important note

`product_id` may reference Inventory products, but do not deduct feed stock in this branch.

---

## 7.13 Movement Record

Represents animal/group movement between paddocks/pens/farms.

### Suggested table name

```text
livestock_movement_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
from_paddock_id nullable
to_paddock_id nullable
movement_date
movement_reason nullable
moved_by_user_id nullable
moved_by_worker_id nullable
team_id nullable
notes nullable
created_by nullable
created_at
updated_at
soft_deletes
```

### Important rules

- Paddocks must belong to the selected farm.
- Recording movement should update the current paddock of the animal or group if implemented.
- Keep movement logic simple and tested.

---

## 7.14 Mortality Record

Represents death or mortality event.

### Suggested table name

```text
livestock_mortality_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
mortality_date
number_dead nullable
cause nullable
suspected_reason nullable
disposal_method nullable
reported_by nullable
notes nullable
created_at
updated_at
soft_deletes
```

### Important notes

- For individual animal mortality, update animal status to `dead` where implemented.
- For group mortality, reduce current_count where implemented.
- Do not implement finance loss posting yet.

---

## 7.15 Yield Record

Represents animal production output.

Examples:

- Milk.
- Eggs.
- Weight gain.
- Honey.
- Fish harvest.
- Wool.

### Suggested table name

```text
livestock_yield_records
```

### Suggested fields

```text
id
organization_id
farm_id
animal_id nullable
animal_group_id nullable
yield_date
yield_type
quantity
unit_of_measure
grade nullable
destination nullable
recorded_by nullable
notes nullable
created_at
updated_at
soft_deletes
```

### Suggested `yield_type` values

```text
milk
eggs
weight_gain
honey
fish
wool
manure
other
```

### Important note

Do not create sales records or traceability batches yet.

---

## 8. Relationships

### Species model

Should have:

```php
organization()
breeds()
animals()
animalGroups()
```

### Breed model

Should have:

```php
organization()
species()
animals()
animalGroups()
```

### Animal model

Should have:

```php
organization()
farm()
paddock()
species()
breed()
dam()
sire()
events()
treatments()
withdrawalPeriods()
breedingRecords()
pregnancyChecks()
birthRecordsAsMother()
weightRecords()
feedRecords()
movementRecords()
mortalityRecords()
yieldRecords()
createdBy()
```

### AnimalGroup model

Should have:

```php
organization()
farm()
paddock()
species()
breed()
events()
treatments()
withdrawalPeriods()
breedingRecords()
pregnancyChecks()
birthRecords()
weightRecords()
feedRecords()
movementRecords()
mortalityRecords()
yieldRecords()
createdBy()
```

### TreatmentRecord model

Should have:

```php
organization()
farm()
animal()
animalGroup()
relatedEvent()
relatedTask()
product()
treatedByUser()
treatedByWorker()
team()
withdrawalPeriods()
```

### Other record models

Each record should belong to:

```php
organization()
farm()
animal() nullable
animalGroup() nullable
```

where applicable.

---

## 9. Permissions

Add starter permissions for the Livestock module.

### Suggested permissions

```text
livestock.view
livestock.manage
livestock-species.view
livestock-species.create
livestock-species.update
livestock-species.deactivate
livestock-animals.view
livestock-animals.create
livestock-animals.update
livestock-animals.deactivate
livestock-groups.view
livestock-groups.create
livestock-groups.update
livestock-groups.deactivate
livestock-health.view
livestock-health.create
livestock-breeding.view
livestock-breeding.create
livestock-births.view
livestock-births.create
livestock-feed.view
livestock-feed.create
livestock-movements.view
livestock-movements.create
livestock-mortality.view
livestock-mortality.create
livestock-yields.view
livestock-yields.create
livestock-withdrawals.view
```

### Suggested default role assignments

Owner:

```text
all livestock permissions
```

System Admin:

```text
all livestock permissions
```

Farm Manager:

```text
livestock operational management permissions
```

Livestock Officer:

```text
livestock animals/groups, health, breeding, births, feed, movements, mortality, yields, withdrawals
```

Farm Hand:

```text
no admin livestock screens yet; future task-only access
```

Storekeeper:

```text
livestock view only if useful
```

Finance Officer:

```text
livestock view only; future costing reports later
```

Auditor:

```text
livestock view permissions
```

---

## 10. Routes

Suggested route prefix:

```text
/admin/livestock
```

Route names:

```text
livestock.*
```

### Suggested routes

```text
GET    /admin/livestock                                Livestock dashboard

GET    /admin/livestock/species                        Species list
GET    /admin/livestock/species/create                 Create species form
POST   /admin/livestock/species                        Store species
GET    /admin/livestock/species/{species}              Species detail
GET    /admin/livestock/species/{species}/edit         Edit species form
PUT    /admin/livestock/species/{species}              Update species
POST   /admin/livestock/species/{species}/deactivate   Deactivate species

GET    /admin/livestock/breeds                         Breed list
GET    /admin/livestock/breeds/create                  Create breed form
POST   /admin/livestock/breeds                         Store breed
GET    /admin/livestock/breeds/{breed}                 Breed detail
GET    /admin/livestock/breeds/{breed}/edit            Edit breed form
PUT    /admin/livestock/breeds/{breed}                 Update breed
POST   /admin/livestock/breeds/{breed}/deactivate      Deactivate breed

GET    /admin/livestock/animals                        Animal list
GET    /admin/livestock/animals/create                 Create animal form
POST   /admin/livestock/animals                        Store animal
GET    /admin/livestock/animals/{animal}               Animal profile
GET    /admin/livestock/animals/{animal}/edit          Edit animal form
PUT    /admin/livestock/animals/{animal}               Update animal
POST   /admin/livestock/animals/{animal}/deactivate    Deactivate animal

GET    /admin/livestock/groups                         Animal group list
GET    /admin/livestock/groups/create                  Create group form
POST   /admin/livestock/groups                         Store group
GET    /admin/livestock/groups/{group}                 Group profile
GET    /admin/livestock/groups/{group}/edit            Edit group form
PUT    /admin/livestock/groups/{group}                 Update group
POST   /admin/livestock/groups/{group}/deactivate      Deactivate group

POST   /admin/livestock/animals/{animal}/treatments    Store animal treatment
POST   /admin/livestock/groups/{group}/treatments      Store group treatment

POST   /admin/livestock/animals/{animal}/breeding      Store breeding record
POST   /admin/livestock/animals/{animal}/pregnancy-checks Store pregnancy check
POST   /admin/livestock/animals/{animal}/births        Store birth record

POST   /admin/livestock/animals/{animal}/weights       Store animal weight
POST   /admin/livestock/groups/{group}/weights         Store group weight

POST   /admin/livestock/animals/{animal}/feed          Store animal feed record
POST   /admin/livestock/groups/{group}/feed            Store group feed record

POST   /admin/livestock/animals/{animal}/movements     Store animal movement
POST   /admin/livestock/groups/{group}/movements       Store group movement

POST   /admin/livestock/animals/{animal}/mortality     Store animal mortality
POST   /admin/livestock/groups/{group}/mortality       Store group mortality

POST   /admin/livestock/animals/{animal}/yields        Store animal yield
POST   /admin/livestock/groups/{group}/yields          Store group yield
```

### Middleware

All routes should require:

```text
auth
appropriate livestock permission
```

Use the existing permission middleware pattern.

---

## 11. Controllers

Suggested controllers:

```text
LivestockDashboardController
SpeciesController
BreedController
AnimalController
AnimalGroupController
LivestockTreatmentController
LivestockBreedingController
LivestockPregnancyCheckController
LivestockBirthController
LivestockWeightController
LivestockFeedController
LivestockMovementController
LivestockMortalityController
LivestockYieldController
```

If this creates too many controllers for the first pass, a `LivestockRecordController` may handle several record forms temporarily, but keep methods clear.

---

## 12. Validation Rules

### Species validation

Required:

- name.
- code.
- species_type.
- status.

Rules:

- code unique within organisation where applicable.

### Breed validation

Required:

- species_id.
- name.
- status.

Rules:

- species must exist.
- breed must belong to selected species.

### Animal validation

Required:

- organization_id.
- farm_id.
- species_id.
- animal_code.
- sex.
- source.
- status.

Optional:

- paddock_id.
- breed_id.
- tag_number.
- rfid_number.
- name.
- date_of_birth.
- health_status.
- production_status.
- dam_id.
- sire_id.
- current_weight.

Rules:

- farm must belong to organisation.
- paddock must belong to selected farm.
- breed must belong to selected species.
- animal_code unique within farm.
- tag_number unique within farm if provided.
- dam/sire must belong to same organisation/farm where provided.

### Animal group validation

Required:

- organization_id.
- farm_id.
- species_id.
- group_code.
- name.
- group_type.
- initial_count.
- current_count.
- source.
- status.

Rules:

- farm must belong to organisation.
- paddock must belong to selected farm if provided.
- breed must belong to selected species.
- group_code unique within farm.
- counts must be numeric and not negative.

### Treatment validation

Required:

- treatment_type.
- treatment_date.

Optional:

- product_id.
- dosage.
- route.
- withdrawal days.
- follow_up_date.

Rules:

- product_id must belong to selected organisation if provided.
- do not deduct inventory.

### Feed validation

Required:

- feed_date.
- quantity.
- quantity_unit.

Optional:

- product_id.
- feed_name_snapshot.

Rules:

- quantity must be greater than zero.
- product must belong to selected organisation if provided.
- do not deduct inventory.

### Movement validation

Required:

- movement_date.

Optional:

- from_paddock_id.
- to_paddock_id.

Rules:

- paddocks must belong to selected farm.
- from and to paddock should not be the same if both are provided.

### Mortality validation

Required:

- mortality_date.

Rules:

- for group mortality, number_dead must be greater than zero and should not exceed current_count.

### Yield validation

Required:

- yield_date.
- yield_type.
- quantity.
- unit_of_measure.

Rules:

- quantity must be greater than zero.

---

## 13. Views / UI

Keep UI simple and operational.

### 13.1 Livestock dashboard

Should show links/cards for:

- Species.
- Breeds.
- Animals.
- Animal Groups.
- Treatments.
- Breeding.
- Births.
- Feed.
- Movements.
- Mortality.
- Yields.
- Withdrawals.

Optional simple metrics:

- Total active animals.
- Active animal groups.
- Animals under treatment.
- Active withdrawals.
- Births this month.
- Mortality this month.

Do not build advanced analytics yet.

### 13.2 Species list

Columns:

- Code.
- Name.
- Species type.
- Status.
- Actions.

### 13.3 Breed list

Columns:

- Species.
- Breed name.
- Status.
- Actions.

### 13.4 Animal list

Columns:

- Animal code.
- Tag.
- Name.
- Species.
- Breed.
- Sex.
- Farm.
- Paddock.
- Status.
- Health status.
- Actions.

### 13.5 Animal profile

Sections:

- Summary.
- Location.
- Health and treatments.
- Withdrawals.
- Breeding and pregnancy.
- Births.
- Weights.
- Feed.
- Movement history.
- Mortality if applicable.
- Yields.
- Notes.

### 13.6 Animal group list

Columns:

- Group code.
- Name.
- Species.
- Breed.
- Farm.
- Paddock.
- Current count.
- Status.
- Actions.

### 13.7 Animal group profile

Sections:

- Summary.
- Location.
- Current count.
- Health and treatments.
- Withdrawals.
- Feed.
- Movement history.
- Mortality.
- Yields.
- Notes.

### 13.8 Treatment form

Fields:

- Treatment type.
- Treatment date.
- Condition/reason.
- Diagnosis.
- Product optional.
- Product name snapshot.
- Dosage.
- Route.
- Frequency.
- Duration.
- Withdrawal meat/milk/egg days.
- Follow-up date.
- Treated by.
- Notes.

### 13.9 Breeding form

Fields:

- Breeding date.
- Breeding method.
- Male animal/sire optional.
- Sire name snapshot.
- Expected due date.
- Notes.

### 13.10 Birth form

Fields:

- Birth date.
- Number born.
- Number alive.
- Number dead.
- Birth type.
- Notes.

### 13.11 Feed form

Fields:

- Feed date.
- Product optional.
- Feed name snapshot.
- Quantity.
- Unit.
- Fed by.
- Notes.

### 13.12 Movement form

Fields:

- Movement date.
- From paddock.
- To paddock.
- Reason.
- Moved by.
- Notes.

### 13.13 Mortality form

Fields:

- Mortality date.
- Number dead where group.
- Cause.
- Suspected reason.
- Disposal method.
- Notes.

### 13.14 Yield form

Fields:

- Yield date.
- Yield type.
- Quantity.
- Unit.
- Grade.
- Destination.
- Notes.

---

## 14. Navigation

Add Livestock to the admin/module navigation if the user has relevant permissions.

Suggested label:

```text
Livestock
```

Suggested left menu inside module:

```text
Livestock Dashboard
Species
Breeds
Animals
Animal Groups
Treatments
Breeding
Births
Feed
Movements
Mortality
Yields
Withdrawals
```

For this branch, a simple navigation link is enough.

Do not show livestock links to users without livestock permissions.

Backend routes must still enforce permissions.

---

## 15. Business Rules

### Rule 1: Support individual animals and animal groups

Do not force all animal records to be individual.

### Rule 2: Livestock records must be organisation/farm scoped

Animal records, groups, and events must belong to organisation and farm.

### Rule 3: Paddocks come from Core/Admin

Do not create another paddock table.

Use Core/Admin paddocks/pens.

### Rule 4: Paddock must belong to selected farm

Do not place animals in paddocks from another farm.

### Rule 5: Breed must belong to selected species

Do not allow invalid species/breed combinations.

### Rule 6: Animal and group codes should be unique within farm

Avoid duplicate identification.

### Rule 7: Treatment products can reference inventory but must not deduct stock yet

Stock deduction comes later through Inventory integration.

### Rule 8: Feed products can reference inventory but must not deduct stock yet

Stock deduction comes later.

### Rule 9: Withdrawal periods can be recorded but should not block sales yet

Sales/dispatch blocking belongs to future Sales/Traceability workflows.

### Rule 10: Mortality should update status/count where simple

- Individual animal mortality can mark animal as dead.
- Group mortality can reduce current_count.

Keep it simple and tested.

### Rule 11: Movement can update current paddock where simple

When movement is recorded, update animal/group paddock if safe and tested.

### Rule 12: Do not post finance costs yet

Treatment, feed, mortality, and yield records should not create cost/revenue entries in this branch.

### Rule 13: Demo livestock data should be local/testing only

Do not seed demo animals into production.

---

## 16. Integration with Other Modules

### 16.1 Core/Admin

Livestock depends on:

- organisations,
- farms,
- paddocks/pens.

### 16.2 Users/Permissions

Livestock access depends on permissions and membership scope.

### 16.3 Workers/Labour

Livestock records may optionally reference workers and teams.

### 16.4 Tasks / Work Orders

Livestock records may optionally reference related tasks.

For now, task references are informational.

Do not auto-complete tasks or create tasks from livestock records yet.

### 16.5 Inventory / Inputs

Treatments and feed may optionally reference inventory products.

Do not deduct stock yet.

### 16.6 Future Finance

Treatment, feed, mortality, and yield records will later support costing and revenue analysis.

Do not post costs yet.

### 16.7 Future Sales / Traceability

Animal sales, milk dispatch, eggs, fish harvest, and related traceability will come later.

Do not create sales records or batches yet.

---

## 17. Seed Data

Add demo livestock data only in local/testing environments.

Suggested demo data:

Species:

```text
Cattle
Goat
Poultry
Fish
```

Breeds:

```text
Friesian
Sahiwal
Boer Goat
Kienyeji Chicken
Broiler
```

Animals:

```text
Cow-001 — Friesian cow
Cow-002 — Sahiwal cow
Buck-001 — Boer goat
```

Animal groups:

```text
Broiler Batch 001
Layers Flock A
Fish Pond Batch A
```

Records:

```text
Treatment record
Feed record
Weight record
Yield record
Movement record
```

Important:

Do not seed demo livestock data in production.

---

## 18. Tests Required

### Migration/model tests

- Species can be created.
- Breed belongs to species.
- Animal belongs to organisation/farm/species/paddock.
- Animal group belongs to organisation/farm/species/paddock.
- Treatment belongs to animal or group.
- Feed belongs to animal or group.
- Movement belongs to animal or group.
- Mortality belongs to animal or group.
- Yield belongs to animal or group.
- Withdrawal period belongs to treatment.

### Route protection tests

- Guest cannot access `/admin/livestock`.
- Authenticated user without livestock permission cannot access `/admin/livestock`.
- Admin/authorised user can access `/admin/livestock`.

### CRUD tests

- Authorised user can create species.
- Authorised user can create breed.
- Authorised user can create individual animal.
- Authorised user can create animal group.
- Authorised user can update animal.
- Authorised user can update animal group.
- Authorised user can record treatment.
- Authorised user can record feed.
- Authorised user can record movement.
- Authorised user can record mortality.
- Authorised user can record yield.

### Validation tests

- Animal requires organisation/farm/species/code.
- Animal rejects paddock from another farm.
- Animal rejects breed from another species.
- Group rejects negative current count.
- Treatment product must belong to same organisation.
- Feed product must belong to same organisation.
- Movement rejects paddock from another farm.
- Group mortality cannot exceed current count.
- Yield quantity must be greater than zero.

### Behaviour tests

- Animal mortality marks animal as dead where implemented.
- Group mortality reduces current_count where implemented.
- Movement updates current paddock where implemented.
- Treatment with withdrawal days creates withdrawal period records where implemented.

### Seeder tests

- Livestock permissions are seeded.
- Demo livestock data is only seeded in local/testing if included.

---

## 19. Acceptance Criteria

This branch is complete when:

1. Livestock module exists under `app/Modules/Livestock`.
2. Routes exist under `/admin/livestock`.
3. Livestock routes are protected by auth and permissions.
4. Species can be managed without hard deletes.
5. Breeds can be managed without hard deletes.
6. Individual animals can be registered and managed.
7. Animal groups can be registered and managed.
8. Animals/groups can be linked to paddocks from Core/Admin.
9. Treatments/vaccinations can be recorded.
10. Breeding and pregnancy checks can be recorded.
11. Births can be recorded.
12. Feed records can be recorded without deducting inventory.
13. Movements can be recorded.
14. Mortality can be recorded.
15. Yields can be recorded without creating sales records.
16. Withdrawal periods can be recorded/displayed where treatment requires them.
17. Livestock permissions are seeded.
18. Navigation appears only for authorised users.
19. Tests pass.
20. README/module documentation is updated.
21. No automatic inventory deduction, finance posting, sales, payroll, RFID/IoT, AI, or offline logic is implemented.

---

## 20. Recommended Build Order

1. Confirm branch includes Core/Admin, Users/Permissions, Workers/Labour, Tasks, Inventory, and Crops.
2. Inspect current permission middleware and seeding structure.
3. Add livestock permissions to seeder.
4. Create Livestock module provider/routes.
5. Create migrations for species, breeds, animals, animal groups, events, treatments, withdrawals, breeding, pregnancy, births, weights, feed, movement, mortality, yields.
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

## 21. Codex Prompt for Livestock Branch

Use this prompt after confirming the branch is correctly based on Crops.

```text
You are working on the `livestock-module` branch of a Laravel 13 modular monolith project for a mixed-farm management platform.

The Laravel foundation, Core/Admin module, Users/Permissions module, Workers/Labour module, Tasks/Work Orders module, Inventory/Inputs module, and Crops module already exist. Do not rebuild them.

This branch is only for the Livestock module foundation.

Do not implement automatic medicine/feed inventory deduction, finance/cost posting, sales/disposal workflows, payroll, RFID/IoT integrations, AI recommendations, advanced veterinary diagnosis, or mobile/offline sync.

First inspect the project and report:
1. Current Core/Admin models for organizations, farms, paddocks, and sites.
2. Current Users/Permissions models, middleware, roles, permissions, and seeding pattern.
3. Current Workers/Labour models for workers and teams.
4. Current Tasks/Work Orders module state.
5. Current Inventory/Inputs product models and stock movement state.
6. Current Crops module state.
7. Existing app/Modules/Livestock folder state.
8. Existing navigation pattern.
9. Existing seeder pattern and local/testing demo data safeguards.
10. Any risks or conflicts before implementation.

Then implement the Livestock module foundation:

1. Module structure:
   - Use app/Modules/Livestock.
   - Add provider/routes/controllers/models/views/tests/README as needed.

2. Migrations and models:
   - livestock_species
   - livestock_breeds
   - livestock_animals
   - livestock_animal_groups
   - livestock_events
   - livestock_treatment_records
   - livestock_withdrawal_periods
   - livestock_breeding_records
   - livestock_pregnancy_checks
   - livestock_birth_records
   - livestock_weight_records
   - livestock_feed_records
   - livestock_movement_records
   - livestock_mortality_records
   - livestock_yield_records

3. Species requirements:
   - Species can be system-level or organization-level.
   - Has name, code, species_type, description, status.
   - Deactivate by status, not hard delete.

4. Breed requirements:
   - Breed belongs to species.
   - Has name, code optional, description, status.
   - Breed must belong to selected species.
   - Deactivate by status, not hard delete.

5. Individual animal requirements:
   - Animal belongs to organization, farm, species, optional breed, optional paddock.
   - Paddock must belong to selected farm.
   - Breed must belong to selected species.
   - Animal has animal_code, tag_number optional, rfid_number optional, name optional, sex, date of birth, source, status, health_status, production_status, current weight, dam/sire optional, notes.
   - Animal code must be unique within farm.
   - Tag number must be unique within farm where provided.
   - Deactivate by status, not hard delete.

6. Animal group requirements:
   - Animal group belongs to organization, farm, species, optional breed, optional paddock.
   - Has group_code, name, group_type, initial_count, current_count, source, status, health_status, production_status, notes.
   - Group code must be unique within farm.
   - Counts cannot be negative.
   - Deactivate/close by status, not hard delete.

7. Event requirements:
   - Event belongs to organization and farm.
   - Event applies to either animal or animal group.
   - Event can optionally reference related task, paddock, performed user, worker, or team.
   - At least one of animal_id or animal_group_id must be present.
   - Do not create inventory or finance side effects.

8. Treatment/vaccination requirements:
   - Treatment can apply to animal or animal group.
   - Can optionally reference inventory product.
   - Product must belong to selected organization if provided.
   - Capture treatment type/date, condition/reason, diagnosis, product snapshot, dosage, route, frequency, duration, withdrawal days, follow-up date, treated by, notes.
   - Do not deduct inventory.
   - If withdrawal meat/milk/egg days are provided, create withdrawal period records where practical and tested.

9. Breeding/pregnancy/birth requirements:
   - Breeding applies to animal or group where useful.
   - Pregnancy check records result and due date.
   - Birth record captures number born/alive/dead.
   - Do not overbuild automatic offspring creation unless simple and tested.

10. Weight/feed/movement/mortality/yield requirements:
   - Weight captures date, weight, unit.
   - Feed can optionally reference inventory product but must not deduct stock.
   - Movement records from/to paddock and validates paddocks belong to farm.
   - Movement may update current paddock if simple and tested.
   - Mortality records cause/details; individual mortality may mark animal dead; group mortality may reduce current_count if simple and tested.
   - Yield captures type, quantity, unit, grade/destination; do not create sales records.

11. Permissions:
   - Seed starter permissions:
     livestock.view, livestock.manage,
     livestock-species.view, livestock-species.create, livestock-species.update, livestock-species.deactivate,
     livestock-animals.view, livestock-animals.create, livestock-animals.update, livestock-animals.deactivate,
     livestock-groups.view, livestock-groups.create, livestock-groups.update, livestock-groups.deactivate,
     livestock-health.view, livestock-health.create,
     livestock-breeding.view, livestock-breeding.create,
     livestock-births.view, livestock-births.create,
     livestock-feed.view, livestock-feed.create,
     livestock-movements.view, livestock-movements.create,
     livestock-mortality.view, livestock-mortality.create,
     livestock-yields.view, livestock-yields.create,
     livestock-withdrawals.view.
   - Assign reasonable defaults:
     owner and system-admin get all.
     farm-manager gets livestock operational management permissions.
     livestock-officer gets livestock operational permissions.
     auditor gets view permissions.
   - Do not break existing permissions.

12. Routes:
   - Use /admin/livestock prefix.
   - Use livestock.* route names.
   - Protect all livestock routes behind auth and appropriate permissions.

13. Views:
   - Add simple Livestock dashboard.
   - Add species list/create/view/edit/deactivate.
   - Add breed list/create/view/edit/deactivate.
   - Add animal list/create/view/edit/deactivate and profile page.
   - Add animal group list/create/view/edit/deactivate and profile page.
   - Add simple forms/actions for treatments, breeding, pregnancy checks, births, weights, feed, movements, mortality, yields.
   - Show relevant record history on animal/group profiles.
   - Keep UI simple and consistent with existing Blade layout.

14. Navigation:
   - Add Livestock admin navigation link only when user has livestock permissions.
   - Do not rely only on hidden links; enforce backend permission checks.

15. Demo data:
   - If demo species/breeds/animals/groups/records are seeded, only create them in local/testing environments.
   - Do not seed demo livestock data in production.

16. Tests:
   - Guest cannot access /admin/livestock.
   - User without livestock permission cannot access /admin/livestock.
   - Admin can access /admin/livestock.
   - Livestock permissions are seeded.
   - Species can be created/updated/deactivated.
   - Breed can be created and must belong to species.
   - Animal can be created and rejects paddock from another farm.
   - Animal rejects breed from another species.
   - Animal group can be created and rejects negative counts.
   - Treatment can reference inventory product from same organization.
   - Treatment rejects product from another organization.
   - Treatment with withdrawal days creates withdrawal periods where implemented.
   - Feed record can reference product without deducting inventory.
   - Movement rejects paddock from another farm and updates current paddock if implemented.
   - Group mortality cannot exceed current count and reduces count if implemented.
   - Individual mortality marks animal as dead if implemented.
   - Yield quantity must be greater than zero.

17. Documentation:
   - Update app/Modules/Livestock/README.md with entities, routes, permissions, business rules, known limitations, and follow-up work.

Constraints:
- Do not implement automatic medicine/feed inventory deduction.
- Do not implement finance/cost posting.
- Do not implement sales/disposal workflows.
- Do not implement payroll.
- Do not implement RFID/IoT integrations.
- Do not implement AI recommendations.
- Do not add unnecessary packages.
- Do not commit .env, vendor, node_modules, local databases, logs, cache files, build output, or generated compiled views.

After implementation, run:
- composer install if needed
- npm install if needed
- php artisan migrate:fresh --seed
- php artisan test
- npm run build
- php artisan route:list --path=admin/livestock

Then summarize exactly what changed, what tests passed, and any risks/follow-up work.
```

---

## 22. Commit Message

If validation passes, use:

```bash
git add --dry-run .
git add .
git commit -m "Build Livestock module foundation"
git push origin livestock-module
```

---

## 23. Follow-Up After This Branch

After this module is complete, the next module should be:

```text
irrigation-module
```

Reason:

Irrigation is important to mixed farm operations and connects naturally to crops, tasks, workers, assets, water sources, and future costing.

Known future livestock follow-ups:

- Dynamic farm/paddock/species/breed filtering.
- Automatic inventory deduction for medicine/feed.
- Treatment approval workflow.
- Vet visit scheduling.
- Advanced breeding/reproduction analytics.
- Milk/egg collection workflows.
- Sales/traceability integration.
- Cost posting to Finance.
- RFID/QR tagging support.
- Mobile/offline animal records.
- Animal health attachments/photos.
- Compliance reports for treatments and withdrawals.

