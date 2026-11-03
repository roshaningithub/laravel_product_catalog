# Laravel Product Catalog API

A RESTful JSON API for managing a product catalog, built with **Laravel 12** and **PHP 8.2+**.

The feature set is small on purpose. The project shows how I structure a Laravel codebase so it stays maintainable as it grows: features live in their own modules, and each layer has one job. Controllers stay thin, validation lives in Form Requests, data moves between layers in typed DTOs, business logic sits in a service, and data access goes through a repository behind an interface.

---

## Tech Stack

| Layer        | Technology                              |
|--------------|-----------------------------------------|
| Language     | PHP 8.2+                                |
| Framework    | Laravel 12                              |
| Database     | SQLite (default), works with MySQL / PostgreSQL |
| Testing      | PHPUnit 11 (feature tests, in-memory SQLite) |
| Code Style   | Laravel Pint                            |
| Tooling      | Laravel Sail, Pail, Tinker              |

---

## Architecture

The product feature is a self-contained **module** under `app/Modules/Product`. It does not use Laravel's default `app/Http` and `app/Models` folders:

```
app/
├── Helpers/
│   └── api_helper.php                  # Shared apiResponse() helper for a consistent JSON envelope
├── Modules/
│   └── Product/
│       ├── Controllers/
│       │   └── ProductController.php   # Thin HTTP layer: maps requests to DTOs, returns responses
│       ├── DTOs/
│       │   ├── CreateProductDTO.php    # Typed, immutable-by-convention data carriers
│       │   ├── UpdateProductDTO.php
│       │   └── DeleteProductDTO.php
│       ├── Models/
│       │   └── Product.php             # Eloquent model
│       ├── Repositories/
│       │   ├── Interfaces/
│       │   │   └── ProductRepositoryInterface.php
│       │   └── Eloquent/
│       │       └── ProductRepository.php
│       ├── Requests/                   # Form Request validation per action
│       │   ├── CreateProductRequest.php
│       │   ├── UpdateProductRequest.php
│       │   └── DeleteProductRequest.php
│       ├── Resources/
│       │   └── ProductResource.php     # API Resource: controls the output shape
│       └── Services/
│           └── ProductService.php      # Business logic, wrapped in DB transactions
└── Providers/
    └── RepositoryServiceProvider.php   # Binds repository interfaces to implementations
```

### Request lifecycle

```
HTTP Request
   │
   ▼
Form Request ──► validation fails ──► 422 JSON errors
   │
   ▼
Controller ──► builds DTO
   │
   ▼
Service ──► opens DB transaction
   │
   ▼
Repository (via interface) ──► Eloquent Model ──► Database
   │
   ▼
API Resource ──► apiResponse() envelope ──► JSON Response
```

### Design decisions

- **Modular structure:** each feature keeps its controllers, requests, DTOs, services and repositories together. A new feature (for example `Category` or `Order`) becomes a new folder under `app/Modules`, not a set of files spread across the app.
- **Repository pattern with dependency inversion:** `ProductService` depends on `ProductRepositoryInterface`, not on Eloquent. `RepositoryServiceProvider` binds the concrete class, so you can swap the persistence layer or mock it in tests without touching business logic.
- **DTOs between layers:** the service layer receives typed objects, not raw `Request` instances or arrays. This decouples business logic from HTTP and makes the service's contract explicit.
- **Transactional service methods:** every write runs inside `DB::beginTransaction()` / `commit()` / `rollBack()`. Multi-step operations added later stay atomic.
- **Consistent response envelope:** every endpoint returns the same `{ status, message, data }` shape through the `apiResponse()` helper, which makes client integration predictable.
- **Validation at the edge:** dedicated Form Requests reject bad input before it reaches the controller, including `exists:products,id` checks on update and delete.

---

## API Reference

Base URL: `http://localhost:8000/api`

All requests and responses use JSON. Send `Accept: application/json` so validation errors come back as JSON.

### Create a product

`POST /api/product`

```json
{
  "name": "Gaming Laptop",
  "description": "High performance laptop",
  "price": 1500
}
```

