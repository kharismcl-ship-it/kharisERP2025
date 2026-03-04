<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Modules\ClientService\Filament\Resources\CsVisitorResource;
use Modules\ClientService\Models\CsVisitor;

class ListCsVisitors extends ListRecords
{
    protected static string $resource = CsVisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'today' => Tab::make('Today')
                ->badge(CsVisitor::whereDate('check_in_at', today())->count())
                ->modifyQueryUsing(fn ($q) => $q->whereDate('check_in_at', today())),
            'yesterday' => Tab::make('Yesterday')
                ->badge(CsVisitor::whereDate('check_in_at', today()->subDay())->count())
                ->modifyQueryUsing(fn ($q) => $q->whereDate('check_in_at', today()->subDay())),
            'this_week' => Tab::make('This Week')
                ->badge(CsVisitor::whereBetween('check_in_at', [now()->startOfWeek(), now()->endOfWeek()])->count())
                ->modifyQueryUsing(fn ($q) => $q->whereBetween('check_in_at', [now()->startOfWeek(), now()->endOfWeek()])),
            'this_month' => Tab::make('This Month')
                ->badge(CsVisitor::whereMonth('check_in_at', now()->month)->whereYear('check_in_at', now()->year)->count())
                ->modifyQueryUsing(fn ($q) => $q->whereMonth('check_in_at', now()->month)->whereYear('check_in_at', now()->year)),
            'all' => Tab::make('All'),
        ];
    }
}
