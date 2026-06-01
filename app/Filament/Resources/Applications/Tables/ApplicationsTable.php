<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use App\Models\JobAttribute;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('candidate_first_name')
                    ->label('First name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidate_last_name')
                    ->label('Last name')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record): string => $record->candidate_email),
                TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: count(auth()->user()?->accessibleCompanyIds() ?? []) <= 1),
                ViewColumn::make('status')
                    ->label('Status')
                    ->view('filament.tables.columns.application-status-badge')
                    ->sortable()
                    ->searchable(),
                ViewColumn::make('flag')
                    ->label('Flag')
                    ->view('filament.tables.columns.application-flag-controls'),
                TextColumn::make('source')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('cv_path')
                    ->label('CV')
                    ->formatStateUsing(fn(?string $state): string => filled($state) ? 'Preview' : '-')
                    ->url(fn($record): ?string => filled($record->cv_path) ? Storage::disk('public')->url($record->cv_path) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('cover_letter_path')
                    ->label('Cover letter')
                    ->formatStateUsing(fn(?string $state): string => filled($state) ? 'Preview' : '-')
                    ->url(fn($record): ?string => filled($record->cover_letter_path) ? Storage::disk('public')->url($record->cover_letter_path) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('applied_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn(): array => JobAttribute::optionsFor('application_status')),
                SelectFilter::make('flag')
                    ->options(Application::flagOptions()),
                SelectFilter::make('job')
                    ->relationship('job', 'title', fn($query) => $query
                        ->when(
                            ! auth()->user()?->canManageAllCompanies(),
                            fn($query) => $query->whereIn('company_id', auth()->user()?->accessibleCompanyIds() ?? []),
                        )),
                SelectFilter::make('company')
                    ->relationship('company', 'name', fn($query) => $query
                        ->when(
                            ! auth()->user()?->canManageAllCompanies(),
                            fn($query) => $query->whereIn('id', auth()->user()?->accessibleCompanyIds() ?? []),
                        ))
                    ->visible(fn(): bool => count(auth()->user()?->accessibleCompanyIds() ?? []) > 1),
            ])
            ->filtersTriggerAction(fn (Action $action): Action => $action
                ->visible(fn (): bool => auth()->user()?->canAccess('applications', 'edit') ?? false))
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Application $record): string => ApplicationResource::getUrl('view', array_merge(
                        ['record' => $record],
                        request()->query(),
                    ))),
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->canAccess('applications', 'edit') ?? false),
            ])
            ->recordUrl(fn (Application $record): string => ApplicationResource::getUrl('view', array_merge(
                ['record' => $record],
                request()->query(),
            )))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('applied_at', 'desc');
    }
}
