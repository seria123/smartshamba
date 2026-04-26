# SmartShamba Agent Memory

## Project Structure
- Laravel 10 application with Filament v3 admin panel
- MySQL database with role-based access control (user, manager, admin)
- Livestock/farm management system with harvest/food stock tracking

## Key Patterns
- User roles: `user` (standard), `manager` (operational), `admin` (super-admin)
- Role-based access via `RoleMiddleware` applied to route groups
- Filament admin panel at `/admin` with role-based menu visibility
- Feed types stored in `feed_types` table with default_unit and min_threshold

## Database Schema Notes
- Users table: role column is string(50) to support multiple roles
- Feed types: Hay, Maize Bran, Silage, Chicken Feed, Concentrate
- Livestock disease tracking with severity levels and treatment status

## Common Issues Fixed
- Food stock dropdown: Ensure `LivestockFeedSeeder` runs to populate feed types
- User roles: Admin users converted to manager role; create new admin users as needed
- Filament v3: Uses middleware-based auth instead of gate() method