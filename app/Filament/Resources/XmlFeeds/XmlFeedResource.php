<?php

namespace App\Filament\Resources\XmlFeeds;

use App\Filament\Resources\XmlFeeds\Pages\CreateXmlFeed;
use App\Filament\Resources\XmlFeeds\Pages\EditXmlFeed;
use App\Filament\Resources\XmlFeeds\Pages\ListXmlFeeds;
use App\Filament\Resources\XmlFeeds\Schemas\XmlFeedForm;
use App\Filament\Resources\XmlFeeds\Tables\XmlFeedsTable;
use App\Models\XmlFeed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class XmlFeedResource extends Resource
{
    protected static ?string $model = XmlFeed::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'XML Feeds';

    protected static ?string $slug = 'xml-feeds';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 12;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracket;

    public static function form(Schema $schema): Schema
    {
        return XmlFeedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return XmlFeedsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('xml_feeds', 'view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canAccess('xml_feeds', 'create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canAccess('xml_feeds', 'edit') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->canAccess('xml_feeds', 'delete') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListXmlFeeds::route('/'),
            'create' => CreateXmlFeed::route('/create'),
            'edit' => EditXmlFeed::route('/{record}/edit'),
        ];
    }
}
