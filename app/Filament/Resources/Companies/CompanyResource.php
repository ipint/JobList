<?php

namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Companies';

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('companies', 'view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canAccess('companies', 'create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return ($user?->canAccess('companies', 'edit') ?? false) && ($user?->hasCompanyAccess($record->id) ?? false);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return ($user?->canAccess('companies', 'delete') ?? false) && ($user?->hasCompanyAccess($record->id) ?? false);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->canManageAllCompanies()) {
            return $query;
        }

        return $query->whereIn('id', $user?->accessibleCompanyIds() ?? []);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
