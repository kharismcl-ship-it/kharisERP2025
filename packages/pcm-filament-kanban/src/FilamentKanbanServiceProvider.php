<?php

namespace Pcm\FilamentKanban;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentKanbanServiceProvider extends PackageServiceProvider
{
    public static string $name = 'pcm-filament-kanban';

    public static string $viewNamespace = 'pcm-filament-kanban';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'pcm/filament-kanban';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        $cssPath = __DIR__ . '/../resources/dist/filament-kanban.css';

        if (! file_exists($cssPath)) {
            return [];
        }

        return [
            Css::make('pcm-filament-kanban-styles', $cssPath),
        ];
    }
}