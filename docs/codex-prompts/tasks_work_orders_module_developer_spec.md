# Tasks / Work Orders Module — Developer Specification

## 1. Purpose of This Module

The Tasks / Work Orders module manages planned and assigned farm work.

This module is one of the most important operational modules in the platform because farm management is action-driven. A farm system is not useful merely because it stores records. It becomes useful when managers can plan work, assign it to people, track progress, approve completion, and connect that work to fields, paddocks, warehouses, irrigation zones, crop cycles, livestock groups, assets, inventory requests, and cost records over time.

For this branch, the module should create the foundation for work orders and tasks. It should not yet implement crop activity logic, livestock activity logic, inventory stock movement, finance cost posting, or advanced mobile/offline workflows.

The goal is to build the task engine that future modules will use.

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

The next logical step is Tasks / Work Orders because the system now has farms, fields, paddocks, users, workers, and teams that can receive assigned work.

---

## 3. Branching Note

Before starting this module, make sure the branch includes the Workers/Labour work.

### If Workers/Labour has been merged into main

```bash
git checkout main
git pull origin main
git checkout -b tasks-work-orders-module
```

### If Workers/Labour has not yet been merged

```bash
git checkout workers-labour-module
git pull origin workers-labour-module
git checkout -b tasks-work-orders-module
```

Do not create this branch from a base that lacks Core/Admin, Users/Permissions, and Workers/Labour.

---

## 4. Module Objective

The objective of this module is to allow authorised users to create, assign, track, submit, review, and approve farm work.

The module should allow users to:

1. Create work orders.
2. Create tasks under work orders or as standalone tasks where appropriate.
3. Assign tasks to workers, labour teams, or system users.
4. Link tasks to farm locations such as fields, paddocks, warehouses, and sites.
5. Classify tasks by category and priority.
6. Track task status.
7. Record progress updates.
8. Upload simple task evidence where file upload support exists.
9. Submit task completion.
10. Approve, reject, or request correction.
11. View overdue and upcoming tasks.
12. Prepare task records for future crop, livestock, irrigation, inventory, labour costing, and reporting workflows.

---

## 5. Scope of This Branch

### In scope

This branch should implement:

- Tasks module provider/routes.
- Work order model and migration.
- Task model and migration.
- Task assignment model/table.
- Task update/progress model/table.
- Task checklist item model/table where practical.
- Basic task evidence/photo/document reference if existing attachment handling supports it, otherwise a placeholder.
- Work order list/create/view/edit/cancel.
- Task list/create/view/edit/cancel.
- Assign tasks to workers and teams.
- Submit task progress/completion.
- Approve/reject task completion.
- Basic task dashboard.
- Auth and permission protection.
- Navigation entry.
- Seeder updates for task permissions.
- Tests.
- README update.

### Out of scope

Do not implement:

- Crop cycle activity creation.
- Livestock health/treatment records.
- Inventory stock issue/deduction.
- Finance cost posting.
- Payroll.
- Advanced mobile offline task sync.
- GPS tracking.
- Route optimisation.
- IoT sensor-triggered tasks.
- Repeating task scheduler beyond simple due dates.
- Full notification engine unless already simple and safe.
- Advanced dashboards.

This branch should build task structure and workflow only.

---

## 6. Key Design Principle

Tasks must not be built as a generic to-do list.

They should be farm-operational records.

Every task should be able to answer:

- Which organisation does this task belong to?
- Which farm does this task belong to?
- What work needs to be done?
- Where is the work happening?
- Who assigned it?
- Who is responsible for doing it?
- When is it due?
- What is its current status?
- What evidence or updates exist?
- Who approved completion?
- Which future module object might this task relate to?

For now, tasks can link to Core/Admin objects such as fields, paddocks, warehouses, and sites. Future modules can add deeper links to crop cycles, animal groups, irrigation schedules, inventory requests, and finance records.

---

## 7. Core Entities

## 7.1 Work Order

