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

## Additional Notes

- For frontend assets, run the following commands if applicable:
  ```bash
  npm install && npm run dev
  ```
- Update the `APP_URL` in your `.env` file if needed:
  ```env
  APP_URL=http://localhost:8000
  ```

---
