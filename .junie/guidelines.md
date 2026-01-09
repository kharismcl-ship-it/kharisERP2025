# Kharis ERP — Engineering Guidelines (Laravel 12 + Livewire v3 + Filament + Nwidart Modules)

These notes capture the conventions and gotchas for this codebase, plus a concrete recipe to extend it into a fully modular ERP per the Kharis specification. This document is targeted at senior Laravel developers; it intentionally omits Laravel basics.

Current baseline: this repository is based on the official Livewire Starter Kit (Laravel 12, Fortify auth flows, Pest tests, Vite build). Livewire v3 tooling (Volt + Flux) is present. Modularization (nwidart/laravel-modules) and Filament are not yet installed — instructions below standardize how to add them.


## 1) Build, Setup, and Configuration

- Prerequisites
  - PHP 8.2+
  - Node 20+
  - Composer 2.6+
  - MySQL 8+ or Postgres 14+ (dev may use SQLite for speed)

- One-step local setup
  - The project defines a Composer script that bootstraps everything, including `.env`, key, migrations, npm, and build:
    ```bash
    composer setup
    ```

- Manual setup (equivalent)
  ```bash
  composer install
  # Create env if missing
  php -r "file_exists('.env') || copy('.env.example', '.env');"

  # Database
  php artisan key:generate
  # If using SQLite in dev:
  php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
  php artisan migrate --force

  # Frontend
  npm install
  npm run build   # or: npm run dev during development
  ```

- Development processes
  - Run all dev services concurrently (app server, queue, pail log UI, Vite):
    ```bash
    composer dev
    ```
  - Notes:
    - Queue worker in dev uses `queue:listen --tries=1`. If you enable jobs (mail, notifications), keep this running.
    - Pail (`laravel/pail`) provides a live log stream; keep its panel visible to catch Livewire/Volt errors.

- App config notes
  - `config:cache` is run implicitly by some scripts; if you touch config during dev and observe stale values, clear the cache:
    ```bash
    php artisan config:clear && php artisan route:clear && php artisan view:clear
    ```


## 2) Testing

- Test runner
  - Framework: Pest 3.x (with `pest-plugin-laravel`).
  - Standard command:
    ```bash
    composer test
    # which runs: php artisan config:clear --ansi && php artisan test
    ```

- Test types
  - Unit tests → `tests/Unit`
  - Feature tests → `tests/Feature`

- Example: simple HTTP healthcheck test (verified passing locally)
  ```php
  <?php
  
  use function Pest\Laravel\get;
  
  it('loads the home page successfully', function () {
      $response = get('/');
      $response->assertOk();
  });
  ```

- Create a new test
  ```bash
  php artisan make:test Feature/MyFeatureTest --pest
  # or:
  php artisan make:test Unit/MyServiceTest --pest --unit
  ```

- Database in tests
  - Prefer in-memory SQLite for speed, or use the default sqlite file set up during `post-create-project-cmd`.
  - Use Pest/Laravel traits as needed:
    - `RefreshDatabase` for transactional refresh
    - `WithoutMiddleware` for API unit focus, etc.

- Livewire/Volt testing
  - Use `Livewire::test()` patterns or Pest macros once Livewire components are introduced in modules.
  - Example (after modules exist):
    ```php
    Livewire::test(\Modules\Hostels\Http\Livewire\HostelList::class)
        ->assertStatus(200)
        ->assertSee('Hostels');
    ```


## 3) Target Architecture — Modules + Livewire + Filament

You must build one Laravel application that loads domain features from toggleable Nwidart modules. All operational UIs use Livewire v3; admin/back-office uses Filament. Soft multi-tenancy via `company_id` across all domain tables.

- Module layout (Nwidart) — when added, each module under `Modules/<Name>` must include:
  ```text
  Modules/
    Core/
    Hostels/
    Farms/
    Construction/
    ManufacturingWater/
    ManufacturingPaper/
    HR/
    ProcurementInventory/
    Fleet/
    Finance/
  ```

  Typical module skeleton:
  ```text
  Modules/Hostels/
    Config/config.php
    module.json               # has "enabled": true/false
    Routes/web.php
    Http/Livewire/...
    Models/...
    Filament/Resources/...
    Database/migrations/...
    Providers/HostelsServiceProvider.php
  ```

- Module toggling rule
  - If a module is disabled in `module.json`, its routes, Livewire components, Filament resources, and menus MUST NOT load.
  - Enforce in the module service provider:
    ```php
    public function boot(): void
    {
        if (! module()->isEnabled($this->moduleName)) {
            return; // don’t register anything
        }
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        // register Livewire components, Filament resources, views, translations, etc.
    }
    ```

- Livewire v3 placement
  - All front-end/operational screens live inside their module’s `Http/Livewire` namespace; route directly to component classes in `Routes/web.php`.

- Filament placement
  - Admin/HQ resources live under `Modules/<Module>/Filament/Resources` and register only when the module is enabled.