A work order is a planned instruction or job package. It may contain one or more tasks.

Examples:

- Prepare Tomato Block A for planting.
- Vaccinate dairy herd.
- Repair irrigation pump.
- Harvest maize field.
- Clean poultry house.
- Apply fertiliser to Field B.

### Suggested table name

```text
ops_work_orders
```

### Suggested fields

```text
id
organization_id
farm_id
work_order_number
title
description
category
priority
status
location_type nullable
location_id nullable
requested_by nullable
created_by
approved_by nullable
approved_at nullable
start_date nullable
due_date nullable
completed_at nullable
cancelled_at nullable
cancellation_reason nullable
notes nullable
created_at
updated_at
soft_deletes
```

### Suggested `category` values

```text
general
crop
livestock
irrigation
inventory
maintenance
harvest
feeding
treatment
cleaning
security
other
```

### Suggested `priority` values

```text
low
normal
high
urgent
```

### Suggested `status` values

```text
draft
planned
assigned
in_progress
submitted
approved
completed
cancelled
rejected
needs_correction
```

### Notes

- `location_type` and `location_id` can be used for simple polymorphic location linking, but keep it controlled.
- Alternatively, use nullable `site_id`, `field_id`, `paddock_id`, `warehouse_id` columns for clarity. For a rookie-friendly project, explicit nullable columns may be easier to understand.
- Work order numbers should be generated consistently, e.g. `WO-2026-0001`.

---

## 7.2 Task

A task is an actionable unit of work.

A work order may have multiple tasks.

Example work order:

- Prepare Tomato Block A for planting.

Example tasks:

- Clear weeds.
- Plough field.
- Apply manure.
- Prepare beds.

### Suggested table name

```text
ops_tasks
```

### Suggested fields

```text
id
organization_id
farm_id
work_order_id nullable
task_number
title
description
category
priority
status
site_id nullable
field_id nullable
paddock_id nullable
warehouse_id nullable
assigned_by nullable
supervisor_user_id nullable
start_date nullable
due_date nullable
started_at nullable
submitted_at nullable
approved_at nullable
completed_at nullable
cancelled_at nullable
rejected_at nullable
approval_notes nullable
cancellation_reason nullable
created_by
updated_by nullable
approved_by nullable
created_at
updated_at
soft_deletes
```

### Suggested task `status` values

```text
draft
assigned
in_progress
submitted
needs_correction
approved
completed
cancelled
rejected
```

### Important note

For version 1, `completed` should usually follow approval. A worker may submit a task, but a supervisor/manager approves it.

Recommended flow:

```text
assigned → in_progress → submitted → approved/completed
```

If rejected:

```text
submitted → needs_correction → in_progress/submitted
```

---

## 7.3 Task Assignment

Represents who is assigned to a task.

A task can be assigned to:

- one worker,
- multiple workers,
- a labour team,
- a system user,
- or a combination depending on the workflow.

### Suggested table name

```text
ops_task_assignments
```

### Suggested fields

```text
id
organization_id
farm_id
task_id
assignment_type
worker_id nullable
team_id nullable
user_id nullable
role_on_task nullable
status
assigned_by
assigned_at
accepted_at nullable
started_at nullable
completed_at nullable
notes nullable
created_at
updated_at
```

### Suggested `assignment_type` values

```text
worker
team
user
```

### Suggested `status` values

```text
assigned
accepted
in_progress
submitted
completed
cancelled
```

### Important rule

At least one of `worker_id`, `team_id`, or `user_id` should be present depending on assignment type.

---

## 7.4 Task Update / Progress Log

Represents progress notes, status changes, issues, or completion notes.

### Suggested table name

```text
ops_task_updates
```

### Suggested fields

```text
id
organization_id
farm_id
task_id
user_id nullable
worker_id nullable
update_type
status_from nullable
status_to nullable
progress_percent nullable
quantity_done nullable
quantity_unit nullable
notes nullable
created_at
updated_at
```

