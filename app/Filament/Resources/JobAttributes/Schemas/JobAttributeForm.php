<?php

namespace App\Filament\Resources\JobAttributes\Schemas;

use App\Models\JobAttribute;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JobAttributeForm
{
    public static function configure(Schema $schema, bool $supportsColor = true): Schema
    {
        return $schema
            ->components([
                Section::make('Attribute')
                    ->columns(1)
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $set('value', Str::slug($state, '_'));
                            }),
                        TextInput::make('value')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in URLs and stored on records. Keep existing values stable once records use them.'),
                        Select::make('color')
                            ->options(JobAttribute::COLORS)
                            ->default('gray')
                            ->visible($supportsColor)
                            ->required($supportsColor)
                            ->dehydrated($supportsColor),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }
}
