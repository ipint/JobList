<?php

namespace App\Filament\Resources\MediaItems;

use App\Filament\Resources\MediaItems\Pages\CreateMediaItem;
use App\Filament\Resources\MediaItems\Pages\EditMediaItem;
use App\Filament\Resources\MediaItems\Pages\ListMediaItems;
use App\Filament\Resources\MediaItems\Schemas\MediaItemForm;
use App\Filament\Resources\MediaItems\Tables\MediaItemsTable;
use App\Models\MediaItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MediaItemResource extends Resource
{
    protected static ?string $model = MediaItem::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Media Manager';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 15;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return MediaItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaItemsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('media', 'view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canAccess('media', 'create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canAccess('media', 'edit') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->canAccess('media', 'delete') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaItems::route('/'),
            'create' => CreateMediaItem::route('/create'),
            'edit' => EditMediaItem::route('/{record}/edit'),
        ];
    }
}