### Suggested `update_type` values

```text
comment
progress
issue
status_change
submission
approval
rejection
correction_request
completion
```

---

## 7.5 Task Checklist Item

Represents checklist steps inside a task.

### Suggested table name

```text
ops_task_checklist_items
```

### Suggested fields

```text
id
task_id
label
is_required
is_completed
completed_by nullable
completed_at nullable
sort_order
created_at
updated_at
```

### Example

Task: Clean Poultry House A

Checklist:

- Remove old litter.
- Wash drinkers.
- Disinfect floor.
- Replace bedding.
- Upload completion photo.

---

## 7.6 Task Attachment / Evidence — Optional for This Branch

If the platform already has an attachment system, task updates may link to attachments.

If not, do not overbuild file management in this branch.

A placeholder can be documented for future task photos/evidence.

Suggested future table:

```text
ops_task_attachments
```

or reuse global attachments when ready.

---

## 8. Relationships

### WorkOrder model

Should have:

```php
organization()
farm()
tasks()
createdBy()
approvedBy()
```

### Task model

Should have:

```php
organization()
farm()
workOrder()
site()
field()
paddock()
warehouse()
assignments()
updates()
checklistItems()
assignedBy()
supervisor()
createdBy()
approvedBy()
```

### TaskAssignment model

Should have:

```php
task()
organization()
farm()
worker()
team()
user()
assignedBy()
```

### TaskUpdate model

Should have:

```php
task()
organization()
farm()
user()
worker()
```

---

## 9. Permissions

Add starter permissions for the Tasks / Work Orders module.

### Suggested permissions

```text
tasks.view
tasks.create
tasks.update
tasks.assign
tasks.submit
tasks.approve
tasks.cancel
tasks.manage
work-orders.view
work-orders.create
work-orders.update
work-orders.cancel
work-orders.manage
```

### Suggested default role assignments

Owner:

```text
all task permissions
```

System Admin:

```text
all task permissions
```

Farm Manager:

```text
tasks.view
tasks.create
tasks.update
tasks.assign
tasks.approve
tasks.cancel
tasks.manage
work-orders.view
work-orders.create
work-orders.update
work-orders.cancel
work-orders.manage
```

Agronomist:

```text
tasks.view
tasks.create
tasks.update
work-orders.view
```

Livestock Officer:

```text
tasks.view
tasks.create
tasks.update
work-orders.view
```

Storekeeper:

```text
tasks.view
```

Farm Hand:

```text
tasks.view
tasks.submit
```

Contractor:

```text
tasks.view
tasks.submit
```

Auditor:

```text
tasks.view
work-orders.view
```

These are starter defaults and can be adjusted later.

---

## 10. Routes

Suggested route prefix:

```text
/admin/tasks
```

Route names:

```text
tasks.*
```

### Suggested routes

```text
GET    /admin/tasks                               Task dashboard

GET    /admin/tasks/work-orders                   Work order list
GET    /admin/tasks/work-orders/create            Create work order form
POST   /admin/tasks/work-orders                   Store work order
GET    /admin/tasks/work-orders/{workOrder}       Work order detail
GET    /admin/tasks/work-orders/{workOrder}/edit  Edit work order form
PUT    /admin/tasks/work-orders/{workOrder}       Update work order
POST   /admin/tasks/work-orders/{workOrder}/cancel Cancel work order

GET    /admin/tasks/items                         Task list
GET    /admin/tasks/items/create                  Create task form
POST   /admin/tasks/items                         Store task
GET    /admin/tasks/items/{task}                  Task detail
GET    /admin/tasks/items/{task}/edit             Edit task form
PUT    /admin/tasks/items/{task}                  Update task
POST   /admin/tasks/items/{task}/assign           Assign task
POST   /admin/tasks/items/{task}/start            Mark task in progress
POST   /admin/tasks/items/{task}/submit           Submit task
POST   /admin/tasks/items/{task}/approve          Approve task
POST   /admin/tasks/items/{task}/reject           Reject/request correction
POST   /admin/tasks/items/{task}/cancel           Cancel task

POST   /admin/tasks/items/{task}/updates          Add task update
POST   /admin/tasks/items/{task}/checklist        Add checklist item
PATCH  /admin/tasks/checklist/{item}/toggle       Toggle checklist item
```

