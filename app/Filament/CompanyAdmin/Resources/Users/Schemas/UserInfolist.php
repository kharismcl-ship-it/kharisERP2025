<?php

namespace App\Filament\CompanyAdmin\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->url(fn ($state) => "mailto:{$state}"),

                        IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->state(fn ($record): bool => (bool) $record->email_verified_at),

                        TextEntry::make('email_verified_at')
                            ->label('Verified At')
                            ->dateTime()
                            ->placeholder('Not verified'),
                    ]),

                Section::make('Company Roles')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label('Assigned Roles')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                            ->placeholder('No roles assigned'),
                    ]),

                Section::make('System')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime()
                            ->since(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->since(),
                    ]),
            ]);
    }
}