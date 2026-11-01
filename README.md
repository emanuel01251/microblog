# Microblog Application

A Laravel 13 microblogging application using Livewire 4, Jetstream 5, Sanctum 4, and Vite. Laravel, MySQL, and phpMyAdmin run in Docker for a consistent PHP 8.3.36 runtime.

> **Note:** This project is currently a work in progress. Some features may be incomplete or subject to change.

## Features

- **User Authentication & Authorization**
  - Secure login and registration
  - Role-based access control
  - Profile privacy settings

- **Content Management**
  - Create posts with images and videos
  - Edit and delete your own content
  - Comment on posts
  - Like and share functionality

- **Profile Management**
  - Customize profile information
  - Upload and edit profile pictures
  - View personal post history

- **Social Features**
  - Follow/Unfollow other users
  - View followers and following lists
  - Search for other users
  - View other users' profiles and posts

- **Share Functionality**
  - Share posts to your profile
  - Add custom captions to shared content
  - View shared posts in feed
  - Track and manage shared content

## Prerequisites

- Docker
- Docker Compose
- PHP 8.3.36 or newer, below PHP 8.4, with the required Laravel extensions. This patched PHP 8.3 baseline is required for the PHP2Shell security fix.
- Composer
- Node.js 22.12 or newer and npm

Verify the PHP runtime before installing dependencies:
```bash
php -v
```

If Windows still reports the old XAMPP PHP 8.2 runtime, upgrade the PHP runtime or use the provided PHP 8.3.36 Docker image before running Composer or Artisan.

## Installation Steps

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Start the Docker services:
```bash
docker compose up -d
```

3. Install PHP dependencies using Composer on Windows:
```bash
composer install
```

4. Copy the environment file:
```bash
Copy-Item .env.example .env
```

5. Update the database configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=microblog
DB_USERNAME=root
DB_PASSWORD=password
```

6. Generate application key:
```bash
php artisan key:generate
```

7. Run database migrations:
```bash
php artisan migrate
```

8. Install frontend dependencies:
```bash
npm install
```

9. Compile assets:
```bash
npm run build
```

10. Start Laravel with the patched PHP 8.3.36 Apache container:
```bash
docker compose up -d --build
```

Open http://localhost:8000. Do not run `php artisan serve` until the host `php -v` reports PHP 8.3.36 or newer and below PHP 8.4.

The container caches Laravel configuration and rechecks PHP files every 60 seconds to reduce Windows bind-mount
overhead during development. Restart the web service after PHP or Blade changes if you need them picked up immediately:

```bash
docker compose restart web
```

## Accessing the Application

- Main application: http://localhost:8000
- PHPMyAdmin: http://localhost:8083
  - Username: root
  - Password: password

## Useful Commands

- Run the test suite with the in-memory SQLite database configured in `phpunit.xml`:
```bash
php artisan test
```

- Verify the dependency and runtime requirements:
```bash
composer audit --locked
composer check-platform-reqs
php artisan view:cache
php artisan route:list
```

- Recreate and seed a local database when testing schema changes:
```bash
php artisan migrate:fresh --seed
```

- Build the production frontend bundle with Vite:
```bash
npm run build
```

- Stop containers:
```bash
docker compose down
```

- View container logs:
```bash
docker compose logs
```

- Rebuild the containerized application:
```bash
docker compose up -d --build
```

## Troubleshooting

1. The Docker MySQL host port is `3308` because ports `3306` and `3307` are already used by Windows MySQL processes.
   - If you free port 3306 later, you can change the host-side port in `docker-compose.yml`.
   - Update `DB_PORT` in `.env` to match the host-side port.

2. If npm commands fail:
   - Ensure Node.js 22.12+ and npm are installed on Windows.
   - Run `npm install` again, then retry `npm run build`.

3. Laravel log or cache permission errors

   If you see an error like:

   ```
   The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
   ```

   On Windows, run:

   ```bash
   php artisan optimize:clear
   ```

4. The web service is included in the default `docker compose up -d` stack. Do not run it at the same time as
   `php artisan serve` because both use port 8000.
