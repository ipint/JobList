<?php

namespace App\Filament\Resources\JobAttributes\Tables;

use App\Models\JobAttribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobAttributesTable
{
    public static function configure(Table $table, bool $supportsColor = false): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->badge()
                    ->color(fn (?string $state): string => $state ?: 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? (JobAttribute::COLORS[$state] ?? $state) : '-')
                    ->visible($supportsColor)
                    ->sortable(),
                TextColumn::make('value')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('display_order')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->defaultSort('display_order');
    }
}
