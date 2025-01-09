# Installation Guide

## Requirements

Ensure the following are installed on your system:
- PHP (>=8.0)
- Composer
- MySQL or any other supported database
- Node.js and npm (optional, for frontend dependencies)

---

## Setup Instructions

### 1. Install Dependencies

Run the following command to install all PHP dependencies using Composer:
```bash
composer install
```

---

### 2. Database Configuration

This application uses **MySQL** by default. To use a different database, update the configuration in `config/database.php`.

#### Steps:
1. Install and set up MySQL (or your preferred database).
2. Create a database for the project.
3. Update the `.env.example` file with your database credentials:
   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
4. Rename `.env.example` to `.env`:
   ```bash
   mv .env.example .env
   ```

---

### 3. Migrations

Run the following command to create the necessary database tables:
```bash
php artisan migrate
```

---

### 4. Database Seeding

To populate the database with dummy data, run:
```bash
php artisan db:seed
```

---

### 5. File Upload Configuration

Uploaded files are stored in the `storage/app/public` directory. To make these files publicly accessible, create a symbolic link with the following command:
```bash
php artisan storage:link
```

---

### 6. Running the Application

You can run the application in one of the following ways:

#### Option 1: Using the Built-in Server
Start the development server:
```bash
php artisan serve
```

#### Option 2: Using Laravel Valet (Recommended for macOS)
Place the project in your Valet directory, and access it via the configured domain.

---

### 7. Running the Queue Worker for Notifications

This application uses queues for handling notifications. To ensure notifications are processed, you need to run the queue worker.

#### Steps:
1. Start the queue worker by running the following command:
   ```bash
   php artisan queue:work
   ```
2. For long-running applications, consider using a process manager like **Supervisor** to keep the queue worker running continuously. Example Supervisor configuration:
   ```
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   user=your-user
   numprocs=1
   redirect_stderr=true
   stdout_logfile=/path-to-your-project/worker.log
   ```

---

## Additional Notes

- For frontend assets, run the following commands if applicable:
  ```bash
  npm install && npm run dev
  ```
- Update the `APP_URL` in your `.env` file if needed:
  ```env
  APP_URL=http://localhost:8000
  ```
- Monitor your queue jobs and logs to ensure the application runs smoothly.

---

