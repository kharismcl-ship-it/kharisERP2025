<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
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
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $month = now()->month;
        $year = now()->year;

        $countToday     = CsVisitor::query()->whereDate('check_in_at', $today)->count();
        $countYesterday = CsVisitor::query()->whereDate('check_in_at', $yesterday)->count();
        $countWeek      = CsVisitor::query()->whereBetween('check_in_at', [$weekStart, $weekEnd])->count();
        $countMonth     = CsVisitor::query()->whereMonth('check_in_at', $month)->whereYear('check_in_at', $year)->count();

        return [
            'today' => Tab::make('Today')
                ->badge($countToday)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('check_in_at', $today)),

            'yesterday' => Tab::make('Yesterday')
                ->badge($countYesterday)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('check_in_at', $yesterday)),

            'this_week' => Tab::make('This Week')
                ->badge($countWeek)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('check_in_at', [$weekStart, $weekEnd])),

            'this_month' => Tab::make('This Month')
                ->badge($countMonth)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('check_in_at', $month)->whereYear('check_in_at', $year)),

            'all' => Tab::make('All'),
        ];
    }
}