### Middleware

All routes should require:

```text
auth
appropriate task/work-order permission
```

Use existing permission middleware pattern from Users/Permissions.

---

## 11. Controllers

Suggested controllers:

```text
TaskDashboardController
WorkOrderController
TaskController
TaskAssignmentController
TaskUpdateController
TaskChecklistController
```

Keep controllers simple.

Do not hide complex business logic inside views.

---

## 12. Form Requests / Validation

Suggested request classes:

```text
StoreWorkOrderRequest
UpdateWorkOrderRequest
StoreTaskRequest
UpdateTaskRequest
AssignTaskRequest
StoreTaskUpdateRequest
StoreTaskChecklistItemRequest
```

If the project has not standardised request classes yet, controller validation is acceptable for this branch, but request classes are cleaner.

### Work order validation

Required:

- organization_id.
- farm_id.
- title.
- category.
- priority.
- status.

Optional:

- description.
- site/field/paddock/warehouse/location.
- start_date.
- due_date.
- notes.

### Task validation

Required:

- organization_id.
- farm_id.
- title.
- category.
- priority.
- status.

Optional:

- work_order_id.
- site_id.
- field_id.
- paddock_id.
- warehouse_id.
- supervisor_user_id.
- start_date.
- due_date.
- checklist.
- notes.

### Assignment validation

Required:

- task_id.
- assignment_type.
- at least one assignee depending on assignment_type.

Rules:

- Worker must belong to selected farm.
- Team must belong to selected farm.
- User must have membership in selected organisation/farm where practical.

### Task update validation

Required:

- task_id.
- update_type.

Optional:

- notes.
- progress_percent.
- quantity_done.
- quantity_unit.

---

## 13. Views / UI

Keep UI simple and operational.

### 13.1 Task dashboard

Should show links/cards for:

- Work Orders.
- Tasks.
- Assigned Tasks.
- Overdue Tasks.
- Pending Approval.

Optional simple metrics:

- Total open tasks.
- Due today.
- Overdue.
- Submitted for approval.

Do not build advanced analytics yet.

### 13.2 Work order list

Columns:

- Work order number.
- Title.
- Farm.
- Category.
- Priority.
- Status.
- Due date.
- Task count.
- Actions.

Filters if easy:

- Farm.
- Status.
- Category.
- Priority.
- Due date.

### 13.3 Work order detail

Sections:

- Work order summary.
- Linked location.
- Tasks under work order.
- Status history placeholder.
- Notes.

### 13.4 Work order create/edit form

Fields:

- Organisation.
- Farm.
- Title.
- Description.
- Category.
- Priority.
- Status.
- Location fields.
- Start date.
- Due date.
- Notes.

### 13.5 Task list

Columns:

- Task number.
- Title.
- Farm.
- Location.
- Category.
- Priority.
- Status.
- Assigned to.
- Due date.
- Actions.

### 13.6 Task detail

Sections:

- Task summary.
- Assignment summary.
- Location.
- Checklist.
- Updates/progress log.
- Approval section.
- Notes.

### 13.7 Task create/edit form

Fields:

- Organisation.
- Farm.
- Work order optional.
- Title.
- Description.
- Category.
- Priority.
- Status.
- Site/field/paddock/warehouse optional.
- Supervisor.
- Start date.
- Due date.
- Notes.

### 13.8 Assignment form

Fields:

- Assignment type.
- Worker.
- Team.
- User.
- Notes.

### 13.9 Update/progress form

Fields:

