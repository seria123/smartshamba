# Livestock Module

Livestock is the animal-side operational foundation for the mixed-farm platform. It supports individually tracked animals and group-managed animals without creating inventory, finance, sales, payroll, RFID/IoT, AI, or offline side effects.

## Entities

- `livestock_species`: system-level or organization-level species.
- `livestock_breeds`: breeds under species.
- `livestock_animals`: individually tracked animals.
- `livestock_animal_groups`: flocks, herds, batches, ponds, and other group records.
- `livestock_events`: timeline events generated from operational records.
- `livestock_treatment_records`: treatment, vaccination, deworming, and related health records.
- `livestock_withdrawal_periods`: meat, milk, and egg withdrawal windows after treatment.
- `livestock_breeding_records`, `livestock_pregnancy_checks`, `livestock_birth_records`.
- `livestock_weight_records`, `livestock_feed_records`, `livestock_movement_records`, `livestock_mortality_records`, `livestock_yield_records`.

## Routes

All routes are under `/admin/livestock`, use `livestock.*` route names, and require authentication plus livestock permissions.

- Dashboard: `livestock.dashboard`
- Species: `livestock.species.*`
- Breeds: `livestock.breeds.*`
- Animals: `livestock.animals.*`
- Animal groups: `livestock.groups.*`
- Profile record actions: treatments, breeding, pregnancy checks, births, weights, feed, movements, mortality, and yields.

## Permissions

The permission seeder creates:

- Module access: `livestock.view`, `livestock.manage`
- Species: `livestock-species.view/create/update/deactivate`
- Animals: `livestock-animals.view/create/update/deactivate`
- Groups: `livestock-groups.view/create/update/deactivate`
- Records: `livestock-health`, `livestock-breeding`, `livestock-births`, `livestock-feed`, `livestock-movements`, `livestock-mortality`, `livestock-yields`, and `livestock-withdrawals`

Default role behavior:

- `owner` and `system-admin`: all permissions.
- `farm-manager`: livestock operational management permissions.
- `livestock-officer`: livestock operational permissions.
- `auditor`: livestock view permissions only.

## Business Rules

- Animals and groups are scoped to organization and farm.
- Paddocks are reused from Core/Admin and must belong to the selected farm.
- Breeds must belong to the selected species.
- Animal codes and group codes are unique within a farm.
- Animal tag numbers are unique within a farm when provided.
- Feed and treatment products may reference Inventory products from the same organization, but stock is not deducted.
- Treatment withdrawal days create withdrawal period records where provided.
- Movement records update the current paddock when a destination paddock is provided.
- Individual mortality marks the animal as dead.
- Group mortality cannot exceed current count and reduces current count.
- Yield records do not create sales or traceability records.

## Demo Data

`LivestockModuleSeeder` creates demo livestock data only in `local` and `testing` environments. It does not seed demo animals in production.

## Known Limitations

- No automatic inventory deduction for medicine or feed.
- No finance cost posting.
- No animal sales, disposals, or traceability batches.
- No advanced veterinary workflow, approval workflow, analytics, RFID/IoT, AI recommendations, or mobile/offline sync.
- Profile record forms are intentionally simple; dynamic filtering by farm/species/breed can be added later.

## Follow-Up Work

- Dynamic dependent selects for farm/paddock and species/breed.
- Medicine and feed stock deduction through Inventory.
- Vet visit scheduling and treatment approval workflow.
- Milk, egg, fish, and animal sale workflows.
- Finance costing and traceability integration.
- Attachments/photos and compliance reports.
