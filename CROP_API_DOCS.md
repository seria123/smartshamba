# Crop Management Module - API Documentation

## Base URL
```
http://localhost:8000/api
```

---

## Endpoints Overview

| Feature | Method | Endpoint | Auth |
|--------|--------|----------|------|
| List crops | GET | `/crops` | No |
| Get crop | GET | `/crops/{id}` | No |
| Create crop | POST | `/crops` | Yes |
| Update crop | PUT | `/crops/{id}` | Yes |
| Delete crop | DELETE | `/crops/{id}` | Yes |
| Get categories | GET | `/crops-categories` | No |
| Get season types | GET | `/crops-season-types` | No |
| Get calendar | GET | `/crops/calendar` | No |
| Add season | POST | `/crops/{id}/seasons` | Yes |
| Get rotations | GET | `/crops/{id}/rotations` | No |
| Suggest rotations | GET | `/crops/{id}/rotations/suggest` | No |
| Add rotation | POST | `/crops/{id}/rotations` | Yes |
| Get yield estimations | GET | `/yield-estimations` | Yes |
| Create yield estimation | POST | `/yield-estimations` | Yes |
| Update yield estimation | PUT | `/yield-estimations/{id}` | Yes |

---

## Crop Selection

### 1. List Crops
```
GET /crops?category=grain&season_type=long_rain&search=maize
```

**Response (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Maize",
            "category": "grain",
            "description": "Common maize (corn) - staple food crop",
            "variety": "Composite Hybrid H614",
            "days_to_maturity": 120,
            "average_yield_per_hectare": "6000.00",
            "yield_unit": "kg",
            "season_type": "long_rain",
            "min_temperature": "15.00",
            "max_temperature": "35.00",
            "seasons": [
                {
                    "id": 1,
                    "name": "Masika (Long Rain)",
                    "season_period": "long_rain",
                    "planting_start_date": "2025-03-01",
                    "planting_end_date": "2025-04-15",
                    "expected_harvest_start": "2025-07-01",
                    "expected_harvest_end": "2025-09-15"
                }
            ]
        }
    ],
    "first_page_url": "/crops?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "/crops?page=1",
    "next_page_url": null,
    "path": "/crops",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

### 2. Get Single Crop
```
GET /crops/1
```

**Response (200):**
```json
{
    "crop": {
        "id": 1,
        "name": "Maize",
        "category": "grain",
        "description": "Common maize (corn) - staple food crop",
        "variety": "Composite Hybrid H614",
        "days_to_maturity": 120,
        "average_yield_per_hectare": "6000.00",
        "yield_unit": "kg",
        "season_type": "long_rain",
        "growth_stages": ["germination", "vegetative", "tasseling", "grain_fill", "maturity"],
        "soil_requirements": {
            "type": "loamy",
            "ph_range": "5.8-7.0"
        },
        "water_requirements": {
            "mm_per_season": 500,
            "critical_stages": ["tasseling", "grain_fill"]
        },
        "min_temperature": "15.00",
        "max_temperature": "35.00",
        "optimal_ph_min": null,
        "optimal_ph_max": null,
        "seasons": [],
        "rotations": [],
        "yield_estimations": []
    }
}
```

### 3. Get Categories
```
GET /crops-categories
```

**Response (200):**
```json
{
    "categories": {
        "vegetable": "Vegetables",
        "fruit": "Fruits",
        "grain": "Grains",
        "legume": "Legumes",
        "root": "Root Crops",
        "tubers": "Tubers",
        "other": "Other"
    }
}
```

### 4. Get Season Types
```
GET /crops-season-types
```

**Response (200):**
```json
{
    "season_types": {
        "short_rain": "Short Rain Season (Vuli)",
        "long_rain": "Long Rain Season (Masika)",
        "all_season": "All Season"
    }
}
```

---

## Seasonal Calendar

### 5. Get Crop Calendar
```
GET /crops/calendar?season_period=long_rain
```

**Response (200):**
```json
{
    "calendar": [
        {
            "id": 1,
            "crop_id": 1,
            "name": "Masika (Long Rain)",
            "season_period": "long_rain",
            "planting_start_date": "2025-03-01",
            "planting_end_date": "2025-04-15",
            "expected_harvest_start": "2025-07-01",
            "expected_harvest_end": "2025-09-15",
            "is_optimal": true,
            "crop": {
                "id": 1,
                "name": "Maize",
                "category": "grain"
            }
        }
    ]
}
```

### 6. Add Crop Season (Admin)
```
POST /crops/1/seasons
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Irrigation Season",
    "season_period": "all_season",
    "planting_start_date": "2025-01-01",
    "planting_end_date": "2025-02-28",
    "expected_harvest_start": "2025-05-01",
    "expected_harvest_end": "2025-07-31",
    "is_optimal": true
}
```

