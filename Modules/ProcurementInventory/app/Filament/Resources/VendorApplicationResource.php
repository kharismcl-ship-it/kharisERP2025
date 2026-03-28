<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\VendorApplicationResource\Pages;
use Modules\ProcurementInventory\Models\VendorApplication;

class VendorApplicationResource extends Resource
{
    protected static ?string $model = VendorApplication::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Vendor Applications';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('business_type')
                    ->badge()
                    ->label('Type')
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted'    => 'warning',
                        'under_review' => 'info',
                        'approved'     => 'success',
                        'rejected'     => 'danger',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->label('Reviewed'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Applied'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted'    => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved'     => 'Approved',
                        'rejected'     => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('review')
                    ->label('Mark Under Review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn (VendorApplication $record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(function (VendorApplication $record) {
                        $record->update(['status' => 'under_review']);
                        Notification::make()->title('Application marked under review')->success()->send();
                    }),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (VendorApplication $record) => in_array($record->status, ['submitted', 'under_review']))
                    ->requiresConfirmation()
                    ->action(function (VendorApplication $record) {
                        try {
                            $vendor = $record->approve(auth()->user());
                            Notification::make()
                                ->title("Approved — Vendor '{$vendor->name}' created")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (VendorApplication $record) => in_array($record->status, ['submitted', 'under_review']))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (VendorApplication $record, array $data) {
                        $record->reject(auth()->user(), $data['rejection_reason']);
                        Notification::make()->title('Application rejected')->warning()->send();
                    }),

                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorApplications::route('/'),
            'view'  => Pages\ViewVendorApplication::route('/{record}'),
        ];
    }
}