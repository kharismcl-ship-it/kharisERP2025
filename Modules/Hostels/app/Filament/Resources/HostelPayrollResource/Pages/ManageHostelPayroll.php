<?php

namespace Modules\Hostels\Filament\Resources\HostelPayrollResource\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\HostelPayrollResource;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Services\PayrollSyncService;

class ManageHostelPayroll extends Page
{
    protected static string $resource = HostelPayrollResource::class;

    protected string $view = 'hostels::filament.resources.hostel-payroll-resource.pages.manage-hostel-payroll';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->subMonth()->startOfMonth(),
            'end_date' => now()->subMonth()->endOfMonth(),
            'sync_attendance' => true,
            'calculate_payroll' => true,
            'export_to_hr' => false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Payroll Period')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required(),

                        Select::make('hostel_id')
                            ->label('Specific Hostel')
                            ->options(Hostel::all()->pluck('name', 'id'))
                            ->nullable()
                            ->searchable(),
                    ])->columns(3),

                Section::make('Sync Options')
                    ->schema([
                        Toggle::make('sync_attendance')
                            ->label('Sync Attendance to HR')
                            ->default(true)
                            ->helperText('Sync approved attendance records to HR system'),

                        Toggle::make('calculate_payroll')
                            ->label('Calculate Payroll')
                            ->default(true)
                            ->helperText('Calculate payroll based on attendance and role rates'),

                        Toggle::make('export_to_hr')
                            ->label('Export to HR System')
                            ->default(false)
                            ->helperText('Export calculated payroll data to HR employee salaries'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('process_payroll')
                ->label('Process Payroll')
                ->submit('processPayroll')
                ->color('primary'),
        ];
    }

    public function processPayroll(PayrollSyncService $payrollService): void
    {
        $data = $this->form->getState();

        $startDate = $data['start_date'];
        $endDate = $data['end_date'];
        $hostelId = $data['hostel_id'] ?? null;

        try {
            // Sync attendance if requested
            if ($data['sync_attendance']) {
                $syncResults = $payrollService->syncAttendanceToPayroll($startDate, $endDate, $hostelId);

                Notification::make()
                    ->title('Attendance Sync Completed')
                    ->body("Synced: {$syncResults['synced']} records, Skipped: {$syncResults['skipped']} records")
                    ->success()
                    ->send();
            }

            // Calculate payroll if requested
            if ($data['calculate_payroll']) {
                $payrollData = $payrollService->calculateHostelStaffPayroll($startDate, $endDate, $hostelId);

                // Export to HR if requested
                if ($data['export_to_hr'] && ! empty($payrollData)) {
                    $exportSuccess = $payrollService->exportPayrollToHR($payrollData);

                    if ($exportSuccess) {
                        Notification::make()
                            ->title('Payroll Export Successful')
                            ->body('Payroll data has been exported to HR system')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Payroll Export Failed')
                            ->body('Failed to export payroll data to HR system')
                            ->danger()
                            ->send();
                    }
                }

                Notification::make()
                    ->title('Payroll Calculation Completed')
                    ->body('Calculated payroll for '.count($payrollData).' employees')
                    ->success()
                    ->send();
            }

        } catch (Exception $e) {
            Notification::make()
                ->title('Payroll Processing Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
