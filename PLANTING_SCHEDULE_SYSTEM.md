# Planting Schedule & Crop Calendar System

## Overview

The Planting Schedule and Crop Calendar system provides comprehensive planning and tracking capabilities for farm crop management. It integrates with existing crops, crop cycles, fields, and farms.

## Features

### 1. Database Schema

**Table: `planting_schedules`**

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint (FK) | Owner/planter |
| crop_id | bigint (FK) | Linked crop |
| crop_cycle_id | bigint (FK nullable) | Optional linked crop cycle |
| field_id | bigint (FK nullable) | Associated field |
| farm_id | bigint (FK nullable) | Associated farm |
| planting_date | date | Primary planting date |
| planting_window_start | date (nullable) | Earliest recommended planting |
| planting_window_end | date (nullable) | Latest recommended planting |
| expected_harvest_date | date (nullable) | Predicted harvest |
| actual_harvest_date | date (nullable) | Actual harvest |
| estimated_quantity | decimal (nullable) | Expected yield |
| quantity_unit | string | Unit (kg, ton, lb, etc.) |
| actual_quantity | decimal (nullable) | Actual harvested |
| status | enum | planned, planted, growing, ready_for_harvest, harvested, cancelled |
| season | enum | spring, summer, fall, winter, year_round |
| variety | string (nullable) | Specific variety |
| notes | text (nullable) | Additional notes |
| completion_percentage | integer | 0-100 progress |
| current_stage | string (nullable) | Growth stage name |
| created_at / updated_at | timestamps |

### 2. Models

**PlantingSchedule Model** (`app/Models/PlantingSchedule.php`)

Relationships:
- `user()` → User (owner)
- `crop()` → Crop
- `cropCycle()` → CropCycle (optional)
- `field()` → Field
- `farm()` → Farm

Accessors:
- `days_until_planting` → int
- `days_until_harvest` → ?int
- `is_overdue` → bool
- `is_in_planting_window` → bool
- `status_color` → string (for UI)
- `progress` → int
- `formatted_planting_window` → string

Scopes:
- `upcoming()` → unplanted scheduled for future
- `active()` → currently growing (planted/growing/ready)
- `bySeason($season)` → filter by season
- `plantedBetween($start, $end)` → date range

### 3. Web Routes

```php
GET    /planting-schedules              → index()
GET    /planting-schedules/calendar     → calendar()
GET    /planting-schedules/create       → create()
POST   /planting-schedules              → store()
GET    /planting-schedules/{id}/edit    → edit()
PUT    /planting-schedules/{id}         → update()
DELETE /planting-schedules/{id}         → destroy()
GET    /planting-schedules/upcoming     → upcoming() [JSON API]
```

### 4. Controllers

**PlantingScheduleController** (`app/Http/Controllers/PlantingScheduleController.php`)

- `index()` → List view with filters, stats, upcoming/active sections
- `calendar()` → Full calendar view with monthly grid and timeline
- `create()` → Show form
- `store()` → Validate & create
- `edit()` → Show edit form
- `update()` → Validate & update
- `destroy()` → Delete
- `upcoming()` → JSON API for upcoming plantings

### 5. Views

**Front-end (Blade Templates)**

- `resources/views/planting_schedules/index.blade.php`
  - Stats cards (total, active, upcoming, seasonal)
  - Filter form (crop, field, farm, season, status)
  - Upcoming plantings alert
  - Main list with pagination
  - Sidebar with upcoming & active summaries
  - Quick actions

- `resources/views/planting_schedules/calendar.blade.php`
  - Monthly overview grid
  - Color-coded status dots
  - Timeline (Gantt-style) view
  - Legend

- `resources/views/planting_schedules/create.blade.php`
  - Multi-section form: Crop & Location, Planting Schedule, Yield Estimates, Status & Progress, Notes
  - Auto-calculations via JavaScript

- `resources/views/planting_schedules/edit.blade.php`
  - Same as create, pre-filled with existing data
  - Delete option

**Admin Panel (Filament)**

- Resource: `app/Filament/Resources/PlantingScheduleResource.php`
  - Table view with status badges, filters (status, season, crop, field, farm, upcoming, overdue, active)
  - Create/Edit forms with organized sections
  
- Page: `app/Filament/Pages/PlantingCalendar.php`
  - FullCalendar.js integration
  - Interactive calendar with modal details
  - Color-coded events by status
  - Stats summary

### 6. Navigation

**Front-end Sidebar** (`resources/views/components/layout/sidebar.blade.php`)

- Crops section now includes:
  - Crop Management (Crop Cycles)
  - Planting Schedule (list view)
  - Crop Calendar (calendar view)

**Admin Panel** (`app/Providers/Filament/AdminPanelProvider.php`)

Added Planning group with:
- Planting Schedule (CRUD resource)
- Crop Calendar (custom page)

### 7. Integration Points

- **Linked Crop Cycles**: Planting schedules can optionally link to existing crop cycles
- **Auto-farm assignment**: If field selected but farm not, farm is auto-assigned from field
- **User ownership**: Unauthenticated can't create; authenticated users auto-assigned
- **Filters**: Crop, field, farm, season, status
- **Status progression**: planned → planted → growing → ready_for_harvest → harvested

## Installation & Setup

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Seed sample data:
   ```bash
   php artisan db:seed --class=PlantingScheduleSeeder
   ```

3. Verify routes:
   ```bash
   php artisan route:list | grep planting
   ```

4. Clear caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## Usage

### Creating a Planting Schedule

1. Navigate to **Planting Schedule** in the sidebar
2. Click **Add Planting**
3. Select crop, field, and enter details
4. Set planting date and expected harvest
5. Optionally set planting window (flexible dates)
6. Save

### Viewing Calendar

1. Click **Crop Calendar** in sidebar, or
2. In admin panel → Planning → Crop Calendar
3. Interactive FullCalendar shows all schedules with color-coded status
4. Click any event for details and quick edit link

### Filtering & Searching

- Filter by crop, field, farm, season, or status
- View upcoming, overdue, or active only
- Search by crop name or variety

## Sample Data

The seeder creates 30 sample planting schedules with random:
- Crops from existing crops table
- Fields and farms
- Dates spanning past 6 months
- All possible statuses
- Realistic growth stages and progress percentages

## Customization

### Adding More Growth Stages

Edit the `current_stage` options in the form schema:
`resources/views/planting_schedules/create.blade.php` and `edit.blade.php`

### Changing Status Flow

Update the `status` enum in the migration and model casts.

### Custom Calendar Colors

Edit the color mapping in:
`app/Filament/Pages/PlantingCalendar.php` (event backgroundColor)
`resources/views/planting_schedules/calendar.blade.php` (legend)

## API Endpoints

### JSON API

`GET /planting-schedules/upcoming` → Returns upcoming plantings as JSON

Response structure:
```json
[
  {
    "id": 1,
    "crop": {"name": "Maize"},
    "field": {"name": "Field A"},
    "planting_date": "2026-05-15",
    "status": "planned",
    ...
  }
]
```

## Future Enhancements

- SMS/email reminders for planting windows
- Calendar export (iCal, Google Calendar)
- Planting recommendations based on weather
- Bulk import via CSV
- Recurring planting schedules
- Integration with tasks/activities
