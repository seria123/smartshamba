# Documents / Attachments / Media Module — Codex Implementation Prompt

## Context

We are continuing a Laravel 13 modular monolith mixed-farm management platform.

We are building one module per Git branch. This branch should add a central **Documents / Attachments / Media** foundation module.

The latest completed and pushed branch should be:

```powershell
notifications-alerts-module
```

Create the new branch from there:

```powershell
git checkout notifications-alerts-module
git pull origin notifications-alerts-module
git checkout -b documents-attachments-module
```

## Working Rules

Before making changes, Codex must:

1. Confirm the current branch:

   ```powershell
   git branch --show-current
   ```

   Expected:

   ```text
   documents-attachments-module
   ```

2. Inspect the repository structure.

3. Confirm that the expected previous modules exist, at minimum:

   - Core/Admin
   - Users/Permissions
   - Workers/Labour
   - Tasks/Work Orders
   - Inventory/Inputs
   - Crops
   - Livestock
   - Irrigation
   - Assets/Maintenance
   - Finance/Costing
   - Sales/Produce/Revenue
   - Reports/Analytics
   - Notifications/Alerts

4. Confirm that the app is still a modular monolith and that module providers/routes follow the established project pattern.

5. Do not implement until the branch and previous-module checks are complete.

---

# Goal

Build a central **Documents / Attachments / Media** module that lets authorised users attach files, photos, scans, receipts, invoices, reports, delivery notes, and supporting evidence to existing farm records.

This module should be a reusable foundation. It should **not** become a full document-management system yet.

The module should answer:

- What documents/photos are attached to this record?
- Who uploaded them?
- When were they uploaded?
- What type/category are they?
- Can an authorised admin view/download/remove them safely?
- Can users find attachments by organisation, farm, category, module/source, and date?

---

# Architectural Position

This is a cross-cutting module.

It may reference records from other modules using a safe generic attachment target pattern, but it must not mutate those source records.

Examples of future use:

- Crop scouting photos
- Crop treatment evidence
- Harvest photos
- Livestock vet reports
- Livestock birth/mortality evidence
- Supplier invoices
- Delivery notes
- Finance receipts
- Sales delivery notes
- Asset repair invoices
- Task before/after photos
- Irrigation issue photos

For this branch, the module owns only the attachment/document records and safe file access logic.

---

# Strict Scope

## Include

Implement a Documents module with:

1. Central attachment records.
2. File upload.
3. Attachment listing.
4. Attachment detail page.
5. Secure download route.
6. Optional inline preview route for safe file types.
7. Delete/remove attachment.
8. Attachment categories/types.
9. Basic notes/description.
10. Organisation/farm scoping.
11. Uploader tracking.
12. Basic source/attachable reference fields.
13. Reports/list filters.
14. Seeder/demo records.
15. Feature tests.

## Do Not Include

Do **not** implement:

- OCR
- AI document reading
- AI image analysis
- e-signatures
- document approval workflows
- document version control
- external cloud storage integrations
- S3
- Google Drive
- Dropbox
- OneDrive
- M-Pesa upload parsing
- automatic finance posting from uploaded receipts
- automatic sales posting from uploaded invoices
- automatic inventory mutation from delivery notes
- automatic crop/livestock/task/asset updates
- notifications integration
- expiry reminders
- PDF generation
- Excel export
- ZIP export
- image editing
- image compression pipelines
- video transcoding
- public unauthenticated file URLs
- raw direct storage links exposed without authorization
- multi-tenant access bypass
- full document management / DMS complexity

This branch is a practical attachment foundation only.

---

# Permissions

Add permissions through the existing first-party RBAC pattern.

Add:

```text
documents.view
documents.manage
documents.delete
```

Suggested behaviour:

- `documents.view`
  - Can view document dashboard/list/detail.
  - Can download/view files allowed by organisation/farm scope.
- `documents.manage`
  - Can upload/create/update attachment metadata.
- `documents.delete`
  - Can delete/remove attachments.

Use existing permission middleware style, matching prior modules.

Do not add Spatie or any new permission package.

---

# Module Registration

Create a new module:

```text
app/Modules/Documents/
```

Suggested structure:

```text
app/Modules/Documents/
  Http/
    Controllers/
      DocumentDashboardController.php
      AttachmentController.php
      AttachmentDownloadController.php
      AttachmentCategoryController.php
      AttachmentReportController.php
  Models/
    FarmAttachment.php
    AttachmentCategory.php
  Providers/
    DocumentsServiceProvider.php
  Services/
    DocumentsAccessContext.php
    AttachmentStorageService.php
    AttachmentSourceResolver.php
    AttachmentSummaryService.php
  routes/
    web.php
```

