```
Tests:    313 passed (651 assertions)
Duration: 12.57s

Http/Controllers/AuthController ..................................... 100.0%  
Http/Controllers/Controller ......................................... 100.0%  
Http/Controllers/LocaleController ................................... 100.0%  
Http/Controllers/TranslationController  57..58, 92..93, 130..131, 202, 221..226 / 75.0%  
Http/Controllers/UserController ..................................... 100.0%  
Http/Requests/StoreLocaleRequest .................................... 100.0%  
Http/Requests/StoreTranslationRequest ............................... 100.0%  
Http/Requests/UpdateLocaleRequest ................................... 100.0%  
Http/Requests/UpdateTranslationValueRequest ......................... 100.0%  
Models/Locale ....................................................... 100.0%  
Models/TranslationKey ............................................... 100.0%  
Models/TranslationRevision .......................................... 100.0%  
Models/TranslationTag ............................................... 100.0%  
Models/TranslationValue ............................................. 100.0%  
Models/User ......................................................... 100.0%  
Providers/AppServiceProvider ........................................ 100.0%  
Repositories/LocaleRepository ....................................... 100.0%  
Repositories/LocaleRepositoryInterface .............................. 100.0%  
Repositories/TranslationRepository ................ 71, 96..119, 110 / 78.5%  
Repositories/TranslationRepositoryInterface ......................... 100.0%  
Repositories/UserRepository ......................................... 100.0%  
Repositories/UserRepositoryInterface ................................ 100.0%  
Services/AuthService ................................................ 100.0%  
Services/LocaleService .............................................. 100.0%  
Services/TranslationService ......................................... 100.0%  
Services/UserService ................................................ 100.0%  
────────────────────────────────────────────────────────────────────────────  
                                                            Total: 90.5 %
```
### Requirements
- PHP 8.4
- Composer
- Posgresql 1.6

### Installation
1. Clone the repository:
   ```sh
   git clone <your-repo-url>
   cd <project-directory>
   ```
2. Install PHP dependencies:
   ```sh
   composer install
   ```
4. Copy the example environment file and configure it:
   ```sh
   cp .env.example .env
   # Edit .env as needed
   ```
5. Generate the application key:
   ```sh
   php artisan key:generate
   ```
6. Run database migrations:
   ```sh
   php artisan migrate:fresh --seed
   ```

### Usage
- Start the development server:
  ```sh
  php artisan serve
  ```
- Access the application at [http://localhost:8000](http://localhost:8000)

### Testing
- Run tests with:
  ```sh
  ./vendor/bin/pest
  ```

- Run tests with coverage:
 
  ```sh
  chmod +x pest-coverage.sh
  ./pest-coverage.sh
  ```
  coverage is in this location `projectfolder/coverage/`

### Project Structure
- `app/` - Application logic (Controllers, Models, Services, Repositories)
- `database/` - Migrations, seeders, factories
- `routes/` - Route definitions (`api.php`, `web.php`, `console.php`)
- `config/` - Configuration files
- `tests/` - Test cases

### Additional Notes
- Modular code with repositories and services
- SwaggerUI

---

## API Usage

### Running the API

### Authentication & Getting a Token
This project likely uses Laravel Sanctum or Passport for API authentication.

1. Register a new user (if registration endpoint is available):
   ```sh
   curl -X POST http://localhost:8000/api/register \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"name": "Test User", "email": "test@example.com", "password": "password", "password_confirmation": "password"}'
   ```
2. Login to get an access token:
   ```sh
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"email": "test@example.com", "password": "password"}'
   ```
   The response will include a token (e.g., `access_token`).

### Using the Token
Include the token in the `Authorization` header for authenticated requests:
```sh
curl -H "Authorization: Bearer <access_token>" http://localhost:8000/api/your-endpoint
```

### Sample API Request
Example: Create a new locale
```sh
curl -X POST http://localhost:8000/api/locales \
  -H "Authorization: Bearer <access_token>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"code": "en", "name": "English"}'
```

### Sample API Response
```json
{
  "id": 1,
  "code": "en",
  "name": "English",
  "created_at": "2025-08-10T12:00:00.000000Z",
  "updated_at": "2025-08-10T12:00:00.000000Z"
}
```

> For a full list of endpoints and request/response formats, see the Swagger/OpenAPI docs if enabled, or review `routes/api.php` and controller files.

### API Documentation (Swagger UI)
If Swagger/OpenAPI is enabled, you can access the interactive API documentation at:

- [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

This provides a full list of endpoints, request/response formats, and allows you to try out API calls directly from the browser.




# Using docker compose

> **⚠️ IMPORTANT NOTE:**
> 
> Using `docker-compose` works, but there is a known issue: **in the Docker setup, Swagger UI does not work properly.** Please keep this in mind when using Docker for local development.


### 1. Clone and Setup

```bash
# Clone the repository (if not already done)
git clone <your-repo-url>
cd <project-directory>

# Copy the Docker environment file
cp docker.env .env
```

### 2. Build and Start Services

```bash
# Build and start all services
docker-compose up -d --build

# View logs
docker-compose logs -f
```

### 3. Install Dependencies and Setup Laravel

```bash
# Install PHP dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database (optional)
docker-compose exec app php artisan db:seed
```