- Update type.
- Progress percent.
- Quantity done.
- Unit.
- Notes.

### 13.10 Approval section

Actions:

- Approve.
- Reject / request correction.
- Cancel.

---

## 14. Navigation

Add Tasks / Work Orders to the admin/module navigation if the user has relevant permissions.

Suggested label:

```text
Tasks / Work Orders
```

Suggested left menu inside module:

```text
Task Dashboard
Work Orders
Tasks
Assigned Tasks
Pending Approval
```

For this branch, a simple navigation link is enough.

Do not show task admin links to users without task permissions.

Backend routes must still enforce permissions.

---

## 15. Business Rules

### Rule 1: Tasks belong to organisation and farm

A task cannot exist without organisation and farm scope.

### Rule 2: Tasks can link to farm locations

Tasks may link to site, field, paddock, or warehouse.

### Rule 3: Tasks can be standalone or belong to a work order

A small task may not need a work order. A larger job may have multiple tasks under one work order.

### Rule 4: Do not hard delete tasks

Tasks should be cancelled or archived, not deleted, because they are operational history.

### Rule 5: Farm hands submit; managers approve

A worker/farm hand may submit task completion, but a manager/supervisor should approve it.

### Rule 6: Assignment must respect farm scope

Do not assign a worker/team from Farm A to a task in Farm B unless explicitly allowed later.

### Rule 7: Task status changes should create updates

When a task status changes, create a task update/log entry where practical.

### Rule 8: Do not create crop/livestock/inventory/finance side effects yet

A fertiliser task may exist, but it should not deduct fertiliser stock yet in this branch.

An animal treatment task may exist, but it should not create animal treatment records yet.

### Rule 9: Due dates matter

Overdue task logic should be simple:

```text
status is not completed/cancelled/approved and due_date < today
```

### Rule 10: Task numbers should be generated

Use a simple readable format such as:

```text
TASK-2026-0001
WO-2026-0001
```

Do not overbuild numbering yet.

---

## 16. Integration with Other Modules

### 16.1 Core/Admin

Tasks depend on:

- organisations,
- farms,
- sites,
- fields,
- paddocks,
- warehouses.

### 16.2 Users/Permissions

Task access depends on permissions and user membership scope.

### 16.3 Workers/Labour

Tasks can be assigned to:

- labour workers,
- labour teams,
- users.

### 16.4 Future Crops module

Crop-related tasks will later be converted into or linked with crop activities.

Examples:

- planting task,
- fertiliser application task,
- spraying task,
- weeding task,
- harvesting task.

Do not implement crop activity side effects yet.

### 16.5 Future Livestock module

Livestock-related tasks will later link to animal events.

Examples:

- feeding task,
- vaccination task,
- treatment task,
- movement task,
- cleaning task.

Do not implement animal event side effects yet.

### 16.6 Future Inventory module

Tasks may later request or consume inputs.

Examples:

- fertiliser,
- chemicals,
- medicine,
- feed,
- fuel.

Do not implement stock movement yet.

### 16.7 Future Finance module

Tasks may later post labour, input, equipment, or fuel cost.

Do not implement cost posting yet.

---

## 17. Seed Data

Add demo task seed data only in local/testing environments.

Suggested demo data:

```text
Work Order: Prepare Tomato Block B for planting
Tasks:
- Clear weeds
- Apply manure
- Prepare beds

Work Order: Clean Poultry House A
Tasks:
- Remove old litter
- Wash drinkers
- Disinfect floor
```

Important:

Do not create production demo tasks.

---

## 18. Tests Required

### Migration/model tests

- Work order can be created under organisation/farm.
- Task can be created under organisation/farm.
- Task can belong to work order.
- Task can link to field/paddock/warehouse where applicable.
- Task can have assignments.
- Task can have updates.
- Task can have checklist items.

### Route protection tests

- Guest cannot access `/admin/tasks`.
- Authenticated user without task permission cannot access `/admin/tasks`.
- Admin/authorised user can access `/admin/tasks`.

