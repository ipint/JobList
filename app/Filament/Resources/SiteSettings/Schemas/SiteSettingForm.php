<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Branding')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('header_logo_path')
                            ->label('Header logo')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->helperText('Used as the admin header/sidebar logo. Leave empty to show the app name.'),
                        FileUpload::make('footer_logo_path')
                            ->label('Footer logo')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable(),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable(),
                        TextInput::make('copyright_text')
                            ->maxLength(255)
                            ->placeholder('(c) 2026 JobList. All rights reserved.'),
                    ]),
            ]);
    }
}
