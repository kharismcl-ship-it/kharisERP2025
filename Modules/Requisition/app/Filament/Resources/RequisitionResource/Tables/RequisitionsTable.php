<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;

class RequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')->searchable()->limit(35),
                TextColumn::make('company.name')->label('From')->toggleable(),
                TextColumn::make('targetCompany.name')->label('To')->default('Internal')->toggleable(),
                TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'fund'      => 'warning',
                        'material'  => 'info',
                        'equipment' => 'success',
                        'service'   => 'gray',
                        default     => 'primary',
                    }),
                TextColumn::make('urgency')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'                        => 'gray',
                        'submitted'                    => 'info',
                        'under_review'                 => 'warning',
                        'pending_revision'             => 'warning',
                        'approved', 'fulfilled'        => 'success',
                        'rejected', 'cancelled'        => 'danger',
                        'closed'                       => 'gray',
                        default                        => 'gray',
                    }),
                TextColumn::make('total_estimated_cost')
                    ->label('Est. Cost')
                    ->money('GHS')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('due_by')
                    ->label('Due')
                    ->date()
                    ->badge()
                    ->color(fn ($state, $record) => $record?->isOverdue() ? 'danger' : 'gray')
                    ->placeholder('—'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(Requisition::STATUSES),
                SelectFilter::make('request_type')->options(Requisition::TYPES),
                SelectFilter::make('urgency')->options(Requisition::URGENCIES),
                Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn ($query) => $query
                        ->whereNotNull('due_by')
                        ->where('due_by', '<', now()->toDateString())
                        ->whereNotIn('status', ['approved', 'fulfilled', 'rejected'])
                    ),
                Filter::make('cross_company')
                    ->label('Cross-Company Requests')
                    ->query(fn ($query) => $query->whereNotNull('target_company_id')),
                Filter::make('created_today')
                    ->label('Created Today')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
            ])
            ->actions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (Requisition $record) => $record->status === 'draft')
                    ->action(fn (Requisition $record) => $record->update(['status' => 'submitted'])),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Requisition $record) => in_array($record->status, ['submitted', 'under_review']))
                    ->action(fn (Requisition $record) => $record->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ])),

                Action::make('return_for_revision')
                    ->label('Return for Revision')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (Requisition $record) => in_array($record->status, ['submitted', 'under_review', 'approved']))
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Revision Notes')
                            ->helperText('Explain what needs to be changed before re-submission.')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Requisition $record, array $data): void {
                        $record->update([
                            'status'           => 'pending_revision',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        RequisitionActivity::log(
                            $record,
                            'revision_requested',
                            "Returned for revision: {$data['rejection_reason']}",
                        );
                        Notification::make()->warning()->title('Returned for Revision')->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Requisition $record) => in_array($record->status, ['submitted', 'under_review', 'approved']))
                    ->form([
                        Textarea::make('rejection_reason')->label('Rejection Reason')->required()->rows(3),
                    ])
                    ->action(fn (Requisition $record, array $data) => $record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ])),

                Action::make('mark_fulfilled')
                    ->label('Mark Fulfilled')
                    ->icon('heroicon-o-archive-box')
                    ->color('success')
                    ->visible(fn (Requisition $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(fn (Requisition $record) => $record->update([
                        'status'       => 'fulfilled',
                        'fulfilled_at' => now(),
                    ])),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}