<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Forms\Components\LeaveAttachmentForm;
use Modules\HR\Filament\Resources\LeaveRequestResource\Pages;
use Modules\HR\Models\LeaveRequest;

class LeaveRequestResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';
    protected static ?string $model = LeaveRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (\Modules\HR\Models\Employee $record) => $record->first_name.' '.$record->last_name)
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('leave_type_id')
                            ->relationship('leaveType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'            => 'Draft',
                                'pending'          => 'Pending',
                                'pending_approval' => 'In Review',
                                'approved'         => 'Approved',
                                'rejected'         => 'Rejected',
                                'cancelled'        => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft')
                            ->native(false),
                        Forms\Components\Textarea::make('reason')
                            ->columnSpanFull(),
                    ]),

                Section::make('Dates & Duration')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');

                                if ($startDate && $endDate) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    $totalDays = $start->diffInDays($end) + 1;
                                    $set('total_days', $totalDays);
                                }
                            }),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');

                                if ($startDate && $endDate) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    $totalDays = $start->diffInDays($end) + 1;
                                    $set('total_days', $totalDays);
                                }
                            }),
                        Forms\Components\TextInput::make('total_days')
                            ->default(0)
                            ->readOnly()
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $leaveTypeId = $get('leave_type_id');
                                        $totalDays = (float) $value;

                                        if ($leaveTypeId && $totalDays > 0) {
                                            $leaveType = \Modules\HR\Models\LeaveType::find($leaveTypeId);

                                            if ($leaveType && $totalDays > $leaveType->max_days_per_year) {
                                                $fail("The requested {$totalDays} days exceed the maximum of {$leaveType->max_days_per_year} days allowed for {$leaveType->name}.");
                                            }
                                        }
                                    };
                                },
                            ]),
                    ]),

                Section::make('Supporting Documents')
                    ->schema([
                        ...LeaveAttachmentForm::make(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_in_days')
                    ->label('Days')
                    ->getStateUsing(fn ($record) => $record->start_date->diffInDays($record->end_date) + 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved'         => 'success',
                        'rejected'         => 'danger',
                        'pending'          => 'warning',
                        'pending_approval' => 'info',
                        'cancelled'        => 'gray',
                        'draft'            => 'gray',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending_approval' => 'In Review',
                        default            => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'full_name'),
                Tables\Filters\SelectFilter::make('leave_type')
                    ->relationship('leaveType', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'            => 'Draft',
                        'pending'          => 'Pending',
                        'pending_approval' => 'In Review',
                        'approved'         => 'Approved',
                        'rejected'         => 'Rejected',
                        'cancelled'        => 'Cancelled',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (LeaveRequest $r) => in_array($r->status, ['pending', 'pending_approval']))
                        ->action(function (LeaveRequest $record) {
                            $companyId = \Filament\Facades\Filament::getTenant()?->id;
                            $approver  = \Modules\HR\Models\Employee::where('user_id', auth()->id())
                                ->where('company_id', $companyId)
                                ->first();
                            $record->update([
                                'status'                  => 'approved',
                                'approved_by_employee_id' => $approver?->id,
                                'approved_at'             => now(),
                            ]);
                            Notification::make()->title('Leave approved')->success()->send();
                        }),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejected_reason')
                                ->label('Reason for Rejection')
                                ->required()
                                ->rows(3),
                        ])
                        ->visible(fn (LeaveRequest $r) => in_array($r->status, ['pending', 'pending_approval']))
                        ->action(function (LeaveRequest $record, array $data) {
                            $record->update([
                                'status'          => 'rejected',
                                'rejected_reason' => $data['rejected_reason'],
                            ]);
                            Notification::make()->title('Leave rejected')->warning()->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'view' => Pages\ViewLeaveRequest::route('/{record}'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
