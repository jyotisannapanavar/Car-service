# Car Service / Car Wash Management System

A complete Car Service and Car Wash Management System built with Laravel, featuring RESTful APIs, role-based access control, and comprehensive business logic for managing organizations, branches, customers, vehicles, services, and pricing.

## Tech Stack

- Backend: Laravel 10.x (LTS)
- Database: MySQL / SQLite
- Authentication: Laravel Sanctum (API-based)
- Architecture: MVC + Service Layer
- API Design: RESTful APIs with versioning

## Features

- Multi-organization and multi-branch support
- Role-based access control (Admin, Branch Manager, Staff)
- Customer and vehicle management
- Service catalog with flexible pricing
- Vehicle-specific pricing rules with hierarchical lookup
- Soft deletes for data recovery
- Standardized JSON API responses

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0+ or SQLite
- Node.js & NPM (optional, for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/sevencroretechnologies/Car-service.git
cd Car-service
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:

For SQLite (development):
```
DB_CONNECTION=sqlite
```
Then create the database file:
```bash
touch database/database.sqlite
```

For MySQL (production):
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_service
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database with sample data:
```bash
php artisan db:seed
```

8. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`

## Default Users

After seeding, the following users are available:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@carservice.com | password |
| Branch Manager | manager@carservice.com | password |
| Staff | staff@carservice.com | password |

## API Endpoints

All API endpoints are prefixed with `/api/v1`

### Authentication
- `POST /login` - Login and get access token
- `POST /logout` - Logout (requires auth)
- `GET /me` - Get current user profile (requires auth)

### Organizations (Admin only)
- `GET /organizations` - List all organizations
- `POST /organizations` - Create organization
- `GET /organizations/{id}` - Get organization details
- `PUT /organizations/{id}` - Update organization
- `DELETE /organizations/{id}` - Delete organization

### Branches
- `GET /branches` - List branches
- `POST /branches` - Create branch
- `GET /branches/{id}` - Get branch details
- `PUT /branches/{id}` - Update branch
- `DELETE /branches/{id}` - Delete branch

### Users (Admin/Branch Manager)
- `GET /users` - List users
- `POST /users` - Create user
- `GET /users/{id}` - Get user details
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user

### Vehicle Types
- `GET /vehicle-types` - List vehicle types (paginated)
- `GET /vehicle-types/list` - List all active vehicle types
- `POST /vehicle-types` - Create vehicle type
- `GET /vehicle-types/{id}` - Get vehicle type details
- `PUT /vehicle-types/{id}` - Update vehicle type
- `DELETE /vehicle-types/{id}` - Delete vehicle type

### Vehicle Brands
- `GET /vehicle-brands` - List vehicle brands
- `GET /vehicle-brands/by-type/{vehicleTypeId}` - List brands by type
- `POST /vehicle-brands` - Create vehicle brand
- `GET /vehicle-brands/{id}` - Get vehicle brand details
- `PUT /vehicle-brands/{id}` - Update vehicle brand
- `DELETE /vehicle-brands/{id}` - Delete vehicle brand

### Vehicle Models
- `GET /vehicle-models` - List vehicle models
- `GET /vehicle-models/by-brand/{vehicleBrandId}` - List models by brand
- `POST /vehicle-models` - Create vehicle model
- `GET /vehicle-models/{id}` - Get vehicle model details
- `PUT /vehicle-models/{id}` - Update vehicle model
- `DELETE /vehicle-models/{id}` - Delete vehicle model

### Services
- `GET /services` - List services
- `GET /services/by-branch/{branchId}` - List services by branch
- `POST /services` - Create service
- `GET /services/{id}` - Get service details
- `PUT /services/{id}` - Update service
- `DELETE /services/{id}` - Delete service

### Customers
- `GET /customers` - List customers
- `GET /customers/search?phone={phone}` - Search customer by phone
- `POST /customers` - Create customer
- `GET /customers/{id}` - Get customer details
- `PUT /customers/{id}` - Update customer
- `DELETE /customers/{id}` - Delete customer

### Customer Vehicles
- `GET /customers/{customerId}/vehicles` - List customer vehicles
- `POST /customer-vehicles` - Create customer vehicle
- `GET /customers/{customerId}/vehicles/{vehicleId}` - Get vehicle details
- `PUT /customers/{customerId}/vehicles/{vehicleId}` - Update vehicle
- `DELETE /customers/{customerId}/vehicles/{vehicleId}` - Delete vehicle

### Vehicle Service Pricing
- `GET /pricing` - List pricing rules
- `GET /pricing/lookup` - Lookup price for service/vehicle combination
- `GET /pricing/by-service/{serviceId}` - Get pricing by service
- `POST /pricing` - Create pricing rule
- `GET /pricing/{id}` - Get pricing details
- `PUT /pricing/{id}` - Update pricing
- `DELETE /pricing/{id}` - Delete pricing

## API Response Format

### Success Response
```json
{
    "success": true,
    "message": "Success message",
    "data": { ... }
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Success message",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

## Authentication

The API uses Laravel Sanctum for token-based authentication. To authenticate:

1. Login to get a token:
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@carservice.com", "password": "password"}'
```

2. Use the token in subsequent requests:
```bash
curl http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Pricing Lookup Logic

The pricing system supports hierarchical pricing rules. When looking up a price, the system checks in this order:

1. Exact match (branch + service + vehicle type + brand + model)
2. Brand level match (branch + service + vehicle type + brand)
3. Type level match (branch + service + vehicle type)

This allows for flexible pricing where you can set a base price for a vehicle type and override it for specific brands or models.

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # API Controllers
│   ├── Middleware/            # Custom middleware
│   └── Requests/Api/V1/       # Form Request validation
├── Models/                    # Eloquent models
├── Services/                  # Business logic services
└── Traits/                    # Reusable traits
database/
├── migrations/                # Database migrations
└── seeders/                   # Database seeders
routes/
└── api.php                    # API routes
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
