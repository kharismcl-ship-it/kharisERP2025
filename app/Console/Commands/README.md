# Module Filament Commands

Powerful commands to generate Filament v4 resources and plugins within Laravel modules using the `nwidart/laravel-modules` package.

## Commands

### Resource Generator
```bash
php artisan module:make-filament-resource {module} {name} [options]
```

### Plugin Generator
```bash
php artisan module:make-filament-plugin {module}
```

## Installation

The command is automatically available once placed in `app/Console/Commands/`. Make sure you have the `nwidart/laravel-modules` package installed.

## Usage

### Basic Usage

```bash
# Generate a basic Filament resource
php artisan module:make-filament-resource CRM Client
```

This creates:
- `Modules/CRM/app/Filament/Resources/ClientResource.php`
- `Modules/CRM/app/Filament/Resources/ClientResource/Pages/`
  - `ListClients.php`
  - `CreateClient.php`
  - `EditClient.php`
  - `ViewClient.php`

### Advanced Options

#### Custom Model Name
```bash
php artisan module:make-filament-resource CRM Client --model=Customer
```

#### With Soft Deletes
```bash
php artisan module:make-filament-resource CRM Client --soft-deletes
```

#### Simple Resource (No View Page)
```bash
php artisan module:make-filament-resource CRM Client --simple
```

#### Combined Options
```bash
php artisan module:make-filament-resource CRM Client --model=Customer --soft-deletes --simple
```

## Generated Files Structure (Filament v4)

```
Modules/{module}/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── {ResourceName}Resource.php
│   │   │   └── {ResourceName}Resource/
│   │   │       ├── Pages/
│   │   │       │   ├── List{ResourceName}.php
│   │   │       │   ├── Create{ResourceName}.php
│   │   │       │   ├── Edit{ResourceName}.php
│   │   │       │   └── View{ResourceName}.php (unless --simple)
│   │   │       ├── Schemas/
│   │   │       │   └── {ModelName}Form.php
│   │   │       └── Tables/
│   │   │           └── {ModelName}Table.php
```

## Features

### Automatic Namespace Handling
- Proper namespace generation for modules
- Correct model namespace resolution
- Automatic service provider registration attempt

### Flexible Model Naming
- Singular/plural detection from resource name
- Custom model names via `--model` option
- Proper model namespace resolution

### Form and Table Schemas (Filament v4)
- Separate Form schema classes in `Schemas/{ModelName}Form.php`
- Separate Table schema classes in `Tables/{ModelName}Table.php`
- Basic form schema with name and description fields
- Searchable and sortable table columns
- Timestamp columns for created_at/updated_at
- Soft delete support with proper column handling

### Service Provider Integration
- Attempts to automatically register resources in module service provider
- Adds proper imports and resource registration
- Falls back gracefully if service provider doesn't exist

## Customization

### Stub Files

The command uses stub files located in `app/Console/Commands/stubs/`:

- `filament-resource.stub` - Main resource class
- `filament-list-page.stub` - List records page
- `filament-create-page.stub` - Create record page
- `filament-edit-page.stub` - Edit record page
- `filament-view-page.stub` - View record page
- `filament-form-schema.stub` - Form schema class (Filament v4)
- `filament-table-schema.stub` - Table schema class (Filament v4)

### Modifying Stubs

You can customize the generated code by modifying the stub files:

1. Edit the stub files in `app/Console/Commands/stubs/`
2. The stubs use placeholder variables like `{{ namespace }}`, `{{ class }}`, etc.
3. Common placeholders:
   - `{{ namespace }}` - PHP namespace
   - `{{ class }}` - Class name
   - `{{ model }}` - Model class name
   - `{{ modelNamespace }}` - Full model namespace
   - `{{ slug }}` - URL slug
   - `{{ formSchema }}` - Form components
   - `{{ tableSchema }}` - Table columns

## Examples

### Basic Client Resource (Filament v4)

```bash
php artisan module:make-filament-resource CRM Client --model=Client
```

Generates a Client resource with:
- Separate `ClientForm` class in `Schemas/` folder
- Separate `ClientTable` class in `Tables/` folder  
- Name field (required, max 255 chars)
- Description textarea
- Created/updated timestamp fields
- Searchable name column
- Sortable timestamp columns
- Proper Filament v4 structure with schema-based forms

### Product Resource with Soft Deletes (Filament v4)

```bash
php artisan module:make-filament-resource Inventory Product --model=Product --soft-deletes
```

Adds:
- Separate `ProductForm` class with soft delete handling
- Separate `ProductTable` class with deleted_at column
- Soft delete functionality
- Deleted at column in table
- Proper soft delete handling
- Filament v4 schema-based structure

## Integration with Module Service Providers

The command attempts to automatically register the generated resource in the module's service provider:

1. Looks for `{Module}ServiceProvider.php`
2. Adds the proper import statement
3. Registers the resource in the `$resources` array

