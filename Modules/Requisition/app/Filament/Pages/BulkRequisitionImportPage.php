<?php

namespace Modules\Requisition\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionItem;

class BulkRequisitionImportPage extends Page
{
    protected string $view = 'requisition::filament.pages.bulk-import';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Bulk Import';

    public ?string $uploadedFile = null;

    public array $importResults = [];

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Upload CSV File')->schema([
                Placeholder::make('csv_format')
                    ->label('CSV Format')
                    ->content('Required columns: title, request_type, urgency, description, cost_centre_code, item_description, item_quantity, item_unit, item_unit_cost')
                    ->columnSpanFull(),
                FileUpload::make('uploadedFile')
                    ->label('Select CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->action('importCsv'),
        ];
    }

    public function importCsv(): void
    {
        $this->validate([
            'uploadedFile' => 'required',
        ]);

        $tenant = filament()->getTenant();
        if (! $tenant) {
            Notification::make()->danger()->title('No tenant context.')->send();
            return;
        }

        $path = storage_path('app/public/' . $this->uploadedFile);

        if (! file_exists($path)) {
            Notification::make()->danger()->title('Uploaded file not found.')->send();
            return;
        }

        $handle  = fopen($path, 'r');
        $headers = fgetcsv($handle);

        if (! $headers) {
            Notification::make()->danger()->title('CSV file is empty or malformed.')->send();
            fclose($handle);
            return;
        }

        // Normalise headers
        $headers       = array_map('trim', $headers);
        $processed     = 0;
        $failed        = 0;
        $errors        = [];
        $rowIndex      = 1;
        $requisitions  = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;

            if (count($row) !== count($headers)) {
                $errors[]  = "Row {$rowIndex}: column count mismatch.";
                $failed++;
                continue;
            }

            $data = array_combine($headers, $row);

            // Validate required columns
            if (empty($data['title']) || empty($data['request_type'])) {
                $errors[]  = "Row {$rowIndex}: 'title' and 'request_type' are required.";
                $failed++;
                continue;
            }

            $requestType = $data['request_type'];
            if (! array_key_exists($requestType, Requisition::TYPES)) {
                $errors[] = "Row {$rowIndex}: invalid request_type '{$requestType}'.";
                $failed++;
                continue;
            }

            $urgency = $data['urgency'] ?? 'medium';
            if (! array_key_exists($urgency, Requisition::URGENCIES)) {
                $urgency = 'medium';
            }

            // Find or create cost centre by code
            $costCentreId = null;
            if (! empty($data['cost_centre_code'])) {
                $cc = \Modules\Finance\Models\CostCentre::where('company_id', $tenant->getKey())
                    ->where('code', $data['cost_centre_code'])
                    ->first();
                $costCentreId = $cc?->id;
            }

            try {
                // Group by title to avoid duplicate requisitions per CSV row with same title
                $key = $data['title'] . '|' . $requestType;
                if (! isset($requisitions[$key])) {
                    $req = Requisition::create([
                        'company_id'    => $tenant->getKey(),
                        'title'         => $data['title'],
                        'request_type'  => $requestType,
                        'urgency'       => $urgency,
                        'description'   => $data['description'] ?? null,
                        'cost_centre_id' => $costCentreId,
                        'status'        => 'draft',
                    ]);
                    $requisitions[$key] = $req;
                    $processed++;
                }

                $req = $requisitions[$key];

                // Add item if provided
                if (! empty($data['item_description'])) {
                    RequisitionItem::create([
                        'requisition_id' => $req->id,
                        'description'    => $data['item_description'],
                        'quantity'       => is_numeric($data['item_quantity'] ?? '') ? (float) $data['item_quantity'] : 1,
                        'unit'           => $data['item_unit'] ?? 'pcs',
                        'unit_cost'      => is_numeric($data['item_unit_cost'] ?? '') ? (float) $data['item_unit_cost'] : null,
                    ]);
                }
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowIndex}: {$e->getMessage()}";
                $failed++;
                Log::warning('BulkRequisitionImport row failed', ['row' => $rowIndex, 'error' => $e->getMessage()]);
            }
        }

        fclose($handle);

        $this->importResults = [
            'processed' => $processed,
            'failed'    => $failed,
            'errors'    => $errors,
        ];

        if ($processed > 0) {
            Notification::make()
                ->success()
                ->title("Import complete: {$processed} requisition(s) created" . ($failed > 0 ? ", {$failed} row(s) failed." : '.'))
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title("Import failed. {$failed} row(s) had errors.")
                ->send();
        }
    }
}