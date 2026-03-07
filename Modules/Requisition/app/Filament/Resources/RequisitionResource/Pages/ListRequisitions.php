<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionResource;
use Modules\Requisition\Filament\Widgets\RequisitionStatsWidget;
use Modules\Requisition\Models\Requisition;

class ListRequisitions extends ListRecords
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RequisitionStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $employeeId     = auth()->user()?->employee?->id;
        $pendingStatus  = ['draft', 'submitted', 'under_review', 'pending_revision'];
        $archiveStatus  = ['fulfilled', 'closed'];

        $myCount      = Requisition::withoutGlobalScopes()->where('requester_employee_id', $employeeId)->count();
        $pendingCount = Requisition::withoutGlobalScopes()->whereIn('status', $pendingStatus)->count();
        $approvedCount = Requisition::withoutGlobalScopes()->where('status', 'approved')->count();
        $overdueCount = Requisition::withoutGlobalScopes()
            ->whereNotNull('due_by')
            ->where('due_by', '<', now()->toDateString())
            ->whereNotIn('status', Requisition::RESOLVED_STATUSES)
            ->count();
        $archiveCount = Requisition::withoutGlobalScopes()->whereIn('status', $archiveStatus)->count();

        return [
            'all' => Tab::make('All Requests'),

            'my' => Tab::make('My Requests')
                ->badge($myCount ?: null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('requester_employee_id', $employeeId)),

            'pending' => Tab::make('Pending')
                ->badge($pendingCount ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', $pendingStatus)),

            'approved' => Tab::make('Approved')
                ->badge($approvedCount ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),

            'overdue' => Tab::make('Overdue')
                ->badge($overdueCount ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotNull('due_by')
                    ->where('due_by', '<', now()->toDateString())
                    ->whereNotIn('status', Requisition::RESOLVED_STATUSES)
                ),

            'archive' => Tab::make('Archive')
                ->badge($archiveCount ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', $archiveStatus)),
        ];
    }
}
