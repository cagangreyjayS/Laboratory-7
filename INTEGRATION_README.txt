LABORATORY 4 + LABORATORY 5 INTEGRATED PROJECT
=============================================

What was connected
- Lab 4 Student Portal theme and dashboard layout are used by the Laravel Lab 5 project.
- Laravel authentication remains in place (the login form posts to Laravel's login route with CSRF protection).
- The dashboard reads the existing Lab 5 `products` records through DashboardController.
- Search, category/status filters, CSV export, and the three charts remain in the Blade dashboard.
- The sidebar includes Dashboard, Profile, Settings, Inventory, and Log Out.
- Lab 5 inventory routes/models/migrations were left in place.
- No database was reset and no migration was run as part of this integration.

How to run
1. Extract this ZIP.
2. Open Command Prompt and change directory into the extracted `lab05_2411600350` folder.
3. Make sure Apache/MySQL are running in XAMPP and that `.env` points to your existing Lab 5 database.
4. If dependencies are already installed, run:
      php artisan serve
   If the project reports missing dependencies, run `composer install` first.
5. Open http://127.0.0.1:8000
6. Sign in with the admin account that exists in your database. The project seeder defines:
      username: admin
      password: password123
   These credentials work only if that user has already been seeded/created.

Important
- Do NOT run `php artisan migrate:fresh`; it deletes existing tables/data.
- If this is the first setup and the existing tables are not yet migrated, review your database backup and migration status before running migrations.
