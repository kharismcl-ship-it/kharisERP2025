<?php

namespace Modules\HR\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Employee;

class ClockInOutPage extends Page
{
    protected string $view = 'hr::filament.pages.clock-in-out';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Clock In / Out';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 3;

    public ?Employee $employee = null;
    public ?AttendanceRecord $todayRecord = null;
    public string $today = '';

    public function mount(): void
    {
        $companyId     = Filament::getTenant()?->id;
        $this->today   = now()->toDateString();
        $this->employee = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if ($this->employee) {
            $this->todayRecord = AttendanceRecord::where('employee_id', $this->employee->id)
                ->where('date', $this->today)
                ->first();
        }
    }

    public function clockIn(): void
    {
        if (! $this->employee) {
            Notification::make()->title('No employee record found')->danger()->send();
            return;
        }

        if ($this->todayRecord && $this->todayRecord->check_in_time) {
            Notification::make()->title('Already clocked in today')->warning()->send();
            return;
        }

        $this->todayRecord = AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $this->employee->id,
                'company_id'  => $this->employee->company_id,
                'date'        => $this->today,
            ],
            [
                'check_in_time' => now(),
                'status'        => 'present',
            ]
        );

        Notification::make()
            ->title('Clocked in at ' . now()->format('g:i A'))
            ->success()
            ->send();
    }

    public function clockOut(): void
    {
        if (! $this->employee || ! $this->todayRecord) {
            Notification::make()->title('No clock-in record found for today')->warning()->send();
            return;
        }

        if ($this->todayRecord->check_out_time) {
            Notification::make()->title('Already clocked out today')->warning()->send();
            return;
        }

        $this->todayRecord->update(['check_out_time' => now()]);
        $this->todayRecord->refresh();

        Notification::make()
            ->title('Clocked out at ' . now()->format('g:i A'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clockIn')
                ->label('Clock In')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->visible(fn () => ! $this->todayRecord?->check_in_time)
                ->action('clockIn'),

            Action::make('clockOut')
                ->label('Clock Out')
                ->icon('heroicon-o-arrow-left-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->todayRecord?->check_in_time && ! $this->todayRecord?->check_out_time)
                ->action('clockOut'),
        ];
    }
}