**201 Created**

```json
{
  "status": true,
  "message": "Product created successfully.",
  "data": {
    "id": 1,
    "name": "Gaming Laptop",
    "price": 1500,
    "description": "High performance laptop"
  }
}
```

### Update a product

`PUT /api/product`

```json
{
  "id": 1,
  "name": "Updated Laptop",
  "description": "New model",
  "price": 1200
}
```

**200 OK**

```json
{
  "status": true,
  "message": "Product updated successfully.",
  "data": {
    "id": 1,
    "name": "Updated Laptop",
    "price": 1200,
    "description": "New model"
  }
}
```

### Delete a product

`DELETE /api/product`

```json
{
  "id": 1
}
```

**200 OK**

```json
{
  "status": true,
  "message": "Product deleted successfully.",
  "data": null
}
```

### Validation rules

| Field         | Create                     | Update                              | Delete                              |
|---------------|----------------------------|-------------------------------------|-------------------------------------|
| `id`          | —                          | required, integer, must exist       | required, integer, must exist       |
| `name`        | required, string, max 255  | required, string, max 255           | —                                   |
| `description` | nullable, string           | nullable, string                    | —                                   |
| `price`       | required, numeric, min 0   | required, numeric, min 0            | —                                   |

### Validation error example

**422 Unprocessable Entity**

```json
{
  "message": "The id field is required.",
  "errors": {
    "id": ["The id field is required."]
  }
}
```

---

## Database Schema

**`products`**

| Column        | Type            | Notes          |
|---------------|-----------------|----------------|
| `id`          | bigint, PK      | auto-increment |
| `name`        | varchar(255)    |                |
| `price`       | decimal(10, 2)  |                |
| `description` | text            | nullable       |
| `created_at`  | timestamp       |                |
| `updated_at`  | timestamp       |                |

---

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm (only for the default frontend assets)

### Installation

```bash
git clone https://github.com/roshaningithub/laravel_product_catalog.git
cd laravel_product_catalog

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

php artisan serve
```

The API is now available at `http://localhost:8000/api`.

There is also a shortcut that runs install, env setup, key generation, migrations and the asset build in one step:

```bash
composer run setup
```

### Using MySQL instead of SQLite

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_catalog
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate`.

### Quick test with cURL

```bash
curl -X POST http://localhost:8000/api/product \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Gaming Laptop","description":"High performance laptop","price":1500}'
```

---

## Testing

Feature tests in `tests/Feature/ProductApiTest.php` cover the success path and the validation-failure path of every endpoint. They run against an in-memory SQLite database with `RefreshDatabase`, so they are fast and leave no data behind.

```bash
php artisan test
# or
composer test
```

| Test                                           | Verifies                                    |
|------------------------------------------------|---------------------------------------------|
| `test_cannot_create_product_without_required_fields` | 422 on empty create payload           |
| `test_can_create_product_successfully`         | 201, response envelope, row persisted       |
| `test_can_create_product_without_description`  | Optional `description` is stored and returned as `null` |
| `test_cannot_update_product_without_required_fields` | 422 on empty update payload           |
| `test_can_update_product_successfully`         | 200, updated data returned, DB reflects it  |
| `test_cannot_delete_product_without_id`        | 422 with `errors.id` present                |
| `test_can_delete_product_successfully`         | 200, row removed from DB                    |

---

## Roadmap

- [ ] `GET /api/products` with pagination, search and filtering
- [ ] `GET /api/products/{id}` for a single product
- [ ] Resource-style routes (`/api/products/{id}`) with route model binding
- [ ] Authentication with Laravel Sanctum (already installed)
- [ ] Unit tests for `ProductService` with a mocked repository
- [ ] Categories module to show cross-module relationships
- [ ] OpenAPI / Swagger documentation
- [ ] Docker setup with Laravel Sail
- [ ] CI pipeline with GitHub Actions (tests + Pint)

---

## Author

**Roshan Maharjan**

If this project helped you or you'd like to connect, feel free to reach out or open an issue.

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
