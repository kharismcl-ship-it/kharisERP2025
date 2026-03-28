<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Clusters\HrSetupCluster;
use Modules\HR\Filament\Resources\OnboardingTaskResource\Pages;
use Modules\HR\Models\OnboardingTask;

class OnboardingTaskResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = OnboardingTask::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?int $navigationSort = 88;
    protected static ?string $navigationLabel = 'Onboarding Templates';

    // Show only templates in this resource; per-employee tasks are managed via employee record.
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('is_template', true)->whereNull('employee_id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Onboarding Task Template')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('assignee_type')
                    ->label('Responsibility')
                    ->options([
                        'employee' => 'New Employee',
                        'hr'       => 'HR Team',
                        'manager'  => 'Line Manager',
                        'it'       => 'IT Team',
                        'finance'  => 'Finance',
                    ])
                    ->required()->default('employee'),
                Forms\Components\TextInput::make('due_days_from_hire')
                    ->label('Due (days after hire)')
                    ->numeric()->required()->default(1)->minValue(1),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Hidden::make('is_template')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('assignee_type')->label('Owner')
                    ->formatStateUsing(fn ($state) => str($state)->headline())
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('due_days_from_hire')->label('Due Day')->suffix(' days')->sortable(),
            ])
            ->reorderable('sort_order')
            ->actions([EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOnboardingTasks::route('/'),
            'create' => Pages\CreateOnboardingTask::route('/create'),
            'edit'   => Pages\EditOnboardingTask::route('/{record}/edit'),
        ];
    }
}