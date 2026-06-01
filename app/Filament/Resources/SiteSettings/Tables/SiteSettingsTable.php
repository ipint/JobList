<?php

namespace App\Filament\Resources\SiteSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                ImageColumn::make('header_logo_path')
                    ->label('Header logo')
                    ->disk('public')
                    ->height(40),
                ImageColumn::make('footer_logo_path')
                    ->label('Footer logo')
                    ->disk('public')
                    ->height(40),
                ImageColumn::make('favicon_path')
                    ->label('Favicon')
                    ->disk('public')
                    ->height(32),
                TextColumn::make('copyright_text')
                    ->limit(64),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