Register the provider in:

```text
bootstrap/app.php
```

Add navigation in:

```text
resources/views/layouts/app.blade.php
```

Add module activation to:

```text
database/seeders/CoreFoundationSeeder.php
```

Add permissions to:

```text
database/seeders/UsersPermissionsSeeder.php
```

Add module seeder call in:

```text
database/seeders/DatabaseSeeder.php
```

---

# Database

Add migration:

```text
database/migrations/2026_05_09_900000_create_documents_attachments_tables.php
```

Use timestamps and soft deletes where appropriate.

## Table: attachment_categories

Suggested fields:

```text
id
organisation_id nullable/constrained if organisation table exists
name
slug
description nullable
is_system boolean default false
is_active boolean default true
sort_order unsigned integer default 0
created_at
updated_at
soft_deletes
```

Purpose:

- Receipt
- Invoice
- Delivery Note
- Vet Report
- Lab Report
- Photo Evidence
- Contract
- Warranty
- Certificate
- Other

Keep categories simple.

## Table: farm_attachments

Suggested fields:

```text
id
organisation_id constrained
farm_id nullable/constrained
attachment_category_id nullable/constrained

attachable_module nullable string
attachable_type nullable string
attachable_id nullable unsignedBigInteger
attachable_label nullable string

title
description nullable

original_filename
stored_filename
storage_disk default local/private pattern
storage_path
mime_type nullable
file_extension nullable
file_size_bytes unsignedBigInteger default 0
checksum_sha256 nullable

visibility enum/private/internal default private
status enum/active/archived default active

uploaded_by nullable constrained users
deleted_by nullable constrained users
deleted_at
created_at
updated_at
```

Notes:

- The exact foreign keys should match the real existing table names.
- Do not guess table names blindly. Inspect migrations/models first.
- If the project uses `organisations` not `organizations`, follow the project’s existing naming.
- The file itself should not be stored in the database.
- Use `farm_attachments` rather than a generic `documents` table name if that better matches the farm-domain module style.

## Polymorphic / Source Reference Strategy

Do not tightly couple this module by adding foreign key columns for every module.

Use generic source fields:

```text
attachable_module
attachable_type
attachable_id
attachable_label
```

Examples:

```text
attachable_module = crops
attachable_type = crop_cycle
attachable_id = 12
attachable_label = Tomato Cycle A
```

```text
attachable_module = livestock
attachable_type = animal
attachable_id = 9
attachable_label = Cow K-023
```

This keeps the module reusable without forcing migrations every time a new source module is added.

If Laravel morphs are already consistently used in the codebase, you may align with that pattern, but do not create fragile hard-coupled relationships to every source module.

---

# Storage Requirements

Use Laravel storage APIs.

Preferred principle:

- Store files in a non-public/private local path.
- Serve downloads through an authenticated controller route.
- Do not expose raw direct storage paths.

Suggested storage path pattern:

```text
attachments/{organisation_id}/{farm_id_or_general}/{yyyy}/{mm}/{uuid-or-generated-filename}
```

Example:

```text
attachments/1/farm-2/2026/05/af9a7c_receipt.pdf
```

The stored filename must be generated/sanitized. Do not trust the uploaded original filename as the storage filename.

The original filename may be saved as metadata.

## Validation

Implement conservative validation.

Allowed file types can include:

```text
pdf
jpg
jpeg
png
webp
doc
docx
xls
xlsx
csv
txt
```

Avoid executable or risky file types:

```text
exe
bat
cmd
ps1
sh
php
js
html
svg
zip
rar
7z
```

Suggested max file size:

```text
10 MB
```

If the existing app has a shared validation config or file limit pattern, follow it.

## Preview

Optional but useful:

- Images: preview inline through a controlled route.
- PDFs: allow inline viewing through a controlled route if safe.
- Office files: download only.

Do not implement advanced previews or conversion.

---

# Routes

All routes should be under:

```text
/admin/documents
```

All routes must require auth and appropriate permissions.

Suggested routes:

```text
GET    /admin/documents
GET    /admin/documents/attachments
GET    /admin/documents/attachments/create
POST   /admin/documents/attachments
GET    /admin/documents/attachments/{attachment}
GET    /admin/documents/attachments/{attachment}/edit
PUT    /admin/documents/attachments/{attachment}
GET    /admin/documents/attachments/{attachment}/download
GET    /admin/documents/attachments/{attachment}/preview
DELETE /admin/documents/attachments/{attachment}

GET    /admin/documents/categories
GET    /admin/documents/categories/create
POST   /admin/documents/categories
GET    /admin/documents/categories/{category}/edit
PUT    /admin/documents/categories/{category}
DELETE /admin/documents/categories/{category}

GET    /admin/documents/reports/summary
GET    /admin/documents/reports/by-category
GET    /admin/documents/reports/by-source
```

