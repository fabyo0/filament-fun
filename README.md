# 🎨 Filament Fun - Modern Laravel Admin Panel

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.2-F59E0B?style=flat)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive learning project exploring Filament PHP's capabilities - featuring multi-tenancy, role management, Stripe payments, and real-time notifications in a beautifully crafted admin interface.

## 📸 Screenshots

<img width="1423" height="646" alt="Screenshot 2025-11-19 at 20 01 02" src="https://github.com/user-attachments/assets/fd12c0e6-19f4-49b5-a6f1-6a53d287abc2" />


## ✨ Key Features

### 🎯 Core Functionality
- **Modern Admin Panel** - Built with Filament 3.2 for exceptional UX
- **Multi-Tenancy** - Team-based data isolation and management
- **Advanced RBAC** - Role & permission system with Filament Shield
- **Employee Management** - Complete HR module with departments and locations
- **Product Catalog** - Full-featured product and inventory management

### 💳 Integrations & Tools
- **Stripe Payments** - Secure payment processing
- **Real-time Notifications** - Powered by Laravel Reverb WebSockets
- **Blog System** - Content management with Firefly Blog
- **Automated Backups** - Scheduled backups with Spatie Laravel Backup
- **Activity Logging** - Complete audit trail of system actions
- **Excel Export** - Data export capabilities

### 🎨 User Experience
- **Dashboard Analytics** - Beautiful charts with Apex Charts
- **Spotlight Search** - Quick navigation (⌘K / Ctrl+K)
- **Multi-language** - Full translation support
- **Dark Mode** - Built-in theme switching

---

## 🚀 Quick Start

### Prerequisites

Ensure you have the following installed:
- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 18+ and npm
- **MySQL** 5.7+ / **PostgreSQL** / **MariaDB** 10.3+
- **PHP Extensions**: `ext-intl`, `ext-gd`, `ext-zip`

### Installation

1️⃣ **Clone & Install Dependencies**
```bash
git clone <repository-url>
cd filament-fun
composer install
npm install
```

2️⃣ **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3️⃣ **Configure Database**

Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=filament_fun
DB_USERNAME=root
DB_PASSWORD=your_password
```

4️⃣ **Run Migrations & Seed Data**
```bash
php artisan migrate --seed
php artisan storage:link
```

5️⃣ **Configure Stripe** (Optional)
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_CURRENCY=usd
```

6️⃣ **Build Frontend Assets**
```bash
npm run build  # Production
# OR
npm run dev    # Development with HMR
```

---

## 🎮 Running the Application

### Option 1: One Command to Rule Them All (Recommended)
```bash
composer dev
```

This starts:
- 🌐 Laravel dev server → `http://localhost:8000`
- 📦 Queue worker (for async jobs)
- 📊 Log viewer (Laravel Pail)
- ⚡ Vite HMR server

### Option 2: Manual Mode

Run in separate terminals:
```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:work

# Terminal 3
npm run dev
```

### Option 3: Laravel Valet (macOS)
```bash
valet park
# Access at: http://filament-fun.test
```

---

## 👤 Default Credentials

After seeding, login with:
```
Email: Check database/seeders/UserSeeder.php
Password: Check database/seeders/UserSeeder.php
```

> **Security Note:** Change default credentials immediately in production!

---

## 📦 Tech Stack

### Core Framework
| Package | Version | Purpose |
|---------|---------|---------|
| Laravel | 11.x | Backend Framework |
| Filament | 3.2 | Admin Panel |
| Livewire | 3.x | Reactive Components |

### Filament Plugins
- **Shield** - Authorization & Permissions
- **Breezy** - Authentication & Profile
- **Excel** - Export Operations
- **Spotlight** - Quick Search (⌘K)
- **Apex Charts** - Data Visualization
- **Blog** - Content Management
- **Translations** - i18n Support
- **Spatie Backup** - Automated Backups

### Additional Tools
- **Spatie Permission** - RBAC System
- **Spatie Media Library** - File Management
- **Laravel Reverb** - WebSocket Server
- **Laravel Trend** - Analytics
- **Stripe PHP SDK** - Payment Processing

---

## 🛠️ Development Commands
```bash
# Code Quality
composer pint              # Format code (Laravel Pint)
composer test              # Run tests

# IDE Support
php artisan ide-helper:generate
php artisan ide-helper:models

# Monitoring
php artisan pail           # Watch logs in real-time
php artisan queue:work     # Process background jobs

# Database
php artisan migrate:fresh --seed  # Fresh start
php artisan db:seed               # Re-seed data

# Cache Management
php artisan optimize:clear        # Clear all caches
php artisan filament:optimize     # Optimize Filament
```

---

## 📁 Project Structure
```
app/
├── Filament/
│   ├── Resources/          # CRUD Resources (Employee, Product, User)
│   ├── Pages/              # Custom Pages (Backups, Payments)
│   ├── Widgets/            # Dashboard Widgets
│   └── Clusters/           # Grouped Resources
├── Models/                 # Eloquent Models
├── Enums/                  # Enum Classes (Role, Status)
├── Casts/                  # Custom Casts (MoneyCast)
└── Providers/              # Service Providers

database/
├── migrations/             # Database Schema
├── seeders/                # Sample Data
└── factories/              # Model Factories

resources/
├── views/                  # Blade Templates
└── js/                     # Frontend Assets
```

---

## 🌍 Multi-language Support

Add new languages:
```bash
php artisan filament:translations
```

Available languages can be managed via the admin panel under **Settings → Translations**.

---

## 🔐 Security Features

- ✅ CSRF Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade Escaping)
- ✅ Role-Based Access Control
- ✅ Secure Password Hashing
- ✅ PCI-Compliant Payments (Stripe)
- ✅ Activity Logging & Audit Trail

---

## 📊 Queue & Background Jobs

### Development
```bash
php artisan queue:work
```

### Production (with Supervisor)

Create `/etc/supervisor/conf.d/filament-fun-worker.conf`:
```ini
[program:filament-fun-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start filament-fun-worker:*
```

---

## 💾 Automated Backups

Backups are managed via **Spatie Laravel Backup**. Access the backup panel at:
```
/admin/backups
```

Configure backup schedule in `config/backup.php` and setup cron:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
```bash
   git checkout -b feature/amazing-feature
```
3. Commit your changes (use [Conventional Commits](https://www.conventionalcommits.org/))
```bash
   git commit -m "feat: add amazing feature"
```
4. Push to your branch
```bash
   git push origin feature/amazing-feature
```
5. Open a Pull Request

---

## 🐛 Bug Reports & Feature Requests

Found a bug or have a feature idea? Please [open an issue](https://github.com/yourusername/filament-fun/issues/new) with:
- Clear description
- Steps to reproduce (for bugs)
- Expected vs actual behavior
- Screenshots (if applicable)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- [Filament PHP](https://filamentphp.com) - Amazing admin panel framework
- [Laravel](https://laravel.com) - The PHP framework for web artisans
- [Spatie](https://spatie.be) - Quality Laravel packages
- All the amazing open-source contributors

---

## 📞 Support

- **Documentation**: [Filament Docs](https://filamentphp.com/docs)
- **Issues**: [GitHub Issues](https://github.com/yourusername/filament-fun/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/filament-fun/discussions)

---

<div align="center">

**⭐ Star this repo if you find it helpful!**

Made with ❤️ using Filament PHP

</div>
