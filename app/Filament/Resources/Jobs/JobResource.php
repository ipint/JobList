<?php

namespace App\Filament\Resources\Jobs;

use App\Filament\Resources\Jobs\Pages\CreateJob;
use App\Filament\Resources\Jobs\Pages\EditJob;
use App\Filament\Resources\Jobs\Pages\ListJobs;
use App\Filament\Resources\Jobs\Schemas\JobForm;
use App\Filament\Resources\Jobs\Tables\JobsTable;
use App\Models\Job;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Jobs';

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return JobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('jobs', 'view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canAccess('jobs', 'create') ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->canAccess('jobs', 'edit') ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->canAccess('jobs', 'delete') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        $query->withCount([
            'applications',
            'applications as applications_viewed_count' => fn (Builder $query): Builder => $query->where('status', '!=', 'new'),
        ]);

        if ($user?->canManageAllCompanies()) {
            return $query;
        }

        return $query->whereIn('company_id', $user?->accessibleCompanyIds() ?? []);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobs::route('/'),
            'create' => CreateJob::route('/create'),
            'edit' => EditJob::route('/{record}/edit'),
        ];
    }
}
