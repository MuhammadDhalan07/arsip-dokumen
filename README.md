# Filament v4 Laravel 12 Starter

A modern Laravel 12 application with Filament v4 admin panel, featuring role-based permissions, comprehensive authentication, and a scalable architecture.

## 🚀 Quick Start

### Prerequisites

- PHP 8.3+
- Composer
- Node.js & NPM
- Laravel Herd (recommended) or Valet

### Installation

1. **Clone and setup**
   ```bash
   git clone <repository-url>
   cd filament-v4-starter
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup database**
   ```bash
   # SQLite (default)
   touch database/database.sqlite
   
   # Or configure MySQL/PostgreSQL in .env
   php artisan migrate --seed
   ```

4. **Create admin user**
   ```bash
   php artisan make:user
   ```

5. **Start development**
   ```bash
   # Using Laravel Herd (recommended)
   # Project will be available at https://filament-v4-starter.test
   
   # Or use the dev script for full development environment
   composer run dev
   ```

## 📋 Features

- **Laravel 12** with streamlined file structure
- **Filament v4** admin panel with modern UI
- **Role-based permissions** via Filament Shield
- **User management** with soft deletes
- **Multi-language support** (Indonesian/English)
- **ULID-based models** for better performance
- **Comprehensive testing** setup
- **Laravel Boost** integration for enhanced development

## 🏗️ Architecture

### Tech Stack

| Component | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.3+ | Core language |
| Laravel | 12.x | Framework |
| Filament | 4.x | Admin panel |
| Livewire | 3.x | Frontend reactivity |
| Tailwind CSS | Latest | Styling |
| Laravel Shield | 4.x | Permission management |

### Project Structure

```
app/
├── Concerns/           # Shared traits (HasUlids)
├── Console/Commands/   # Custom Artisan commands
├── Filament/           # Filament components
│   ├── Pages/         # Custom pages (Dashboard, Profile)
│   └── Resources/     # CRUD resources
│       └── Pengaturan/ # Settings resources
├── Http/Controllers/   # HTTP controllers
├── Models/            # Eloquent models
├── Policies/          # Authorization policies
└── Providers/         # Service providers
```

## 📖 Documentation

- [**Architecture Guide**](docs/ARCHITECTURE.md) - System design and patterns
- [**API Reference**](docs/API.md) - Filament resources and components
- [**Development Guide**](docs/DEVELOPMENT.md) - Workflow and conventions
- [**Deployment Guide**](docs/DEPLOYMENT.md) - Production setup

## 🛠️ Development

### Available Commands

```bash
# Development environment with hot reload
composer run dev

# Code formatting
vendor/bin/pint

# Testing
composer run test
php artisan test

# User management
php artisan make:user
php artisan shield:fresh
```

### Code Standards

- Follow Laravel conventions and PSR standards
- Use Filament v4 patterns for admin components
- Implement proper authorization with policies
- Write feature tests for new functionality
- Use ULID for model IDs

## 🔐 Authentication & Authorization

### User Management

- **Registration**: Admin-only via Filament panel
- **Roles & Permissions**: Managed through Filament Shield
- **Profile Management**: Self-service profile editing
- **Password Reset**: Built-in Laravel functionality

### Default Roles

- **Super Admin**: Full system access
- **User**: Basic authenticated access

## 🌐 Localization

Supports multiple languages with Indonesian as default:

- **Indonesian** (id): Primary language
- **English** (en): Secondary language

Language files located in `lang/` directory.

## 🧪 Testing

Comprehensive test suite covering:

- **Feature Tests**: User workflows and integration
- **Unit Tests**: Individual component functionality
- **Filament Tests**: Admin panel interactions

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 📦 Key Dependencies

### Production

- `filament/filament` - Admin panel framework
- `bezhansalleh/filament-shield` - Permission management
- `laravel/framework` - Core Laravel framework

### Development

- `laravel/boost` - Enhanced development tools
- `laravel/pint` - Code formatting
- `phpunit/phpunit` - Testing framework

## 🚀 Deployment

### Production Checklist

- [ ] Configure production environment variables
- [ ] Set up database (MySQL/PostgreSQL recommended)
- [ ] Run migrations and seeders
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up SSL certificates
- [ ] Configure caching (Redis recommended)
- [ ] Set up monitoring and logging

See [Deployment Guide](docs/DEPLOYMENT.md) for detailed instructions.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes following the coding standards
4. Write tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Laravel Boost Documentation](https://github.com/laravel/boost)
- [Filament Shield Documentation](https://github.com/bezhanSalleh/filament-shield)