**Response (201):**
```json
{
    "message": "Crop season added successfully",
    "season": {
        "id": 2,
        "crop_id": 1,
        "name": "Irrigation Season",
        "season_period": "all_season",
        "planting_start_date": "2025-01-01",
        "planting_end_date": "2025-02-28",
        "expected_harvest_start": "2025-05-01",
        "expected_harvest_end": "2025-07-31",
        "is_optimal": true
    }
}
```

---

## Crop Rotation

### 7. Get Crop Rotations
```
GET /crops/1/rotations
```

**Response (200):**
```json
{
    "rotations": [
        {
            "id": 1,
            "crop_id": 1,
            "previous_crop_id": 2,
            "sequence_order": 1,
            "yield_benefit_percentage": "15.00",
            "benefits": "Nitrogen fixation improves soil fertility",
            "risks": null,
            "recommendations": null,
            "previous_crop": {
                "id": 2,
                "name": "Beans"
            }
        }
    ]
}
```

### 8. Suggest Rotations
```
GET /crops/1/rotations/suggest
```

**Response (200):**
```json
{
    "suggestions": [
        {
            "id": 1,
            "crop_id": 1,
            "previous_crop_id": 2,
            "sequence_order": 1,
            "yield_benefit_percentage": "15.00",
            "benefits": "Nitrogen fixation improves soil fertility",
            "previous_crop": {
                "id": 2,
                "name": "Beans"
            }
        }
    ]
}
```

### 9. Add Rotation (Admin)
```
POST /crops/1/rotations
Authorization: Bearer {token}
Content-Type: application/json

{
    "previous_crop_id": 3,
    "sequence_order": 2,
    "yield_benefit_percentage": 10,
    "benefits": "Improves soil structure",
    "recommendations": "Till soil after harvest"
}
```

---

## Yield Estimation

### 10. Get Yield Estimations
```
GET /yield-estimations?season=long_rain&year=2025
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "crop_id": 1,
            "farmer_id": 1,
            "field_id": 1,
            "hectares": "5.00",
            "season": "long_rain",
            "year": 2025,
            "estimated_yield": "30000.00",
            "actual_yield": null,
            "yield_per_hectare": "6000.00",
            "yield_unit": "kg",
            "estimated_income": "450000.00",
            "actual_income": null,
            "notes": "Expected good harvest",
            "status": "planned",
            "crop": {
                "id": 1,
                "name": "Maize"
            },
            "field": {
                "id": 1,
                "name": "North Field"
            }
        }
    ],
    "total": 1
}
```

### 11. Create Yield Estimation
```
POST /yield-estimations
Authorization: Bearer {token}
Content-Type: application/json

{
    "crop_id": 1,
    "field_id": 1,
    "hectares": 5,
    "season": "long_rain",
    "year": 2025,
    "notes": "Planning to plant in March"
}
```

**Response (201):**
```json
{
    "message": "Yield estimation created",
    "estimation": {
        "id": 1,
        "crop_id": 1,
        "farmer_id": 1,
        "field_id": 1,
        "hectares": "5.00",
        "season": "long_rain",
        "year": 2025,
        "estimated_yield": "30000.00",
        "yield_per_hectare": "6000.00",
        "yield_unit": "kg",
        "status": "planned",
        "crop": {
            "id": 1,
            "name": "Maize"
        }
    }
}
```

### 12. Update Yield Estimation (After Harvest)
```
PUT /yield-estimations/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "actual_yield": 28000,
    "actual_income": 420000,
    "status": "harvested"
}
```

**Response (200):**
```json
{
    "message": "Yield estimation updated",
    "estimation": {
        "id": 1,
        "crop_id": 1,
        "farmer_id": 1,
        "hectares": "5.00",
        "estimated_yield": "30000.00",
        "actual_yield": "28000.00",
        "yield_per_hectare": "5600.00",
        "estimated_income": "450000.00",
        "actual_income": "420000.00",
        "status": "harvested",
        "crop": {
            "id": 1,
            "name": "Maize"
        }
    }
}
```

---

## Seed Command

```bash
# Seed the database with crops
php artisan db:seed --class=CropSeeder

# Or run all seeders
php artisan db:seed

# Fresh migrate and seed
php artisan migrate:fresh --seed
```

---

## Example Workflows

### Farmer Planning Next Season

```bash
# 1. Browse available crops
GET /crops?category=grain

# 2. Check seasonal calendar
GET /crops/calendar?season_period=long_rain

# 3. Get rotation suggestions for desired crop
GET /crops/1/rotations/suggest

# 4. Create yield estimation
POST /yield-estimations
{
    "crop_id": 1,
    "hectares": 3,
    "season": "long_rain",
    "year": 2025
}
```