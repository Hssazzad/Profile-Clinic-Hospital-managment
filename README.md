# Hospital Management System (ProfClinic)

A comprehensive Laravel-based hospital and clinic management system for managing patients, prescriptions, appointments, admissions, and medical records.

## Features

- **Patient Management**: Create, update, and search patient records
- **Appointments**: Schedule and manage patient appointments
- **Prescriptions**: Manage medical prescriptions with multiple templates
- **Admissions**: Track hospital admissions and discharge records
- **Master Data**: Configure medical specialties, medicines, investigations, diagnoses, and complaints
- **User Management**: Role-based access control and user assignment
- **Menu Management**: Dynamic menu creation for different user roles
- **Nursing Module**: Post-surgery care and patient round management

## Requirements

- PHP 7.2 or higher
- MySQL/MariaDB 5.7 or higher
- Composer
- Node.js & npm (for frontend assets)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Hssazzad/Hospital-software.git
cd profclinic
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=profclinic
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

```bash
mysql -u root -p
CREATE DATABASE profclinic;
EXIT;
```

### 6. Run Database Migrations

The project includes a migration that imports the complete SQL dump with all hospital management tables:

```bash
php artisan migrate
```

This will:
- Create the `migrations` table
- Execute `2026_04_05_000000_import_sql_dump.php`
- Import all tables from `database/sql/u972011074_vzeTw.sql`
- Populate initial data for patients, appointments, medicines, investigations, diagnoses, and more

**Troubleshooting Migration:**
- If you get "Table 'profclinic.users' doesn't exist", run: `php artisan migrate --fresh`
- Ensure MySQL is running and accessible
- Check database credentials in `.env`

### 7. Build Frontend Assets

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Database Structure

The SQL dump includes the following key tables:

### Core Tables
- `users` - System users and authentication
- `patients` - Patient information and records
- `doctors` - Doctor profiles and specialties

### Clinical Tables
- `admissions` - Patient admission records
- `appointments` - Appointment scheduling
- `prescriptions` - Medical prescriptions
- `prescription_complains` - Patient complaints linked to prescriptions

### Master Data Tables
- `common_medicine` - Medicine catalog
- `common_investigation` - Lab investigations and tests
- `common_diagnosis` - Diagnosis codes and descriptions
- `common_complain` - Common patient complaints
- `configspeciality` - Medical specialties
- `configdistrict` - District configuration
- `configunion` - Union/area configuration

### System Tables
- `parent_menu` - Menu category system
- `cache` - Application cache storage
- `cache_locks` - Cache lock management

## Project Structure

```
profclinic/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Providers/
├── config/
├── database/
│   ├── migrations/
│   │   └── 2026_04_05_000000_import_sql_dump.php
│   └── sql/
│       └── u972011074_vzeTw.sql
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
└── .env
```

## Key Route Files

- `routes/web.php` - Main application routes
- `routes/patient.php` - Patient management routes
- `routes/prescriptions.php` - Prescription routes
- `routes/appointment.php` - Appointment routes
- `routes/nursing.php` - Nursing module routes
- `routes/auth.php` - Authentication routes

## Configuration Files

- `.env` - Environment variables and database credentials
- `config/database.php` - Database configuration
- `config/app.php` - Application settings
- `config/auth.php` - Authentication configuration
- `tailwind.config.js` - Tailwind CSS configuration
- `vite.config.js` - Vite build configuration

## Troubleshooting

### Common Issues

**Error: "Table 'profclinic.users' doesn't exist"**
```bash
php artisan migrate
```

**Error: "Connection refused" or "Access Denied"**
- Verify MySQL is running
- Check `.env` database credentials
- Ensure the `profclinic` database exists

**Permission Denied on Storage**
```bash
chmod -R 775 storage bootstrap/cache
```

**Clear Application Cache**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Git Branches

- `main` - Production-ready code
- `hospital-management` - Active development branch with latest features

## Development

### Running Tests
```bash
php artisan test
```

### Running Migrations
```bash
# Run all pending migrations
php artisan migrate

# Fresh migration (drops all tables and re-runs)
php artisan migrate:fresh

# Rollback last batch
php artisan migrate:rollback
```

## Contributing

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Commit changes: `git commit -am 'Add feature'`
3. Push to branch: `git push origin feature/your-feature`
4. Submit a pull request

## License

This project is proprietary and confidential.

## Support

For issues or questions, please contact the development team.

---

**Last Updated**: April 5, 2026  
**Version**: 1.0.0  
**Deployed Branch**: hospital-management

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
https://profclinic.erpbd.org/login