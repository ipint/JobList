<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Last name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Legacy company')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('companies.name')
                    ->label('Companies')
                    ->badge()
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_super_admin')
                    ->label('Super admin')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('companies')
                    ->relationship('companies', 'name'),
                SelectFilter::make('role')
                    ->relationship('role', 'name'),
                TernaryFilter::make('is_super_admin')
                    ->label('Super admins'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('first_name');
    }
}
