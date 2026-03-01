<?php

namespace Modules\HR\Filament\Resources\AnnouncementResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\AnnouncementResource;
use Modules\HR\Models\Announcement;

class ViewAnnouncement extends ViewRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->is_published)
                ->action(function () {
                    $this->record->update(['is_published' => true, 'published_at' => now()]);
                    $this->refreshFormData(['is_published', 'published_at']);
                    Notification::make()->title('Announcement published')->success()->send();
                }),
            Action::make('unpublish')
                ->label('Unpublish')
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->is_published)
                ->action(function () {
                    $this->record->update(['is_published' => false]);
                    $this->refreshFormData(['is_published']);
                    Notification::make()->title('Announcement unpublished')->warning()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Announcement Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')->columnSpanFull()->weight('bold')->size('lg'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'general'   => 'gray',
                                'policy'    => 'primary',
                                'event'     => 'info',
                                'emergency' => 'danger',
                                'holiday'   => 'success',
                                default     => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('priority')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'low'    => 'gray',
                                'normal' => 'info',
                                'high'   => 'warning',
                                'urgent' => 'danger',
                                default  => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('target_audience')
                            ->label('Audience')
                            ->formatStateUsing(fn ($state) => Announcement::AUDIENCES[$state] ?? ucfirst($state)),
                        TextEntry::make('company.name')->label('Company'),
                        IconEntry::make('is_published')->label('Published')->boolean(),
                        IconEntry::make('send_email')->label('Email Notification')->boolean(),
                        IconEntry::make('send_sms')->label('SMS Notification')->boolean(),
                    ]),

                Section::make('Content')
                    ->schema([
                        TextEntry::make('content')->html()->columnSpanFull(),
                    ]),

                Section::make('Schedule')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('published_at')->label('Published On')->dateTime()->placeholder('—'),
                        TextEntry::make('expires_at')->label('Expires On')->dateTime()->placeholder('Never'),
                    ]),
            ]);
    }
}