Do not add API routes unless the project has already established admin API routes for other modules.

---

# Controllers

## DocumentDashboardController

Shows:

- Total active attachments
- Total storage used
- Attachments uploaded this month
- Attachments by category
- Recent uploads
- Farm-scoped summary

## AttachmentController

Handles:

- List
- Create form
- Store upload
- Detail
- Edit metadata
- Update metadata
- Delete/remove

Store flow:

1. Validate organisation/farm access.
2. Validate category access.
3. Validate file.
4. Generate safe stored filename/path.
5. Store file using Laravel Storage.
6. Save metadata.
7. Redirect with success.

Delete flow:

1. Confirm user has `documents.delete`.
2. Confirm same organisation/farm access.
3. Confirm attachment exists.
4. Soft-delete or archive DB record.
5. Remove physical file only if implemented safely and intentionally.
6. If physical deletion fails, do not crash the app; report a meaningful error or keep the record archived.

For this branch, prefer soft delete of the DB record. Physical file deletion can be implemented carefully, but do not introduce risky bulk delete logic.

## AttachmentDownloadController

Handles secure download/preview.

Must:

- Require auth.
- Verify permission.
- Verify organisation/farm scope.
- Check file exists.
- Return streamed download/response.
- Never expose unauthorised raw storage paths.

## AttachmentCategoryController

CRUD for categories.

Guard system categories from unsafe deletion if `is_system = true`.

## AttachmentReportController

Read-only summary pages.

---

# Services

## DocumentsAccessContext

Centralise organisation/farm access resolution.

Follow the existing access pattern used by Finance/Sales/Reports/Notifications.

Do not invent a new access model.

## AttachmentStorageService

Responsible for:

- Validating storage path generation.
- Storing uploaded files.
- Generating safe filenames.
- Calculating checksum if practical.
- Deleting files safely if implemented.

## AttachmentSourceResolver

Responsible for:

- Presenting source labels.
- Validating safe source module/type values.
- Building source links where obvious.

Keep it conservative.

Supported source module/type suggestions:

```text
core.farm
core.field
workers.worker
tasks.task
tasks.work_order
inventory.product
inventory.stock_lot
crops.crop_cycle
crops.activity
crops.treatment
crops.harvest
livestock.animal
livestock.group
livestock.treatment
irrigation.schedule
irrigation.event
irrigation.issue
assets.asset
assets.maintenance_record
finance.cost_entry
sales.sale_record
```

Do not break if a source type is unknown. Store and display as generic source text.

For this branch, the source resolver may be simple. Do not over-engineer cross-module joins.

## AttachmentSummaryService

Provides dashboard/report totals.

---

# Views

Create Blade views under:

```text
resources/views/documents/
```

Suggested views:

```text
dashboard.blade.php
attachments/index.blade.php
attachments/create.blade.php
attachments/show.blade.php
attachments/edit.blade.php
categories/index.blade.php
categories/create.blade.php
categories/edit.blade.php
reports/summary.blade.php
reports/by-category.blade.php
reports/by-source.blade.php
```

Use existing layout/components/classes from prior modules.

No new frontend framework.

No charting dependency.

Keep the UI admin-friendly and simple.

## Attachment List Filters

Include filters where practical:

- Organisation/farm context if pattern exists
- Category
- Source module
- Source type
- Mime/file type
- Date from/to
- Uploaded by
- Search title/original filename

## Upload Form Fields

Include:

```text
file
title
category
organisation/farm context
attachable_module
attachable_type
attachable_id
attachable_label
description/notes
```

Do not require source fields. Some attachments may be general farm-level documents.

---

# Seeder

Create:

```text
database/seeders/DocumentsAttachmentsSeeder.php
```

Seed only safe starter/demo data.

Seed categories such as:

```text
Receipt
Invoice
Delivery Note
Photo Evidence
Vet Report
Lab Report
Warranty
Certificate
Contract
Other
```

For demo attachments:

- Do not depend on real uploaded binary files unless you create tiny safe text placeholder files through Storage in local/testing only.
- Demo data must be guarded to local/testing environment if it creates records that are not universal starter categories.
- System categories can be seeded in all environments if appropriate.

Important:

- Avoid seeding references to non-existing files in a way that breaks download tests.
- If demo records need files, create small `.txt` placeholder files safely in storage during seeding.

