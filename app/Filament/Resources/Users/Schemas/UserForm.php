<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->visible(false)
                            ->dehydrated(false),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        Toggle::make('is_super_admin')
                            ->label('Super admin')
                            ->default(false)
                            ->helperText('Super admins can access every section and every company.'),
                        Select::make('role_id')
                            ->label('Role')
                            ->relationship('role', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get): bool => ! (bool) $get('is_super_admin'))
                            ->required(fn ($get): bool => ! (bool) $get('is_super_admin')),
                        Select::make('companies')
                            ->label('Companies')
                            ->relationship('companies', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Non-super admins only see records for these companies.'),
                    ]),
            ]);
    }
}
