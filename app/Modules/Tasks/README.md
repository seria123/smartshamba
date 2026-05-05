# Tasks / Work Orders Module

This module provides the operational task foundation for SmartShamba. It supports work orders, standalone tasks, assignment to workers/teams/users, simple progress updates, checklist items, and submit/review flows.

It intentionally does not create crop activities, livestock events, inventory stock movements, finance cost postings, payroll records, irrigation automation, sales records, or mobile/offline sync.

## Structure

- `Providers/TasksServiceProvider.php` registers module routes and Blade views.
- `routes/web.php` exposes `/admin/tasks` with `tasks.*` route names.
- `Http/Controllers` contains dashboard, work order, task, assignment, update, and checklist controllers.
- `Models` contains `OpsWorkOrder`, `OpsTask`, `OpsTaskAssignment`, `OpsTaskUpdate`, and `OpsTaskChecklistItem`.
- Views live in `resources/views/tasks`.
- Tests live in `tests/Feature/TasksWorkOrdersModuleTest.php`.

## Entities

- `ops_work_orders`: organization/farm-scoped work packages with generated work order number, category, priority, status, optional Core location links, requested/created/approved user metadata, dates, cancellation fields, notes, and soft deletes.
- `ops_tasks`: organization/farm-scoped actionable work records with generated task number, optional work order, optional Core location links, category, priority, status, supervisor/approval metadata, dates, cancellation fields, notes, and soft deletes.
- `ops_task_assignments`: task assignees by worker, labour team, or user. Worker and team assignments are constrained to the task farm in controller validation.
- `ops_task_updates`: progress comments, status logs, submissions, approval/rejection notes, quantities, and progress percentages.
- `ops_task_checklist_items`: simple checklist steps that can be toggled complete/incomplete.

## Routes

All routes require authentication and permission middleware.

- `GET /admin/tasks` -> `tasks.dashboard`
- `GET /admin/tasks/work-orders` -> `tasks.work-orders.index`
- `GET|POST /admin/tasks/work-orders/create` and `/admin/tasks/work-orders` for work order creation
- `GET|PUT /admin/tasks/work-orders/{workOrder}/edit` for updates
- `POST /admin/tasks/work-orders/{workOrder}/cancel` for cancellation
- `GET /admin/tasks/items` -> `tasks.items.index`
- `GET|POST /admin/tasks/items/create` and `/admin/tasks/items` for task creation
- `GET|PUT /admin/tasks/items/{task}/edit` for updates
- `POST /admin/tasks/items/{task}/assign` for assignments
- `POST /admin/tasks/items/{task}/start` for in-progress status
- `POST /admin/tasks/items/{task}/submit` for completion submission
- `POST /admin/tasks/items/{task}/approve` for approval/completion
- `POST /admin/tasks/items/{task}/reject` for correction request
- `POST /admin/tasks/items/{task}/cancel` for cancellation
- `POST /admin/tasks/items/{task}/updates` for progress updates
- `POST /admin/tasks/items/{task}/checklist` for checklist creation
- `PATCH /admin/tasks/checklist/{item}/toggle` for checklist toggling

## Permissions

Seeded permissions:

- `tasks.view`
- `tasks.create`
- `tasks.update`
- `tasks.assign`
- `tasks.submit`
- `tasks.approve`
- `tasks.cancel`
- `tasks.manage`
- `work-orders.view`
- `work-orders.create`
- `work-orders.update`
- `work-orders.cancel`
- `work-orders.manage`

Default role assignments:

- `owner` and `system-admin`: all seeded permissions.
- `farm-manager`: task/work-order management starter permissions.
- `agronomist` and `livestock-officer`: task view/create/update plus work-order view.
- `farm-hand` and `contractor`: task view and submit.
- `auditor`: view permissions.

## Demo Data

`TasksWorkOrdersSeeder` creates demo work orders and tasks only in `local` and `testing` environments. It exits without changes in production-like environments.

## Known Limitations

- No task recurrence or scheduling engine.
- No notification engine.
- No attachment/file upload implementation yet.
- No crop, livestock, inventory, finance, payroll, irrigation, sales, or offline side effects.
- Forms use simple selects instead of dynamic farm-scoped filtering.
- Approval is intentionally simple: approve marks the task `completed`; reject marks it `needs_correction`.
