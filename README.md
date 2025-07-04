# Microblog Application

A Laravel 9.7-based microblogging application running with Docker. This project is inspired by Instagram and Twitter, created as a learning journey to explore modern web development practices using Laravel framework and Docker containerization.

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

## Installation Steps

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Start the Docker containers:
```bash
docker-compose up -d
```

3. Install PHP dependencies using Composer:
```bash
docker-compose exec web composer install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Update the database configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=microblog
DB_USERNAME=root
DB_PASSWORD=password
```

6. Generate application key:
```bash
docker-compose exec web php artisan key:generate
```

7. Run database migrations:
```bash
docker-compose exec web php artisan migrate
```

8. Install Node.js dependencies:
```bash
docker-compose exec web npm install
```

9. Compile assets:
```bash
docker-compose exec web npm run dev
```

## Accessing the Application

- Main application: http://localhost:8000
- PHPMyAdmin: http://localhost:8081
  - Username: root
  - Password: password

## Useful Commands

- Stop containers:
```bash
docker-compose down
```

- View container logs:
```bash
docker-compose logs
```

- Rebuild containers:
```bash
docker-compose up -d --build
```

## Troubleshooting

1. If port 80 is in use by IIS on Windows:
   - Either stop IIS using `net stop was /y` (requires admin privileges)
   - Or modify the port in docker-compose.yml to use port 8000 instead

2. If npm commands fail:
   - Ensure Node.js is installed in the container
   - Check if the container is running: `docker-compose ps`
   - Try rebuilding the container: `docker-compose up -d --build`

3. Laravel log or cache permission errors

   If you see an error like:

   ```
   The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
   ```

   You can fix it by running:

   ```bash
   docker-compose run --rm web chmod -R 777 storage bootstrap/cache
   ```

   This command gives the necessary write permissions to the folders Laravel needs.
   **Note:** This is safe for local development, but not recommended for production.
