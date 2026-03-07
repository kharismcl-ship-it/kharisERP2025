<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorBadgeResource\Pages;

use App\Models\Company;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Modules\ClientService\Filament\Resources\CsVisitorBadgeResource;
use Modules\ClientService\Models\CsVisitorBadge;

class ListCsVisitorBadges extends ListRecords
{
    protected static string $resource = CsVisitorBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_badges')
                ->label('Generate Badges')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->modalHeading('Generate Visitor Badge Codes')
                ->modalWidth('lg')
                ->form([
                    Select::make('company_id')
                        ->label('Company')
                        ->options(Company::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->helperText('Badges will be linked to this company.'),

                    TextInput::make('prefix')
                        ->label('Badge Prefix')
                        ->required()
                        ->maxLength(6)
                        ->default('VB')
                        ->helperText('Short code prefixed to each badge (e.g. "KH" → KH-0001). Max 6 chars.')
                        ->rules(['alpha', 'max:6']),

                    TextInput::make('batch')
                        ->label('Batch Identifier')
                        ->placeholder('Auto-generated if left empty')
                        ->maxLength(50),

                    TextInput::make('quantity')
                        ->label('Number of Badges')
                        ->numeric()
                        ->required()
                        ->default(50)
                        ->minValue(1)
                        ->maxValue(500)
                        ->helperText('How many badge codes to generate in this batch.'),
                ])
                ->action(function (array $data): void {
                    try {
                        $prefix   = strtoupper(trim($data['prefix']));
                        $batchId  = filled($data['batch'])
                            ? $data['batch']
                            : CsVisitorBadge::nextBatchId($prefix);
                        $quantity = (int) $data['quantity'];

                        $codes = CsVisitorBadge::generateBatch(
                            batchNumber: $batchId,
                            quantity:    $quantity,
                            prefix:      $prefix,
                            companyId:   (int) $data['company_id'],
                        );

                        Notification::make()
                            ->success()
                            ->title('Badges Generated')
                            ->body("Created {$quantity} badge codes (prefix: {$prefix}) in batch {$batchId}.")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Generation Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Action::make('statistics')
                ->label('Statistics')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading('Badge Inventory')
                ->modalWidth('md')
                ->modalContent(function () {
                    $total     = CsVisitorBadge::count();
                    $available = CsVisitorBadge::where('status', 'available')->count();
                    $issued    = CsVisitorBadge::where('status', 'issued')->count();
                    $void      = CsVisitorBadge::where('status', 'void')->count();

                    $pctAvail  = $total ? round($available / $total * 100) : 0;
                    $pctIssued = $total ? round($issued    / $total * 100) : 0;
                    $pctVoid   = $total ? round($void      / $total * 100) : 0;

                    return view('clientservice::filament.badge-stats', compact(
                        'total', 'available', 'issued', 'void',
                        'pctAvail', 'pctIssued', 'pctVoid'
                    ));
                })
                ->slideOver(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make('All'),
            'available' => Tab::make('Available')
                ->badge(fn () => CsVisitorBadge::where('status', 'available')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'available')),
            'issued'    => Tab::make('Issued')
                ->badge(fn () => CsVisitorBadge::where('status', 'issued')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'issued')),
            'void'      => Tab::make('Void')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'void')),
        ];
    }
}
