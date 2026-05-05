# Workers/Labour Module

This module provides the foundation for farm labour administration in the SmartShamba modular monolith. It is intentionally limited to worker records, labour teams, and simple attendance capture.

## Structure

- `Providers/WorkersServiceProvider.php` registers module routes and Blade views.
- `routes/web.php` exposes the `/admin/labour` admin routes with `labour.*` route names.
- `Http/Controllers` contains dashboard, worker, team, and attendance controllers.
- `Models` contains `LabourWorker`, `LabourTeam`, and `LabourAttendanceRecord`.
- Views live under `resources/views/labour`.
- Feature tests live in `tests/Feature/WorkersLabourModuleTest.php`.

## Entities

- `labour_workers`: organization/farm-scoped worker profiles with optional linked user account, worker code, contact details, employment type, primary role, status, start date, rate type, default rate, and notes.
- `labour_teams`: organization/farm-scoped teams with optional supervisor worker, team type, status, and notes.
- `labour_team_worker`: many-to-many team membership for workers.
- `labour_attendance_records`: simple daily attendance for one worker, optional team, status, check-in/out, hours worked, recorder, and notes.

Workers and teams are deactivated by setting `status` to `inactive`; they are not hard deleted through the UI.

## Routes

All routes require authentication and backend permission checks.

- `GET /admin/labour` -> `labour.dashboard`
- `GET /admin/labour/workers` -> `labour.workers.index`
- `GET|POST /admin/labour/workers/create` and `/admin/labour/workers` for worker creation
- `GET|PUT /admin/labour/workers/{worker}/edit` for worker updates
- `POST /admin/labour/workers/{worker}/deactivate` for worker deactivation
- `GET /admin/labour/teams` -> `labour.teams.index`
- `GET|POST /admin/labour/teams/create` and `/admin/labour/teams` for team creation
- `GET|PUT /admin/labour/teams/{team}/edit` for team updates
- `POST|DELETE /admin/labour/teams/{team}/workers` for team membership
- `POST /admin/labour/teams/{team}/deactivate` for team deactivation
- `GET /admin/labour/attendance` -> `labour.attendance.index`
- `GET|POST /admin/labour/attendance/create` and `/admin/labour/attendance` for attendance recording
- `GET|PUT /admin/labour/attendance/{attendance}/edit` for attendance updates

## Permissions

Seeded labour permissions:

- `workers.view`
- `workers.create`
- `workers.update`
- `workers.deactivate`
- `workers.manage`
- `teams.view`
- `teams.create`
- `teams.update`
- `teams.deactivate`
- `attendance.view`
- `attendance.record`
- `attendance.update`

Default assignments:

- `owner` and `system-admin`: all seeded permissions.
- `farm-manager`: starter labour management permissions.
- `auditor`: labour view permissions only.

The navigation link appears only for users with `workers.view`, but route middleware enforces access on the backend.

## Demo Data

`WorkersLabourSeeder` creates one demo worker, one demo team, and one demo attendance record only in `local` and `testing` environments. It exits without changes in production-like environments.

## Known Limitations

- No payroll, costing, or wage posting.
- No task assignment.
- No crop-cycle or livestock labour posting.
- No inventory stock movement.
- No advanced attendance approval workflow.
- Forms use simple full-list selects; farm-aware filtering can be improved later.