If the service provider doesn't exist or can't be modified, the command will still succeed but show a reminder to manually register the resource.

## Manual Registration

If automatic registration fails, manually add to your module service provider:

```php
use Modules\CRM\Filament\Resources\ClientResource;

protected array $resources = [
    ClientResource::class,
];
```

---

# Module Filament Plugin Generator

A command to automatically discover and register all Filament resources and pages within a module by creating a Filament plugin class.

## Command

```bash
php artisan module:make-filament-plugin {module}
```

## Usage

### Basic Usage

```bash
# Generate a Filament plugin for a module
php artisan module:make-filament-plugin CRM
```

This command:
1. Discovers all Filament resources in `Modules/{module}/app/Filament/Resources/`
2. Discovers all Filament pages in `Modules/{module}/app/Filament/Pages/`
3. Creates a plugin class that automatically registers all discovered resources and pages
4. Generates proper use statements and array content

## Generated Files Structure

```
Modules/{module}/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── ClientResource.php
│   │   │   └── ...
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   └── ...
│   │   └── {Module}FilamentPlugin.php  ← Generated plugin
```

## Features

### Automatic Discovery
- Scans the module's Filament Resources directory for all `*Resource.php` files
- Scans the module's Filament Pages directory for all `.php` files
- Validates that discovered classes actually exist
- Handles proper namespace generation

### Plugin Class Generation
- Creates a Filament plugin class with proper namespace
- Generates use statements for all discovered resources and pages
- Creates arrays for resource and page registration
- Uses simple class names for cleaner registration

### Integration Ready
- The generated plugin can be directly registered in your Filament panel configuration
- Provides a clean way to organize module-specific Filament components
- Follows Filament v4 plugin patterns

## Example Output

For a CRM module with `ClientResource` and `Dashboard` page:

```php
<?php

namespace Modules\CRM\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\CRM\Filament\Resources\ClientResource;
use Modules\CRM\Filament\Pages\Dashboard;

class CRMFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'crm';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ClientResource::class,
            ])
            ->pages([
                Dashboard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

## Registration in Panel Configuration

After generating the plugin, register it in your Filament panel configuration:

```php
use Modules\CRM\Filament\CRMFilamentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            CRMFilamentPlugin::class,
        ]);
}
```

## Best Practices

1. **Run After Resource Creation**: Generate the plugin after creating all your Filament resources and pages
2. **Module Organization**: Use this command to keep each module's Filament components self-contained
3. **Automatic Updates**: Re-run the command when adding new resources/pages to automatically update the plugin
4. **Version Control**: Commit the generated plugin class to version control

## Troubleshooting

### No Resources or Pages Found
```
Discovered and registered 0 resources and 0 pages.
```
Solution: Make sure you have created Filament resources and pages in the module first

### Class Not Found Errors
```
Class 'Modules\CRM\Filament\Resources\ClientResource' not found
```
Solution: Run `composer dump-autoload` to refresh the class autoloader

### Module Not Found
```
Module {module} does not exist!
```
Solution: Create the module first using `php artisan module:make {module}`

## Dependencies

- Laravel 8+
- Filament v4
- nwidart/laravel-modules package
- Symfony Finder component (for file discovery)

## Integration with Resource Generator

These commands work together seamlessly:

1. First, generate Filament resources:
   ```bash
   php artisan module:make-filament-resource CRM Client
   ```

2. Then, generate the plugin to automatically register them:
   ```bash
   php artisan module:make-filament-plugin CRM
   ```

This creates a complete, self-contained Filament module with automatic registration.

## Best Practices

1. **Use Descriptive Names**: Choose clear resource names (Client, Product, Invoice)
2. **Model Consistency**: Keep resource names and model names consistent
3. **Module Organization**: Group related resources in appropriate modules
4. **Customize Stubs**: Modify stubs to match your project's coding standards
5. **Test Generation**: Always test generated resources before production use

## Troubleshooting

### Module Not Found
```
Module {module} does not exist!
```
Solution: Create the module first using `php artisan module:make {module}`

### Service Provider Issues
```
Could not automatically register resource in service provider
```
Solution: Manually register the resource as shown in the Manual Registration section

### Permission Errors
```
File creation failed: Permission denied
```
Solution: Check directory permissions in the Modules directory

## Dependencies

- Laravel 8+
- Filament v4
- nwidart/laravel-modules package

## Support

For issues or feature requests, please check:
- The generated code structure
- Module service provider configuration
- Filament documentation for resource customization

## Version History

- v1.0.0: Initial release with basic resource generation
- Features: Basic CRUD, soft deletes, simple mode, service provider integration
- v2.0.0: Filament v4 support with separate Schemas/Tables structure
- Features: Separate Form and Table schema classes, proper model-based file naming
- v2.1.0: Added Filament plugin generator
- Features: Automatic discovery and registration of resources and pages, plugin class generation

---

**Note**: This command is designed for Filament v4 and Laravel modules. Make sure your project meets these requirements before use.