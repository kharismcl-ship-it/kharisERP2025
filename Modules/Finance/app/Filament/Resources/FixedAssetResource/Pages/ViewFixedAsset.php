<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Filament\Resources\FixedAssetResource;
use Modules\Finance\Models\FixedAssetDepreciationRun;
use Modules\Finance\Models\FixedAssetTransfer;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class ViewFixedAsset extends ViewRecord
{
    protected static string $resource = FixedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status === 'active'),

            // ── Record Depreciation ──────────────────────────────────────────
            Action::make('record_depreciation')
                ->label('Record Depreciation')
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->visible(fn () => $this->record->status === 'active')
                ->form([
                    DatePicker::make('period_end_date')
                        ->label('Period End Date')
                        ->required()
                        ->default(now()->endOfMonth()),
                    TextInput::make('amount')
                        ->label('Depreciation Amount (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->required()
                        ->default(fn () => $this->record->monthlyDepreciation())
                        ->helperText('Default: monthly straight-line. Adjust if needed.'),
                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2)
                        ->nullable(),
                ])
                ->modalHeading('Record Depreciation')
                ->modalDescription('Posts a depreciation entry and logs it to the Depreciation History tab.')
                ->action(function (array $data) {
                    $amount     = (float) $data['amount'];
                    $accumBefore = (float) $this->record->accumulated_depreciation;
                    $maxAccum   = (float) $this->record->cost - (float) $this->record->residual_value;

                    if ($accumBefore + $amount > $maxAccum) {
                        $amount = max(0, $maxAccum - $accumBefore);
                        Notification::make()
                            ->title('Amount capped at remaining depreciable value (GHS ' . number_format($amount, 2) . ')')
                            ->warning()
                            ->send();
                    }

                    if ($amount <= 0) {
                        Notification::make()->title('Asset is already fully depreciated')->warning()->send();
                        return;
                    }

                    $accumAfter = $accumBefore + $amount;
                    $journalEntry = null;

                    DB::transaction(function () use ($data, $amount, $accumBefore, $accumAfter, &$journalEntry) {
                        // Update accumulated depreciation
                        $this->record->update(['accumulated_depreciation' => $accumAfter]);

                        // Post GL Journal Entry if accounts are configured on category
                        $category = $this->record->category;
                        if ($category?->depreciation_account_id && $category?->accumulated_depreciation_account_id) {
                            $ref = 'DEP-' . $this->record->asset_code . '-' . now()->format('Ymd');

                            $journalEntry = JournalEntry::create([
                                'company_id'  => $this->record->company_id,
                                'date'        => $data['period_end_date'],
                                'reference'   => $ref,
                                'description' => 'Depreciation — ' . $this->record->name . ' (' . $this->record->asset_code . ')',
                            ]);

                            // Debit: Depreciation Expense
                            JournalLine::create([
                                'journal_entry_id' => $journalEntry->id,
                                'account_id'       => $category->depreciation_account_id,
                                'debit'            => $amount,
                                'credit'           => 0,
                            ]);

                            // Credit: Accumulated Depreciation
                            JournalLine::create([
                                'journal_entry_id' => $journalEntry->id,
                                'account_id'       => $category->accumulated_depreciation_account_id,
                                'debit'            => 0,
                                'credit'           => $amount,
                            ]);
                        }

                        // Log the depreciation run
                        FixedAssetDepreciationRun::create([
                            'fixed_asset_id'     => $this->record->id,
                            'period_end_date'    => $data['period_end_date'],
                            'amount'             => $amount,
                            'accumulated_before' => $accumBefore,
                            'accumulated_after'  => $accumAfter,
                            'journal_entry_id'   => $journalEntry?->id,
                            'posted_by_user_id'  => Auth::id(),
                            'notes'              => $data['notes'] ?? null,
                        ]);
                    });

                    $this->refreshFormData(['accumulated_depreciation']);

                    $message = 'Depreciation recorded — GHS ' . number_format($amount, 2);
                    if ($journalEntry) {
                        $message .= ' | Journal: ' . $journalEntry->reference;
                    }

                    Notification::make()->title($message)->success()->send();
                }),

            // ── Dispose Asset ────────────────────────────────────────────────
            Action::make('dispose')
                ->label('Dispose Asset')
                ->icon('heroicon-o-archive-box-x-mark')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'active')
                ->form([
                    DatePicker::make('disposal_date')
                        ->label('Disposal Date')
                        ->required()
                        ->default(now()),
                    TextInput::make('disposal_amount')
                        ->label('Disposal / Sale Proceeds (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),
                    Textarea::make('notes')
                        ->label('Reason / Notes')
                        ->rows(2)
                        ->nullable(),
                ])
                ->modalHeading('Dispose Asset')
                ->modalDescription('Mark this asset as disposed. Records the disposal date and any sale proceeds.')
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $this->record->update([
                        'status'          => 'disposed',
                        'disposal_date'   => $data['disposal_date'],
                        'disposal_amount' => $data['disposal_amount'] ?? 0,
                    ]);
                    $this->refreshFormData(['status', 'disposal_date', 'disposal_amount']);

                    Notification::make()->title('Asset marked as disposed')->success()->send();
                }),

            // ── Write Off Asset ──────────────────────────────────────────────
            Action::make('write_off')
                ->label('Write Off')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'active')
                ->form([
                    DatePicker::make('disposal_date')
                        ->label('Write-Off Date')
                        ->required()
                        ->default(now()),
                    Textarea::make('notes')
                        ->label('Reason for Write-Off')
                        ->rows(2)
                        ->nullable(),
                ])
                ->modalHeading('Write Off Asset')
                ->modalDescription('Sets status to Written Off and fully depreciates the asset.')
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $fullDepreciation = (float) $this->record->cost - (float) $this->record->residual_value;

                    $this->record->update([
                        'status'                   => 'written_off',
                        'disposal_date'            => $data['disposal_date'],
                        'accumulated_depreciation' => $fullDepreciation,
                    ]);
                    $this->refreshFormData(['status', 'disposal_date', 'accumulated_depreciation']);

                    Notification::make()->title('Asset written off')->danger()->send();
                }),

            // ── Transfer Location / Custodian ────────────────────────────────
            Action::make('transfer')
                ->label('Transfer')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('gray')
                ->visible(fn () => $this->record->status === 'active')
                ->form([
                    TextInput::make('new_location')
                        ->label('New Location / Department')
                        ->required()
                        ->maxLength(255)
                        ->default(fn () => $this->record->location),

                    Select::make('new_custodian_id')
                        ->label('New Custodian (Employee)')
                        ->relationship('custodian', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->default(fn () => $this->record->custodian_employee_id),

                    DatePicker::make('transfer_date')
                        ->label('Transfer Date')
                        ->required()
                        ->default(now()),

                    Textarea::make('notes')
                        ->label('Transfer Notes')
                        ->rows(2)
                        ->nullable(),
                ])
                ->modalHeading('Transfer Asset')
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        // Log the transfer
                        FixedAssetTransfer::create([
                            'fixed_asset_id'         => $this->record->id,
                            'from_location'          => $this->record->location,
                            'to_location'            => $data['new_location'],
                            'from_custodian_id'      => $this->record->custodian_employee_id,
                            'to_custodian_id'        => $data['new_custodian_id'] ?? null,
                            'transfer_date'          => $data['transfer_date'],
                            'transferred_by_user_id' => Auth::id(),
                            'notes'                  => $data['notes'] ?? null,
                        ]);

                        // Update the asset
                        $this->record->update([
                            'location'               => $data['new_location'],
                            'custodian_employee_id'  => $data['new_custodian_id'] ?? null,
                        ]);
                    });

                    $this->refreshFormData(['location', 'custodian_employee_id']);

                    Notification::make()
                        ->title('Asset transferred to ' . $data['new_location'])
                        ->success()
                        ->send();
                }),

            // ── Reactivate ───────────────────────────────────────────────────
            Action::make('reactivate')
                ->label('Reactivate')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['disposed', 'written_off']))
                ->requiresConfirmation()
                ->modalHeading('Reactivate Asset')
                ->modalDescription('Restores asset status to Active and clears disposal fields.')
                ->action(function () {
                    $this->record->update([
                        'status'          => 'active',
                        'disposal_date'   => null,
                        'disposal_amount' => null,
                    ]);
                    $this->refreshFormData(['status', 'disposal_date', 'disposal_amount']);

                    Notification::make()->title('Asset reactivated')->success()->send();
                }),

            DeleteAction::make()
                ->visible(fn () => $this->record->status !== 'active'),
        ];
    }
}