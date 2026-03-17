<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Schemas;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave Request')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('leave_type_id')
                        ->label('Leave Type')
                        ->options(function () {
                            $companyId = Filament::getTenant()?->id;
                            return LeaveType::where('company_id', $companyId)
                                ->where('is_active', true)
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->native(false)
                        ->live()
                        ->helperText(fn (Get $get) => self::balanceHint($get('leave_type_id'))),

                    Placeholder::make('days_preview')
                        ->label('Days Requested')
                        ->content(function (Get $get): string {
                            $start = $get('start_date');
                            $end   = $get('end_date');
                            if ($start && $end && $end >= $start) {
                                $days = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
                                return $days . ' day' . ($days !== 1 ? 's' : '');
                            }
                            return '—';
                        }),

                    Forms\Components\DatePicker::make('start_date')
                        ->required()
                        ->native(false)
                        ->live(),

                    Forms\Components\DatePicker::make('end_date')
                        ->required()
                        ->native(false)
                        ->live()
                        ->after('start_date'),

                    Forms\Components\Textarea::make('reason')
                        ->label('Reason')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function balanceHint(?int $leaveTypeId): ?string
    {
        if (! $leaveTypeId) {
            return null;
        }

        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return null;
        }

        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', now()->year)
            ->first();

        if (! $balance) {
            return 'No balance on record for this leave type this year.';
        }

        return "Available: {$balance->current_balance} days  |  Used: {$balance->used_balance}  /  Allocated: {$balance->initial_balance}";
    }
}
