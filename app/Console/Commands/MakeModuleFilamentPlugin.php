<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class MakeModuleFilamentPlugin extends Command
{
    protected $signature = 'module:make-filament-plugin {module : The name of the module}';

    protected $description = 'Create a new Filament plugin within a module';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $module = $this->argument('module');

        $modulePath = base_path("Modules/{$module}");

        if (! $this->files->exists($modulePath)) {
            $this->error("Module {$module} does not exist!");

            return 1;
        }

        // Discover resources and pages
        $discoveredResources = $this->discoverResources($module);
        $discoveredPages = $this->discoverPages($module);

        $this->createPluginClass($module, $discoveredResources, $discoveredPages);

        $this->info("Filament plugin created successfully in module {$module}!");
        $this->info("Discovered and registered {$discoveredResources->count()} resources and {$discoveredPages->count()} pages.");
        $this->info("Don't forget to register the plugin in your panel configuration.");
    }

    protected function discoverResources($module)
    {
        $resourcesPath = base_path("Modules/{$module}/app/Filament/Resources");

        if (! $this->files->exists($resourcesPath)) {
            return collect();
        }

        $finder = new Finder;
        $finder->files()->in($resourcesPath)->name('*Resource.php');

        $resources = collect();

        foreach ($finder as $file) {
            $relativePath = str_replace(base_path("Modules/{$module}/app/Filament/"), '', $file->getRealPath());
            $className = str_replace(['.php', '/'], ['', '\\'], $relativePath);
            $fullClassName = "Modules\\{$module}\\Filament\\{$className}";

            if (class_exists($fullClassName)) {
                $resources->push($fullClassName);
            }
        }

        return $resources;
    }

    protected function discoverPages($module)
    {
        $pagesPath = base_path("Modules/{$module}/app/Filament/Pages");

        if (! $this->files->exists($pagesPath)) {
            return collect();
        }

        $finder = new Finder;
        $finder->files()->in($pagesPath)->name('*.php');

        $pages = collect();

        foreach ($finder as $file) {
            $relativePath = str_replace(base_path("Modules/{$module}/app/Filament/"), '', $file->getRealPath());
            $className = str_replace(['.php', '/'], ['', '\\'], $relativePath);
            $fullClassName = "Modules\\{$module}\\Filament\\{$className}";

            if (class_exists($fullClassName)) {
                $pages->push($fullClassName);
            }
        }

        return $pages;
    }

    protected function createPluginClass($module, $resources, $pages)
    {
        $filamentPath = base_path("Modules/{$module}/app/Filament");
        $this->ensureDirectoryExists($filamentPath);

        $stub = $this->getStub('plugin');
        $pluginName = "{$module}FilamentPlugin";

        // Generate use statements for resources
        $useStatements = $this->generateUseStatements($resources);

        // Generate resources array content with simple class names
        $resourcesContent = $this->generateArrayContent($resources, '            // Register all Filament Resources Class', true);

        // Generate pages array content with simple class names
        $pagesContent = $this->generateArrayContent($pages, '            // Register all Filament Pages Class', true);

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ module }}',
                '{{ moduleLower }}',
                '{{ useStatements }}',
                '            // Register all Filament Resources Class
            // Eg. Resource::class,',
                '            // Register all Filament Pages Class
            // Eg. Page::class,',
            ],
            [
                "Modules\\{$module}\\Filament",
                $pluginName,
                $module,
                Str::lower($module),
                $useStatements,
                $resourcesContent,
                $pagesContent,
            ],
            $stub
        );

        $this->files->put("{$filamentPath}/{$pluginName}.php", $content);
    }

    protected function generateUseStatements($items)
    {
        if ($items->isEmpty()) {
            return '';
        }

        $useStatements = '';
        foreach ($items as $item) {
            $useStatements .= "\nuse {$item};";
        }

        return $useStatements;
    }

    protected function generateArrayContent($items, $defaultComment, $useSimpleClassNames = false)
    {
        if ($items->isEmpty()) {
            return $defaultComment;
        }

        $content = '';
        foreach ($items as $item) {
            $className = $useSimpleClassNames ? class_basename($item) : $item;
            $content .= "            {$className}::class,\n";
        }

        return rtrim($content);
    }

    protected function getStub($type)
    {
        $stubPath = __DIR__.'/stubs/filament-plugin.stub';

        if ($this->files->exists($stubPath)) {
            return $this->files->get($stubPath);
        }

        return $this->getDefaultStub($type);
    }

    protected function getDefaultStub($type)
    {
        $stubs = [
            'plugin' => file_get_contents(__DIR__.'/../../stubs/filament-plugin.stub'),
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
