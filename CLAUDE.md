# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Wartung-App is a Laravel 11 WordPress Maintenance Management System for Masinga Tech. It tracks clients, websites, and maintenance activities with automated PDF report generation and email notifications.

## Development Commands

```bash
# Start all development services (Laravel server, queue, logs, Vite)
composer run dev

# Individual services
php artisan serve        # Laravel dev server
npm run dev              # Vite dev server
npm run build            # Build production assets

# Database
php artisan migrate      # Run migrations
php artisan migrate:fresh --seed  # Reset database with seed data

# Testing (Pest PHP)
php artisan test                              # All tests
php artisan test tests/Feature/ClientControllerTest.php  # Single file
php artisan test --filter "test_name"         # Single test
php artisan test --profile                    # With timing details

# Code style
./vendor/bin/pint        # Fix code style (Laravel Pint)
```

## Architecture

### Data Model Relationships
```
Client (1) → (M) Website (1) → (M) MaintenanceReport (1) → (M) PluginUpdate
                                        ↑
                              User (1) → (M)
```

### Key Controllers
- **MaintenanceReportController** - Core business logic: maintenance workflow, PDF generation (DomPDF), email sending, next maintenance date calculation
- **ClientController** / **WebsiteController** - Standard CRUD with soft deletes
- **DashboardController** - Statistics, upcoming/overdue maintenance

### Frontend Stack
- **Blade templates** (43 views in `resources/views/`)
- **Tailwind CSS** with Masinga Tech branding (primary: #2563eb)
- **Alpine.js** for reactive interactions
- **Vite** for asset bundling

### PDF Generation
- Template: `resources/views/pdf/maintenance-report.blade.php`
- Output: `storage/app/public/reports/`
- Library: barryvdh/laravel-dompdf

### Email System
- Mailable: `app/Mail/MaintenanceReportMail.php`
- Template: `resources/views/emails/maintenance-report.blade.php`
- Attaches generated PDF automatically

## Maintenance Workflow

1. Create draft report for a website
2. Fill checklist (backup status, PHP compatibility, WordPress/theme versions, 10 functional checks)
3. Add plugin updates dynamically
4. Complete report → generates PDF
5. Send email to client → marks sent_at timestamp
6. Auto-calculates next maintenance based on package type (monthly/quarterly/yearly)

## Testing

Uses Pest PHP with Laravel plugin. Tests use in-memory SQLite and array drivers for mail/cache.

Test files:
- `tests/Feature/` - Controller and workflow tests
- `tests/Unit/` - Model relationship tests
- `tests/Pest.php` - Helper: `actingAsUser()` for authentication

## Configuration Notes

- **Database**: SQLite by default (switchable via .env)
- **UI Language**: German
- **Mail Testing**: Pre-configured for Mailtrap
- **Test Credentials**: `admin@masingatech.com` / `password` (after seeding)