---

# Tests

Add:

```text
tests/Feature/DocumentsAttachmentsModuleTest.php
```

Tests should cover:

1. Unauthenticated users cannot access `/admin/documents`.
2. Users without `documents.view` cannot access the dashboard.
3. Users with `documents.view` can access dashboard/list/detail.
4. Users with `documents.manage` can upload a valid attachment.
5. Invalid file type is rejected.
6. Oversized file is rejected if practical to test.
7. Uploaded file metadata is saved.
8. Download route requires permission and scope.
9. Download route returns file for authorised user.
10. Attachment metadata can be updated.
11. Attachment can be deleted/soft-deleted by `documents.delete`.
12. Category CRUD works for authorised manager.
13. System category deletion is blocked.
14. Reports pages are read-only GET pages.
15. Route list under `/admin/documents` shows expected routes.

Use Laravel testing helpers for file uploads.

Do not require public/build during tests. If the existing TestCase disables Vite, keep that pattern.

---

# Navigation

Add Documents/Attachments to the admin navigation after Notifications or before Reports depending on existing menu order.

Recommended order:

```text
Core
Access
Workers
Tasks
Inventory
Crops
Livestock
Irrigation
Assets
Finance
Sales
Reports
Notifications
Documents
```

Or if the layout already places cross-cutting modules together, follow the existing structure.

Navigation should respect `documents.view`.

---

# Reports

Add simple read-only pages:

1. Summary
   - total attachments
   - total storage used
   - uploads this month
   - recent uploads

2. By Category
   - category
   - count
   - total size

3. By Source
   - attachable_module
   - attachable_type
   - count
   - total size

No charts dependency.

Tables/cards only.

---

# Security Requirements

This module handles files, so be careful.

Must implement:

- Authenticated routes.
- Permission checks.
- Organisation/farm access checks.
- Safe file validation.
- Safe filename generation.
- No direct public raw paths.
- No executable file uploads.
- No PHP/HTML/JS/SVG uploads.
- No user-controlled storage paths.
- No path traversal.
- No download without database record authorization.
- No cross-organisation attachment access.

Use `Storage::disk(...)` APIs.

If mime type and extension disagree, prefer rejecting or using conservative validation.

---

# Expected Files Changed

Likely files:

```text
bootstrap/app.php
resources/views/layouts/app.blade.php

app/Modules/Documents/...

resources/views/documents/...

database/migrations/2026_05_09_900000_create_documents_attachments_tables.php
database/seeders/DocumentsAttachmentsSeeder.php
database/seeders/CoreFoundationSeeder.php
database/seeders/UsersPermissionsSeeder.php
database/seeders/DatabaseSeeder.php

tests/Feature/DocumentsAttachmentsModuleTest.php
```

Avoid unrelated changes.

---

# Validation Commands

After implementation, run:

```powershell
php artisan migrate:fresh --seed
php artisan test
npm.cmd run build
php artisan route:list --path=admin/documents
git branch --show-current
git status --short
git add --dry-run .
```

If `npm.cmd run build` fails due to sandbox/esbuild spawn permissions, rerun with the appropriate local approval/escalation.

---

# Git / Commit Safety

Do not stage or commit automatically unless specifically instructed.

Before final response, report:

1. Current branch.
2. Files changed.
3. Routes added.
4. Migration/table summary.
5. Permissions added.
6. Validation command results.
7. Explicit forbidden-logic confirmation.
8. Any risks/follow-ups.
9. Whether anything unrelated appears in `git status`.

Important:

Do not use:

```powershell
git add .
```

because this repo has repeatedly had unrelated untracked prompt docs and generated files.

When the user is ready to commit, they should manually stage only Documents module files.

---

# Forbidden Logic Confirmation Required

At the end, explicitly confirm that you did **not** add:

- OCR
- AI document reading
- e-signatures
- approval workflows
- version control
- external cloud storage providers
- public unauthenticated file URLs
- automatic finance posting
- automatic sales posting
- automatic inventory mutation
- source module mutations
- notification generation
- expiry reminder automation
- PDF/Excel export
- image/video processing pipelines
- mobile/offline sync

---

# Follow-ups to Note, But Not Implement Now

Possible later improvements:

- Document expiry tracking
- Link expiry alerts to Notifications
- Per-module attachment panels
- Bulk uploads
- Image thumbnails
- Cloud storage adapter
- OCR/AI extraction for receipts and invoices
- Document approval workflow
- Version control
- E-signatures
- Mobile photo capture
- Offline upload queue
- Richer document search
- Download audit trail

Do not implement these in this branch.
