<?php

namespace App\Filament\Resources\XmlFeeds\Schemas;

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\ExperienceLevel;
use App\Models\WorkMode;
use App\Models\XmlFeed;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class XmlFeedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Feed')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->default(true),
                        Placeholder::make('public_url')
                            ->label('Public XML URL')
                            ->content(fn ($record): string => $record ? route('public.xml-feeds.show', $record) : 'Save this feed to generate a public URL.'),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Company Scope')
                    ->schema([
                        Select::make('companies')
                            ->label('Companies')
                            ->relationship('companies', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                Section::make('Job Attribute Filters')
                    ->description('Leave blank to include all values.')
                    ->columns(2)
                    ->schema([
                        Select::make('departments')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Department::query()->where('is_active', true)->orderBy('label')->pluck('label', 'value')->all()),
                        Select::make('employment_types')
                            ->label('Employment Type')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => EmploymentType::query()->where('is_active', true)->orderBy('label')->pluck('label', 'value')->all()),
                        Select::make('work_modes')
                            ->label('Work Mode')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => WorkMode::query()->where('is_active', true)->orderBy('label')->pluck('label', 'value')->all()),
                        Select::make('experience_levels')
                            ->label('Experience Level')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => ExperienceLevel::query()->where('is_active', true)->orderBy('label')->pluck('label', 'value')->all()),
                    ]),
                Section::make('XML Output Fields')
                    ->description('Choose which tags are included in each <job> item.')
                    ->schema([
                        Select::make('selected_fields')
                            ->label('Fields')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(XmlFeed::defaultXmlFields())
                            ->options(XmlFeed::xmlFieldOptions()),
                    ]),
            ]);
    }
}