### CRUD tests

- Authorised user can create work order.
- Authorised user can update work order.
- Authorised user can cancel work order.
- Authorised user can create task.
- Authorised user can update task.
- Authorised user can assign task to worker/team.
- Authorised user can submit task update.
- Authorised user can approve task.
- Authorised user can reject/request correction.

### Validation tests

- Work order requires organisation/farm/title.
- Task requires organisation/farm/title.
- Worker assignment must belong to same farm.
- Team assignment must belong to same farm.
- Invalid status is rejected.

### Seeder tests

- Task permissions are seeded.
- Demo tasks are only seeded in local/testing if included.

---

## 19. Acceptance Criteria

This branch is complete when:

1. Tasks module exists under `app/Modules/Tasks`.
2. Routes exist under `/admin/tasks`.
3. Task routes are protected by auth and permissions.
4. Work order CRUD works without hard deletes.
5. Task CRUD works without hard deletes.
6. Tasks can be assigned to workers, teams, or users.
7. Task progress updates can be recorded.
8. Task checklist items can be created/toggled where implemented.
9. Task submit/approve/reject/cancel flows work.
10. Task permissions are seeded.
11. Navigation appears only for authorised users.
12. Tests pass.
13. README/module documentation is updated.
14. No crop cycle, animal record, inventory movement, finance costing, payroll, or advanced offline logic is implemented.

---

## 20. Recommended Build Order

1. Confirm branch includes Core/Admin, Users/Permissions, and Workers/Labour.
2. Inspect current permission middleware and seeding structure.
3. Add task/work order permissions to seeder.
4. Create Tasks module provider/routes.
5. Create migrations for work orders, tasks, task assignments, task updates, checklist items.
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

## 21. Codex Prompt for Tasks / Work Orders Branch

Use this prompt after confirming the branch is correctly based on Workers/Labour.