- Soft multi-tenancy (Core)
  - Every domain table includes `company_id`.
  - Core provides `Company` model, user-company pivot, middleware `SetCompany` that:
    - Resolves company by slug
    - Checks membership via `company_user`
    - Sets `current_company_id` on the user/session/global helper
    - 403s if unauthorized

- Routing pattern (per module)
  ```php
  Route::middleware(['web', 'auth', 'set-company:hostels'])
      ->prefix('hostels')
      ->name('hostels.')
      ->group(function () {
          Route::get('/', \Modules\Hostels\Http\Livewire\HostelList::class)->name('index');
          Route::get('{hostel:slug}', \Modules\Hostels\Http\Livewire\Dashboard::class)->name('dashboard');
      });
  ```

- Domain coverage (entities & workflows)
  - Hostels: Hostel/Room/Bed/Tenant/Booking/FeeType with booking lifecycle and optional Finance invoicing.
  - Farms: Farm/Field/CropCycle/LivestockGroup/Tasks/Inputs/Harvest/Sales with dashboards and records.
  - Construction: ConstructionProject/BoqItem/ProjectTask/Timesheets/Materials/Issues.
  - ManufacturingWater: Plant/Product/Batch/QualityTest.
  - ManufacturingPaper: Plant/Product/Batch/ProductionLine/Logs.
  - HR: Department/JobPosition/Employee/Attendance/Leave.
  - ProcurementInventory: Supplier/ItemCategory/Item/StockLocation/PR/PO/GRN/StockMovement.
  - Fleet: Vehicle/Driver/Trip/Fuel/Maintenance.
  - Finance: Account/Journal/Invoice/Payment with cross-module invoice links.


## 4) How to add Modules & Filament to this repo

The current app doesn’t yet include Nwidart Modules or Filament. Standardize on these steps:

1. Install modules package
   ```bash
   composer require nwidart/laravel-modules
   php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
   php artisan module:make Core
   # Repeat for: Hostels, Farms, Construction, ManufacturingWater, ManufacturingPaper, HR, ProcurementInventory, Fleet, Finance
   ```

2. Install Filament v3/v4
   ```bash
   composer require filament/filament
   php artisan filament:install
   ```

3. Livewire v3 is already present (Volt/Flux). Ensure component namespaces are PSR-4 within modules; don’t place operational screens in `app/Livewire`.

4. Register module providers in `config/app.php` only if needed; generally, Nwidart handles per-module providers via discovery.

5. Implement `SetCompany` middleware in `Modules/Core/Http/Middleware/SetCompany.php`; register alias `set-company` in Core service provider.

6. Ensure Filament navigation only includes enabled modules (guard in service provider and/or use dynamic menu builders that check `module()->isEnabled`).


## 5) Code Style, Quality, and Conventions

- Code style
  - Use Laravel Pint (already in `require-dev`). Run:
    ```bash
    ./vendor/bin/pint
    ```
  - Follow framework defaults; match existing import order and formatting.

- Naming & structure
  - Keep Eloquent models inside their module.
  - Use route model binding by slug for primary public entities.
  - Always include `company_id` and enforce scoping via middleware or global scopes where appropriate.

- Testing conventions
  - New features must ship with Feature tests covering happy path and authorization edge cases.
  - For Livewire, assert render, validation, and state transitions.
  - For Filament resources: use Filament testing utilities for forms/tables where relevant.

- Database
  - Prefer UUIDs only where justified; slugs are required for public routes.
  - Use standard timestamps; soft deletes if entities are user-facing and recoverable.

- Security
  - Authentication: Fortify is installed; keep 2FA pathways intact.
  - Authorization: Spatie Roles/Permissions via Core + Shield once added; ensure checks exist in route groups and components.


## 6) Troubleshooting

- Blank screen or Livewire errors during dev
  - Verify `composer dev` is running; pail will show Livewire exceptions and failed requests.

- Routes/components missing
  - Confirm the corresponding module’s `module.json` has `"enabled": true` and that the module’s service provider bails out early when disabled.

- Config/test flakiness
  - Clear caches: `php artisan config:clear && php artisan route:clear && php artisan view:clear`.
  - Rebuild assets if frontend issues persist: `npm run build`.


## 7) Verified Example – Running Tests

- A minimal Feature test that hits `/` was created and executed successfully with `composer test` (all suites passed). Use the example in Section 2 as a template for new tests. This file was only for demonstration and should not be required long-term.


## 8) Scope Alignment: Kharis ERP Rules (Single Source of Truth)

All generated code must respect these rules:
- One Laravel app with Nwidart modules per domain (toggleable).
- Livewire v3 for all operational/front-end screens inside modules.
- Filament resources for admin/HQ, inside modules.
- Soft multi-tenancy via `company_id` across all domain tables.
- Entities, fields, and workflows outlined above.
