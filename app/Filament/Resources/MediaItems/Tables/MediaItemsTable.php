<?php

namespace App\Filament\Resources\MediaItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(48),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alt_text')
                    ->label('Alt text')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('mime_type')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state / 1024, 1) . ' KB' : '-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
