<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorProfileResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ClientService\Models\CsVisitor;

class VisitLogRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Visit Log';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('check_in_at')
            ->defaultSort('check_in_at', 'desc')
            ->columns([
                TextColumn::make('check_in_at')
                    ->label('Checked In')
                    ->dateTime('D j M Y, g:i A')
                    ->sortable(),

                TextColumn::make('check_out_at')
                    ->label('Checked Out')
                    ->dateTime('D j M Y, g:i A')
                    ->placeholder('Still Inside')
                    ->sortable(),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(fn (CsVisitor $record): string => $record->duration ?? '—')
                    ->badge()
                    ->color(fn (CsVisitor $record): string => $record->is_checked_out ? 'success' : 'warning'),

                TextColumn::make('badge_number')
                    ->label('Badge')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('purpose_of_visit')
                    ->label('Purpose')
                    ->limit(30)
                    ->tooltip(fn (CsVisitor $record) => $record->purpose_of_visit)
                    ->placeholder('—'),

                TextColumn::make('hostEmployee.full_name')
                    ->label('Host')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('is_checked_out')
                    ->label('Status')
                    ->badge()
                    ->state(fn (CsVisitor $record): string => $record->is_checked_out ? 'Out' : 'In')
                    ->color(fn (CsVisitor $record): string => $record->is_checked_out ? 'success' : 'warning'),

                TextColumn::make('checkedInBy.name')
                    ->label('Checked In By')
                    ->placeholder('Kiosk')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('checkedOutBy.name')
                    ->label('Checked Out By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('checkout')
                    ->label('Check Out Now')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn (CsVisitor $record): bool => ! $record->is_checked_out)
                    ->requiresConfirmation()
                    ->action(function (CsVisitor $record): void {
                        $record->update([
                            'check_out_at'           => now(),
                            'checked_out_by_user_id' => auth()->id(),
                        ]);

                        \Modules\ClientService\Models\CsVisitorBadge::where('issued_to_visitor_id', $record->id)
                            ->where('status', 'issued')
                            ->first()
                            ?->revokeFromVisitor('Checked out from visitor profile page.');
                    }),
            ])
            ->paginated([10, 25, 50])
            ->striped();
    }
}