```text
You are working on the `tasks-work-orders-module` branch of a Laravel 13 modular monolith project for a mixed-farm management platform.

The Laravel foundation, Core/Admin module, Users/Permissions module, and Workers/Labour module already exist. Do not rebuild them.

This branch is only for the Tasks / Work Orders foundation.

Do not implement crop cycles, animal records, inventory stock movement, finance/costing, payroll, irrigation business logic, sales, or advanced dashboards. Do not implement mobile/offline sync.

First inspect the project and report:
1. Current Core/Admin models for organizations, farms, sites, fields, paddocks, and warehouses.
2. Current Users/Permissions models, middleware, roles, permissions, and seeding pattern.
3. Current Workers/Labour models for workers, teams, and attendance.
4. Existing app/Modules/Tasks folder state.
5. Existing navigation pattern.
6. Existing seeder pattern and local/testing demo data safeguards.
7. Any risks or conflicts before implementation.

Then implement the Tasks / Work Orders module foundation:

1. Module structure:
   - Use app/Modules/Tasks.
   - Add provider/routes/controllers/models/views/tests/README as needed.

2. Migrations and models:
   - ops_work_orders
   - ops_tasks
   - ops_task_assignments
   - ops_task_updates
   - ops_task_checklist_items

3. Work order requirements:
   - Work order belongs to organization and farm.
   - Work order has work_order_number, title, description, category, priority, status, optional location reference, start_date, due_date, completed/cancelled fields, notes.
   - Work orders should be cancelled by status, not hard deleted.

4. Task requirements:
   - Task belongs to organization and farm.
   - Task optionally belongs to a work order.
   - Task can optionally link to site, field, paddock, or warehouse.
   - Task has task_number, title, description, category, priority, status, due dates, supervisor/assigned metadata, approval fields, notes.
   - Tasks should be cancelled by status, not hard deleted.

5. Assignment requirements:
   - Task can be assigned to worker, labour team, or user.
   - Worker/team must belong to the same farm as the task.
   - Assignment stores assignment_type, assignee reference, assigned_by, assigned_at, status, notes.

6. Task update requirements:
   - Users can add progress/update records to a task.
   - Status changes should create update/log records where practical.
   - Updates can capture update_type, progress_percent, quantity_done, quantity_unit, notes.

7. Checklist requirements:
   - Task can have checklist items.
   - Checklist items can be toggled completed/incomplete.
   - Keep this simple.

8. Permissions:
   - Seed starter permissions:
     tasks.view, tasks.create, tasks.update, tasks.assign, tasks.submit, tasks.approve, tasks.cancel, tasks.manage,
     work-orders.view, work-orders.create, work-orders.update, work-orders.cancel, work-orders.manage.
   - Assign reasonable defaults:
     owner and system-admin get all.
     farm-manager gets task/work-order management starter permissions.
     agronomist and livestock-officer get task view/create/update starter permissions.
     farm-hand and contractor get tasks.view and tasks.submit.
     auditor gets view permissions.
   - Do not break existing permissions.

9. Routes:
   - Use /admin/tasks prefix.
   - Use tasks.* route names.
   - Protect all task routes behind auth and appropriate permissions.

10. Views:
   - Add simple Task dashboard.
   - Add work order list/create/view/edit/cancel.
   - Add task list/create/view/edit/cancel.
   - Add task assignment form/action.
   - Add task updates/progress section.
   - Add task submit/approve/reject flow.
   - Add simple checklist item creation/toggle where practical.
   - Keep UI simple and consistent with existing Blade layout.

11. Navigation:
   - Add Tasks / Work Orders admin navigation link only when user has task permissions.
   - Do not rely only on hidden links; enforce backend permission checks.

12. Demo data:
   - If demo work orders/tasks are seeded, only create them in local/testing environments.
   - Do not seed demo tasks in production.

13. Tests:
   - Guest cannot access /admin/tasks.
   - User without task permission cannot access /admin/tasks.
   - Admin can access /admin/tasks.
   - Task permissions are seeded.
   - Work order can be created, updated, cancelled.
   - Task can be created, updated, cancelled.
   - Task can be assigned to a worker/team/user.
   - Assignment rejects worker/team from another farm.
   - Task update can be recorded.
   - Task can be submitted and approved.
   - Task can be rejected/request correction.
   - Validation rejects missing organization/farm/title.

14. Documentation:
   - Update app/Modules/Tasks/README.md with entities, routes, permissions, known limitations, and follow-up work.

Constraints:
- Do not implement crop activity creation.
- Do not implement livestock activity creation.
- Do not implement inventory stock movement or stock issue.
- Do not implement finance/cost posting.
- Do not implement payroll.
- Do not implement irrigation business logic.
- Do not add unnecessary packages.
- Do not commit .env, vendor, node_modules, local databases, logs, cache files, build output, or generated compiled views.

After implementation, run:
- composer install if needed
- npm install if needed
- php artisan migrate:fresh --seed
- php artisan test
- npm run build
- php artisan route:list --path=admin/tasks

Then summarize exactly what changed, what tests passed, and any risks/follow-up work.
```

---

## 22. Commit Message

If validation passes, use:

```bash
git add --dry-run .
git add .
git commit -m "Build Tasks and Work Orders foundation"
git push origin tasks-work-orders-module
```

---

## 23. Follow-Up After This Branch

After this module is complete, the next module should be:

```text
inventory-module
```

Reason:

Inventory should come before Crops and Livestock because crop and animal operations will consume inputs such as seed, fertiliser, chemicals, medicine, feed, and fuel.

Known future task follow-ups:

- Farm hand simplified task screen.
- Mobile/offline task update flow.
- Task recurrence/scheduling.
- Notifications/reminders.
- Task evidence attachments/photos.
- Task-to-crop activity conversion.
- Task-to-livestock event conversion.
- Task-to-inventory request workflow.
- Task labour cost posting.
- Task productivity dashboards.

