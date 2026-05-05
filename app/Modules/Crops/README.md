# Crops Module

Foundation module for crop production records in the Laravel modular monolith.

## Entities

- `crop_crops`: system-level or organization-level crop master records.
- `crop_varieties`: varieties scoped to a crop.
- `crop_seasons`: organization seasons, optionally narrowed to one farm.
- `crop_cycles`: field-level production cycles for a crop/variety/season.
- `crop_activities`: field activity timeline entries linked to optional tasks, workers, or teams.
- `crop_planting_details`: planting detail extension for planting/transplanting activities.
- `crop_scouting_observations`, `crop_treatment_applications`, `crop_harvest_records`, `crop_loss_records`: operational crop records.

## Routes

All routes are under `/admin/crops` and use `crops.*` route names. Routes are protected by `auth` plus the relevant `admin.access:*` permission middleware.

## Permissions

Seeded permissions cover dashboard, crop master, seasons, cycles, activities, scouting, treatments, harvests, and losses. Owner and system-admin receive all permissions. Farm manager receives operational crop management. Agronomist receives starter operational permissions. Auditor receives view-only crop permissions.

## Business Rules

- Crops and varieties are deactivated with `status`, not hard deleted.
- Crop cycles are closed or cancelled with status and closure/cancellation fields.
- Fields must belong to the selected farm.
- Varieties must belong to the selected crop.
- Related tasks must match the crop cycle organization and farm.
- Treatment products must belong to the crop cycle organization.
- Harvest quantity must be greater than zero.
- Inventory stock is not deducted, finance cost is not posted, and sales/traceability batches are not created.

## Demo Data

Demo crops, seasons, cycles, and activities are seeded only in `local` and `testing` environments by `CropsModuleSeeder`.

## Known Limitations

- Forms are intentionally simple and do not dynamically filter dropdowns client-side.
- No inventory deduction, costing, payroll, sales batch, weather, satellite, AI, or mobile/offline workflows are implemented.
- Follow-up work can add richer reporting, approvals, filtering, and dedicated edit flows for individual records.
