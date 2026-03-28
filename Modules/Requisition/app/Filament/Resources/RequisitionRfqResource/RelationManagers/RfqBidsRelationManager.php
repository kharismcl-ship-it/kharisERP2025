<?php

namespace Modules\Requisition\Filament\Resources\RequisitionRfqResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RfqBidsRelationManager extends RelationManager
{
    protected static string $relationship = 'bids';

    protected static ?string $title = 'Vendor Bids';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bid Details')->schema([
                Grid::make(2)->schema([
                    Select::make('vendor_id')
                        ->label('Vendor')
                        ->relationship('vendor', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('vendor_contact_name')->label('Contact Name')->nullable()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('quoted_amount')->label('Quoted Amount (GHS)')->numeric()->required(),
                    TextInput::make('delivery_days')->label('Delivery (days)')->numeric()->nullable(),
                    TextInput::make('payment_terms')->label('Payment Terms')->nullable()->maxLength(255),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'received'    => 'Received',
                        'shortlisted' => 'Shortlisted',
                        'rejected'    => 'Rejected',
                        'awarded'     => 'Awarded',
                    ])
                    ->default('received')
                    ->required(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')->label('Vendor')->searchable(),
                TextColumn::make('quoted_amount')->money('GHS'),
                TextColumn::make('delivery_days')->label('Delivery Days')->placeholder('—'),
                TextColumn::make('payment_terms')->label('Payment Terms')->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'received'    => 'gray',
                        'shortlisted' => 'info',
                        'rejected'    => 'danger',
                        'awarded'     => 'success',
                        default       => 'gray',
                    }),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                EditAction::make(),
                Action::make('shortlist')
                    ->label('Shortlist')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'received')
                    ->action(fn ($record) => $record->update(['status' => 'shortlisted']))
                    ->requiresConfirmation(),
                Action::make('reject_bid')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['received', 'shortlisted']))
                    ->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->requiresConfirmation(),
                Action::make('award')
                    ->label('Award')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['received', 'shortlisted']))
                    ->requiresConfirmation()
                    ->modalHeading('Award this Bid?')
                    ->modalDescription('This will set the bid as awarded and update the RFQ status to awarded.')
                    ->action(function ($record) {
                        $record->update(['status' => 'awarded']);
                        $record->rfq->update([
                            'awarded_vendor_id' => $record->vendor_id,
                            'status'            => 'awarded',
                            'awarded_at'        => now(),
                        ]);
                        Notification::make()->success()->title('Bid awarded successfully.')->send();
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}