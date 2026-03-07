<?php

namespace Modules\ClientService\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\ClientService\Filament\Resources\CsVisitorBadgeResource\Pages;
use Modules\ClientService\Models\CsVisitorBadge;

class CsVisitorBadgeResource extends Resource
{
    protected static ?string $model = CsVisitorBadge::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Services';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Visitor Badges';

    protected static ?string $modelLabel = 'Visitor Badge';

    protected static ?string $pluralModelLabel = 'Visitor Badges';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('badge_code')
                    ->label('Badge')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('prefix')
                    ->label('Prefix')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'issued'    => 'warning',
                        'void'      => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('company.name')
                    ->label('Company')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('batch_number')
                    ->label('Batch')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('issuedToVisitor.full_name')
                    ->label('Issued To')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('issued_at')
                    ->label('Issued At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('issuedBy.name')
                    ->label('Issued By')
                    ->placeholder('Kiosk')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('badge_code')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'issued'    => 'Issued',
                        'void'      => 'Void',
                    ])
                    ->multiple(),

                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    Action::make('issue_to_visitor')
                        ->label('Assign to Visitor')
                        ->icon('heroicon-o-user-plus')
                        ->color('warning')
                        ->visible(fn (CsVisitorBadge $record) => $record->isAvailable())
                        ->form([
                            \Filament\Forms\Components\Select::make('visitor_id')
                                ->label('Checked-In Visitor')
                                ->options(fn () => \Modules\ClientService\Models\CsVisitor::query()
                                    ->whereNull('check_out_at')
                                    ->whereNull('badge_number')
                                    ->get()
                                    ->mapWithKeys(fn ($v) => [$v->id => "{$v->full_name} — {$v->check_in_at->format('g:i A, D j M')}"]))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (CsVisitorBadge $record, array $data): void {
                            $visitor = \Modules\ClientService\Models\CsVisitor::findOrFail($data['visitor_id']);
                            $record->issueToVisitor($visitor);
                            Notification::make()->success()->title("Badge {$record->badge_code} assigned to {$visitor->full_name}.")->send();
                        }),

                    Action::make('revoke')
                        ->label('Revoke from Visitor')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn (CsVisitorBadge $record) => $record->isIssued())
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Badge')
                        ->form([
                            Textarea::make('reason')->label('Reason (optional)')->rows(2),
                        ])
                        ->action(function (CsVisitorBadge $record, array $data): void {
                            $name = $record->issuedToVisitor?->full_name ?? 'visitor';
                            $record->revokeFromVisitor($data['reason'] ?? null);
                            Notification::make()->success()->title("Badge {$record->badge_code} returned from {$name}.")->send();
                        }),

                    Action::make('void')
                        ->label('Void Badge')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (CsVisitorBadge $record) => ! $record->isVoid() && ! $record->isIssued())
                        ->requiresConfirmation()
                        ->modalHeading('Void Badge')
                        ->modalDescription('This badge will be permanently decommissioned and cannot be reused.')
                        ->form([
                            Textarea::make('reason')->label('Void Reason')->required()->rows(2),
                        ])
                        ->action(function (CsVisitorBadge $record, array $data): void {
                            $record->voidBadge($data['reason']);
                            Notification::make()->warning()->title("Badge {$record->badge_code} has been voided.")->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_void')
                        ->label('Void Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')->label('Void Reason')->required()->rows(2),
                        ])
                        ->action(function ($records, array $data): void {
                            $count = 0;
                            foreach ($records as $badge) {
                                if ($badge->isAvailable()) {
                                    $badge->voidBadge($data['reason']);
                                    $count++;
                                }
                            }
                            Notification::make()->warning()->title("{$count} badge(s) voided.")->send();
                        }),

                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records): void {
                            foreach ($records as $badge) {
                                if ($badge->isIssued()) {
                                    Notification::make()->danger()->title('Cannot delete issued badges.')->send();
                                    $action->halt();
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCsVisitorBadges::route('/'),
        ];
    }
}