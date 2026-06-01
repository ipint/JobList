<?php

namespace App\Filament\Resources\Jobs\Tables;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\JobAttribute;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->company?->name ?? $record->company_name),
                TextColumn::make('county.name')
                    ->label('County')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('department')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state): ?string => JobAttribute::labelFor('department', $state)),
                TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('employment_type')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state): ?string => JobAttribute::labelFor('employment_type', $state)),
                TextColumn::make('work_mode')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state): ?string => JobAttribute::labelFor('work_mode', $state)),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str($state)->title()->toString()),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                TextColumn::make('applications_viewed_count')
                    ->label('Viewed/Applied')
                    ->state(fn ($record): string => "{$record->applications_viewed_count}/{$record->applications_count}")
                    ->url(fn ($record): string => ApplicationResource::getUrl('index', [
                        'tableFilters' => [
                            'job' => [
                                'value' => $record->id,
                            ],
                        ],
                    ]))
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('applications_count', $direction)),
                TextColumn::make('published_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'expired' => 'Expired',
                        'archived' => 'Archived',
                    ]),
                SelectFilter::make('department')
                    ->options(fn (): array => JobAttribute::optionsFor('department')),
                SelectFilter::make('employment_type')
                    ->options(fn (): array => JobAttribute::optionsFor('employment_type')),
                SelectFilter::make('work_mode')
                    ->options(fn (): array => JobAttribute::optionsFor('work_mode')),
                SelectFilter::make('experience_level')
                    ->options(fn (): array => JobAttribute::optionsFor('experience_level')),
                SelectFilter::make('county')
                    ->relationship('county', 'name'),
                SelectFilter::make('company')
                    ->relationship('company', 'name', fn ($query) => $query
                        ->when(
                            ! auth()->user()?->canManageAllCompanies(),
                            fn ($query) => $query->whereIn('id', auth()->user()?->accessibleCompanyIds() ?? []),
                        ))
                    ->visible(fn (): bool => count(auth()->user()?->accessibleCompanyIds() ?? []) > 1),
                TernaryFilter::make('is_featured')
                    ->label('Featured jobs'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('updateEmploymentType')
                        ->label('Set employment type')
                        ->visible(fn (): bool => auth()->user()?->canAccess('jobs', 'edit') ?? false)
                        ->form([
                            Select::make('employment_type')
                                ->label('Employment type')
                                ->options(fn (): array => JobAttribute::optionsFor('employment_type'))
                                ->required(),
                        ])
                        ->action(fn ($records, array $data): mixed => $records->each->update([
                            'employment_type' => $data['employment_type'],
                        ])),
                    BulkAction::make('updateWorkMode')
                        ->label('Set work mode')
                        ->visible(fn (): bool => auth()->user()?->canAccess('jobs', 'edit') ?? false)
                        ->form([
                            Select::make('work_mode')
                                ->label('Work mode')
                                ->options(fn (): array => JobAttribute::optionsFor('work_mode'))
                                ->required(),
                        ])
                        ->action(fn ($records, array $data): mixed => $records->each->update([
                            'work_mode' => $data['work_mode'],
                        ])),
                    BulkAction::make('updateStatus')
                        ->label('Set status')
                        ->visible(fn (): bool => auth()->user()?->canAccess('jobs', 'edit') ?? false)
                        ->form([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'expired' => 'Expired',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                        ])
                        ->action(fn ($records, array $data): mixed => $records->each->update([
                            'status' => $data['status'],
                        ])),
                    BulkAction::make('updateFeatured')
                        ->label('Set featured')
                        ->visible(fn (): bool => auth()->user()?->canAccess('jobs', 'edit') ?? false)
                        ->form([
                            Toggle::make('is_featured')
                                ->label('Featured')
                                ->required(),
                        ])
                        ->action(fn ($records, array $data): mixed => $records->each->update([
                            'is_featured' => (bool) $data['is_featured'],
                        ])),
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->canAccess('jobs', 'delete') ?? false),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
