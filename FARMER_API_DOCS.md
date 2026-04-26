# Farmer Management Module - API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

### 1. Register Farmer
```
POST /auth/register
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+255712345678",
    "national_id": "ID123456789",
    "date_of_birth": "1985-06-15",
    "gender": "male",
    "address": "123 Main Street",
    "village": "Mvomero",
    "ward": "Ward 5",
    "district": "Morogoro",
    "region": "Morogoro",
    "latitude": -6.8232,
    "longitude": 37.6523,
    "farm_size_hectares": 5.5,
    "farm_type": "smallholder",
    "crop_history": ["Maize", "Beans"],
    "farming_methods": ["irrigation", "organic"]
}
```

**Response (201):**
```json
{
    "message": "Farmer registered successfully",
    "user": {...},
    "farmer": {...},
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### 2. Login
```
POST /auth/login
Content-Type: application/json

{
    "email": "john.doe@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {...},
    "farmer": {...},
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### 3. Logout
```
POST /auth/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Logged out successfully"
}
```

## Profile Management

### 4. Get Profile
```
GET /profile
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "farmer": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "phone": "+255712345678",
        "district": "Morogoro",
        "region": "Morogoro",
        ...
    }
}
```

### 5. Update Profile
```
PUT /profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe Updated",
    "phone": "+255798765432",
    "address": "New Address"
}
```

**Response (200):**
```json
{
    "message": "Profile updated successfully",
    "farmer": {...}
}
```

## Farm Details

### 6. Update Farm Details
```
PUT /farm-details
Authorization: Bearer {token}
Content-Type: application/json

{
    "farm_size_hectares": 10.0,
    "farm_type": "medium",
    "latitude": -6.8232,
    "longitude": 37.6523,
    "farming_methods": ["irrigation", "organic", "greenhouse"]
}
```

**Response (200):**
```json
{
    "message": "Farm details updated successfully",
    "farmer": {...}
}
```

## Crop History

### 7. Get Crop History
```
GET /crop-history
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "crop_histories": [
        {
            "id": 1,
            "crop_name": "Maize",
            "year": 2024,
            "hectares": 3.0,
            "expected_yield": 3000,
            "actual_yield": 2800,
            "income": 560000
        }
    ]
}
```

### 8. Add Crop History
```
POST /crop-history
Authorization: Bearer {token}
Content-Type: application/json

{
    "crop_name": "Beans",
    "year": 2024,
    "hectares": 2.5,
    "expected_yield": 1500,
    "actual_yield": 1400,
    "yield_unit": "kg",
    "income": 280000,
    "notes": "Good harvest season"
}
```

**Response (201):**
```json
{
    "message": "Crop history added successfully",
    "crop_history": {...}
}
```

### 9. Update Crop History
```
PUT /crop-history/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "actual_yield": 1500,
    "income": 300000
}
```

### 10. Delete Crop History
```
DELETE /crop-history/{id}
Authorization: Bearer {token}
```

## Document Upload

### 11. Upload Document
```
POST /documents
Authorization: Bearer {token}
Content-Type: multipart/form-data

- document: (file)
- title: "National ID Copy"
- document_type: "national_id"
- description: "Valid national ID document"
```

**Response (201):**
```json
{
    "message": "Document uploaded successfully",
    "document": {
        "id": 1,
        "title": "National ID Copy",
        "document_type": "national_id",
        "file_name": "document.pdf",
        "file_type": "application/pdf",
        "file_size": 1024000
    }
}
```

### 12. Get Documents
```
GET /documents
Authorization: Bearer {token}
```

### 13. Delete Document
```
DELETE /documents/{id}
Authorization: Bearer {token}
```

## Public Endpoints

### 14. List Farmers (Admin/Public)
```
GET /farmers?region=Morogoro&district=Morogoro&farm_type=smallholder&is_active=true
```

### 15. Get Farmer Details
```
GET /farmers/{id}
```

---

## Environment Setup Required

```bash
# Install Sanctum
composer require laravel/sanctum

# Publish Sanctum config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Add Sanctum guard to config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

---

## Testing with cURL

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"first_name":"John","last_name":"Doe","email":"john@test.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@test.com","password":"password123"}'

# Get Profile (with token)
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Upload Document
curl -X POST http://localhost:8000/api/documents \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "document=@/path/to/file.pdf" \
  -F "title=My Document" \
  -F "document_type=land_title"
```