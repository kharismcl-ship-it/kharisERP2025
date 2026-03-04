<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\AnnouncementResource\Pages;
use Modules\HR\Models\Announcement;

class AnnouncementResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';
    protected static ?string $model = Announcement::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;


    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'Announcements';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Announcement Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload(),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255)->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('priority')
                            ->options(Announcement::PRIORITIES)
                            ->required()->native(false),
                    ]),

                Section::make('Audience & Scheduling')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('target_audience')
                            ->options(Announcement::AUDIENCES)
                            ->required()->native(false)->live(),
                        Forms\Components\Select::make('target_department_id')
                            ->relationship('targetDepartment', 'name')
                            ->searchable()->preload()->nullable()
                            ->visible(fn ($get) => $get('target_audience') === 'department'),
                        Forms\Components\Select::make('target_job_position_id')
                            ->relationship('targetJobPosition', 'title')
                            ->searchable()->preload()->nullable()
                            ->visible(fn ($get) => $get('target_audience') === 'job_position'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')->default(false)->inline(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date')->native(false)->nullable(),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiry Date')->native(false)->nullable(),
                        Forms\Components\Toggle::make('send_email')
                            ->label('Send Email Notification')->default(false)->inline(false),
                        Forms\Components\Toggle::make('send_sms')
                            ->label('Send SMS Notification')->default(false)->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->weight('bold')->limit(50)
                    ->description(fn (Announcement $r) => $r->company->name ?? ''),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'gray',
                        'normal' => 'info',
                        'high'   => 'warning',
                        'urgent' => 'danger',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('target_audience')
                    ->label('Audience')
                    ->formatStateUsing(fn ($state) => Announcement::AUDIENCES[$state] ?? ucfirst($state)),
                Tables\Columns\IconColumn::make('is_published')->label('Published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Published On')->dateTime()->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('expires_at')->label('Expires')->date()->placeholder('Never')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('priority')->options(Announcement::PRIORITIES),
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Announcement $r) => ! $r->is_published)
                        ->action(function (Announcement $record) {
                            $record->update(['is_published' => true, 'published_at' => now()]);
                            Notification::make()->title('Announcement published')->success()->send();
                        }),
                    Action::make('unpublish')
                        ->label('Unpublish')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Announcement $r) => $r->is_published)
                        ->action(function (Announcement $record) {
                            $record->update(['is_published' => false]);
                            Notification::make()->title('Announcement unpublished')->warning()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view'   => Pages\ViewAnnouncement::route('/{record}'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}