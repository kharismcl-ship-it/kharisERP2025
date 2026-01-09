<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MakeModuleFilamentResource extends Command
{
    protected $signature = 'module:make-filament-resource {module : The name of the module} {name : The name of the resource} {--model= : The model class name} {--soft-deletes : Generate with soft deletes} {--simple : Generate a simple resource}';

    protected $description = 'Create a new Filament resource within a module';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $module = $this->argument('module');
        $resourceName = $this->argument('name');
        $model = $this->option('model') ?? Str::singular($resourceName);
        $softDeletes = $this->option('soft-deletes');
        $simple = $this->option('simple');

        $modulePath = base_path("Modules/{$module}");

        if (! $this->files->exists($modulePath)) {
            $this->error("Module {$module} does not exist!");

            return 1;
        }

        $this->createResourceClass($module, $resourceName, $model, $softDeletes, $simple);
        $this->createPages($module, $resourceName, $model, $simple);
        $this->createSchemasAndTables($module, $resourceName, $model);
        $this->updateModuleServiceProvider($module, $resourceName);

        $this->info("Filament resource {$resourceName} created successfully in module {$module}!");
        $this->info("Don't forget to register the resource in your module's service provider.");
    }

    protected function createResourceClass($module, $resourceName, $model, $softDeletes, $simple)
    {
        $resourcePath = base_path("Modules/{$module}/app/Filament/Resources");
        $this->ensureDirectoryExists($resourcePath);

        $stub = $this->getStub('resource');
        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ model }}',
                '{{ modelNamespace }}',
                '{{ slug }}',
                '{{ navigationIcon }}',
                '{{ navigationGroup }}',
                '{{ resourceName }}',
                '{{ pluralResourceName }}',
                '{{ formSchema }}',
                '{{ tableSchema }}',
                '{{ softDeletes }}',
                '{{ module }}',
            ],
            [
                "Modules\\{$module}\\Filament\\Resources",
                $resourceName.'Resource',
                $model,
                "Modules\\{$module}\\Models\\{$model}",
                Str::kebab(Str::plural($resourceName)),
                'heroicon-o-rectangle-stack',
                $module,
                $resourceName,
                Str::plural($resourceName),
                $this->getFormSchema($module, $model, $simple),
                $this->getTableSchema($module, $model, $softDeletes, $simple),
                $softDeletes ? "\n    protected static bool \$softDeletes = true;" : '',
                $module,
            ],
            $stub
        );

        $this->files->put("{$resourcePath}/{$resourceName}Resource.php", $content);
    }

    protected function createPages($module, $resourceName, $model, $simple)
    {
        $pagesPath = base_path("Modules/{$module}/app/Filament/Resources/{$resourceName}Resource/Pages");
        $this->ensureDirectoryExists($pagesPath);

        $pages = ['List', 'Create', 'Edit'];

        if (! $simple) {
            $pages[] = 'View';
        }

        foreach ($pages as $page) {
            $stub = $this->getStub(strtolower($page).'-page');

            // Use plural name for List pages, singular for others
            $pageClass = $page === 'List'
                ? "{$page}".Str::plural($resourceName)
                : "{$page}{$resourceName}";

            $content = str_replace(
                [
                    '{{ namespace }}',
                    '{{ class }}',
                    '{{ resource }}',
                    '{{ resourceClass }}',
                    '{{ model }}',
                    '{{ modelNamespace }}',
                    '{{ module }}',
                    '{{ resourceName }}',
                ],
                [
                    "Modules\\{$module}\\Filament\\Resources\\{$resourceName}Resource\\Pages",
                    $pageClass,
                    Str::kebab($resourceName),
                    "{$resourceName}Resource",
                    $model,
                    "Modules\\{$module}\\Models\\{$model}",
                    $module,
                    $resourceName,
                ],
                $stub
            );

            $this->files->put("{$pagesPath}/{$pageClass}.php", $content);
        }
    }

    protected function createSchemasAndTables($module, $resourceName, $model)
    {
        // Create Schemas directory and Form schema
        $schemasPath = base_path("Modules/{$module}/app/Filament/Resources/{$resourceName}Resource/Schemas");
        $this->ensureDirectoryExists($schemasPath);

        $formStub = $this->getStub('form-schema');
        $formSchemaContent = $this->getFormSchema($module, $model, false);

        $formContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ formSchema }}',
            ],
            [
                "Modules\\{$module}\\Filament\\Resources\\{$resourceName}Resource\\Schemas",
                "{$model}Form",
                $formSchemaContent,
            ],
            $formStub
        );
        $this->files->put("{$schemasPath}/{$model}Form.php", $formContent);

        // Create Tables directory and Table schema
        $tablesPath = base_path("Modules/{$module}/app/Filament/Resources/{$resourceName}Resource/Tables");
        $this->ensureDirectoryExists($tablesPath);

        $tableStub = $this->getStub('table-schema');
        $tableSchemaContent = $this->getTableSchema($module, $model, false, false);

        $tableContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ tableSchema }}',
            ],
            [
                "Modules\\{$module}\\Filament\\Resources\\{$resourceName}Resource\\Tables",
                "{$model}Table",
                $tableSchemaContent,
            ],
            $tableStub
        );
        $this->files->put("{$tablesPath}/{$model}Table.php", $tableContent);
    }

    protected function updateModuleServiceProvider($module, $resourceName)
    {
        $providerPath = base_path("Modules/{$module}/app/Providers/{$module}ServiceProvider.php");

        if ($this->files->exists($providerPath)) {
            $content = $this->files->get($providerPath);

            if (strpos($content, 'Filament\\.*Resources') === false) {
                $resourceClass = "Modules\\{$module}\\Filament\\Resources\\{$resourceName}Resource";

                $import = "use {$resourceClass};";
                $registration = "\n        \\Filament\\\\Resources\\\\.*Resources::class => [\n            {$resourceName}Resource::class,\n        ],";

                if (strpos($content, $import) === false) {
                    $content = preg_replace(
                        '/use (.*);\nclass/s',
                        "$0\n{$import}",
                        $content
                    );
                }

                if (strpos($content, 'Filament\\.*Resources') === false) {
                    $content = preg_replace(
                        '/protected array \$resources = \[\s*\];/',
                        "protected array \$resources = [{$registration}\n    ];",
                        $content
                    );
                }

                $this->files->put($providerPath, $content);
            }
        }
    }

    protected function getFormSchema($module, $model, $simple)
    {
        $modelClass = "Modules\\{$module}\\Models\\{$model}";

        if (! class_exists($modelClass)) {
            return $this->getDefaultFormSchema($simple);
        }

        $modelInstance = new $modelClass;
        $fillable = $modelInstance->getFillable();

        if (empty($fillable)) {
            return $this->getDefaultFormSchema($simple);
        }

        $schema = '';

        foreach ($fillable as $field) {
            $schema .= $this->getFieldForColumn($module, $model, $field);
        }

        return $schema;
    }

    protected function getDefaultFormSchema($simple)
    {
        if ($simple) {
            return "\n                Forms\\Components\\TextInput::make('name')\n                    ->required()\n                    ->maxLength(255),\n                Forms\\Components\\Textarea::make('description')\n                    ->maxLength(65535)\n                    ->columnSpanFull(),";
        }

        return "\n                Forms\\Components\\TextInput::make('name')\n                    ->required()\n                    ->maxLength(255),\n                Forms\\Components\\Textarea::make('description')\n                    ->maxLength(65535)\n                    ->columnSpanFull(),\n                Forms\\Components\\DateTimePicker::make('created_at')\n                    ->disabled()\n                    ->dehydrated()\n                    ->default(now()),\n                Forms\\Components\\DateTimePicker::make('updated_at')\n                    ->disabled()\n                    ->dehydrated()\n                    ->default(now()),\n            ";
    }

    protected function getFieldTypeFromDatabase($module, $model, $field)
    {
        try {
            $tableName = Str::snake(Str::plural($model));
            $connection = config('database.default');
            $schema = DB::connection($connection)->getDoctrineSchemaManager();
            $database = DB::connection($connection)->getDatabaseName();

            $tableDetails = $schema->listTableDetails($tableName);
            $column = $tableDetails->getColumn($field);

            return $column->getType()->getName();
        } catch (\Exception $e) {
            // Fallback to string if we can't determine the type
            return 'string';
        }
    }

    protected function getFieldForColumn($module, $model, $field)
    {
        // Skip timestamps and auto-increment fields
        if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
            return '';
        }

        // Check if field is a foreign key
        if (Str::endsWith($field, '_id')) {
            $relationship = Str::beforeLast($field, '_id');
            $relatedModel = Str::studly($relationship);

            return "\n                Forms\\Components\\Select::make('{$field}')\n                    ->relationship('{$relationship}', 'name')\n                    ->required(),";
        }

        // Check field type from database
        $fieldType = $this->getFieldTypeFromDatabase($module, $model, $field);

        switch ($fieldType) {
            case 'text':
            case 'longtext':
                return "\n                Forms\\Components\\Textarea::make('{$field}')\n                    ->maxLength(65535)\n                    ->columnSpanFull(),";

            case 'datetime':
            case 'timestamp':
                return "\n                Forms\\Components\\DateTimePicker::make('{$field}')\n                    ->required(),";

            case 'boolean':
                return "\n                Forms\\Components\\Toggle::make('{$field}')\n                    ->required(),";

            case 'integer':
            case 'bigint':
                return "\n                Forms\\Components\\TextInput::make('{$field}')\n                    ->numeric()\n                    ->required(),";

            default:
                return "\n                Forms\\Components\\TextInput::make('{$field}')\n                    ->required()\n                    ->maxLength(255),\n            ";
        }
    }

    protected function getColumnForField($module, $model, $field)
    {
        // Skip timestamps and auto-increment fields
        if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
            return '';
        }

        // Check if field is a foreign key
        if (Str::endsWith($field, '_id')) {
            $relationship = Str::beforeLast($field, '_id');
            $relatedModel = Str::studly($relationship);

            return "\n                Tables\\Columns\\TextColumn::make('{$field}')\n                    ->searchable()\n                    ->sortable(),";
        }

        // Check field type from database
        $fieldType = $this->getFieldTypeFromDatabase($module, $model, $field);

        switch ($fieldType) {
            case 'text':
            case 'longtext':
                return "\n                Tables\\Columns\\TextColumn::make('{$field}')\n                    ->searchable()\n                    ->sortable()\n                    ->limit(50),\n            ";

            case 'datetime':
            case 'timestamp':
                return "\n                Tables\\Columns\\TextColumn::make('{$field}')\n                    ->dateTime()\n                    ->sortable(),";

            case 'boolean':
                return "\n                Tables\\Columns\\IconColumn::make('{$field}')\n                    ->boolean()\n                    ->sortable(),";

            case 'integer':
            case 'bigint':
                return "\n                Tables\\Columns\\TextColumn::make('{$field}')\n                    ->numeric()\n                    ->sortable(),";

            default:
                return "\n                Tables\\Columns\\TextColumn::make('{$field}')\n                    ->searchable()\n                    ->sortable(),\n            ";
        }
    }

    protected function getTableSchema($module, $model, $softDeletes, $simple)
    {
        $modelClass = "Modules\\{$module}\\Models\\{$model}";

        if (! class_exists($modelClass)) {
            return $this->getDefaultTableSchema($simple, $softDeletes);
        }

        $modelInstance = new $modelClass;
        $fillable = $modelInstance->getFillable();

        if (empty($fillable)) {
            return $this->getDefaultTableSchema($simple, $softDeletes);
        }

        $columns = '';

        foreach ($fillable as $field) {
            $columns .= $this->getColumnForField($module, $model, $field);
        }

        if (! $simple) {
            $columns .= "\n                Tables\\Columns\\TextColumn::make('created_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),\n                Tables\\Columns\\TextColumn::make('updated_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),";
        }

        if ($softDeletes) {
            $columns .= "\n                Tables\\Columns\\TextColumn::make('deleted_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),";
        }

        return $columns;
    }

    protected function getDefaultTableSchema($simple, $softDeletes)
    {
        $columns = "\n                Tables\\Columns\\TextColumn::make('name')\n                    ->searchable()\n                    ->sortable(),";

        if (! $simple) {
            $columns .= "\n                Tables\\Columns\\TextColumn::make('created_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),\n                Tables\\Columns\\TextColumn::make('updated_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),";
        }

        if ($softDeletes) {
            $columns .= "\n                Tables\\Columns\\TextColumn::make('deleted_at')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true),";
        }

        return $columns;
    }

    protected function getStub($type)
    {
        $stubs = [
            'resource' => __DIR__.'/stubs/filament-resource.stub',
            'list-page' => __DIR__.'/stubs/filament-list-page.stub',
            'create-page' => __DIR__.'/stubs/filament-create-page.stub',
            'edit-page' => __DIR__.'/stubs/filament-edit-page.stub',
            'view-page' => __DIR__.'/stubs/filament-view-page.stub',
            'form-schema' => __DIR__.'/stubs/filament-form-schema.stub',
            'table-schema' => __DIR__.'/stubs/filament-table-schema.stub',
        ];

        return $this->files->exists($stubs[$type])
            ? $this->files->get($stubs[$type])
            : $this->getDefaultStub($type);
    }

    protected function getDefaultStub($type)
    {
        $stubs = [
            'resource' => file_get_contents(__DIR__.'/../../stubs/filament-resource.stub'),
            'list-page' => file_get_contents(__DIR__.'/../../stubs/filament-list-page.stub'),
            'create-page' => file_get_contents(__DIR__.'/../../stubs/filament-create-page.stub'),
            'edit-page' => file_get_contents(__DIR__.'/../../stubs/filament-edit-page.stub'),
            'view-page' => file_get_contents(__DIR__.'/../../stubs/filament-view-page.stub'),
        ];

        return $stubs[$type] ?? '';
    }

    protected function ensureDirectoryExists($path)
    {
        if (! $this->files->exists($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}
