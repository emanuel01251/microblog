# Microblog Application

A Laravel 9.7-based microblogging application. Laravel runs natively on Windows for fast file access, while MySQL and phpMyAdmin run in Docker.

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
- PHP 8.2 or newer with the required Laravel extensions
- Composer
- Node.js and npm

## Installation Steps

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Start MySQL and phpMyAdmin:
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

8. Install Node.js dependencies:
```bash
npm install
```

9. Compile assets:
```bash
npm run dev
```

10. Start Laravel:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## Accessing the Application

- Main application: http://localhost:8000
- PHPMyAdmin: http://localhost:8083
  - Username: root
  - Password: password

## Useful Commands

- Stop containers:
```bash
docker compose down
```

- View container logs:
```bash
docker compose logs
```

- Rebuild the optional containerized web application:
```bash
docker compose --profile container-app up -d --build
```

## Troubleshooting

1. The Docker MySQL host port is `3308` because ports `3306` and `3307` are already used by Windows MySQL processes.
   - If you free port 3306 later, you can change the host-side port in `docker-compose.yml`.
   - Update `DB_PORT` in `.env` to match the host-side port.

2. If npm commands fail:
   - Ensure Node.js and npm are installed on Windows.
   - Run `npm install` again, then retry `npm run dev`.

3. Laravel log or cache permission errors

   If you see an error like:

   ```
   The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
   ```

   On Windows, run:

   ```bash
   php artisan optimize:clear
   ```

4. To use the old Docker-based PHP/Apache workflow instead:

   ```bash
   docker compose --profile container-app up -d --build
   ```

   Do not run it at the same time as `php artisan serve` because both use port 8000.
