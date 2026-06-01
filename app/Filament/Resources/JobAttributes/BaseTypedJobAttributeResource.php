<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Resources\JobAttributes\Schemas\JobAttributeForm;
use App\Filament\Resources\JobAttributes\Tables\JobAttributesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

abstract class BaseTypedJobAttributeResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'label';

    protected static string | UnitEnum | null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 1;

    protected static string | BackedEnum | null $navigationIcon = null;

    protected static bool $supportsColor = false;

    protected static string $permissionSection = 'job_attributes';

    public static function form(Schema $schema): Schema
    {
        return JobAttributeForm::configure($schema, static::$supportsColor);
    }

    public static function table(Table $table): Table
    {
        return JobAttributesTable::configure($table, static::$supportsColor);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess(static::$permissionSection, 'view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canAccess(static::$permissionSection, 'create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canAccess(static::$permissionSection, 'edit') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->canAccess(static::$permissionSection, 'delete') ?? false;
    }
}
