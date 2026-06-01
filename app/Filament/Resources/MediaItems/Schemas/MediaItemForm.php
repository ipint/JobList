<?php

namespace App\Filament\Resources\MediaItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Media')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('path')
                            ->label('Image')
                            ->disk('public')
                            ->directory('media')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->maxSize(5120)
                            ->required()
                            ->columnSpanFull(),
                        Hidden::make('disk')
                            ->default('public'),
                        TextInput::make('title')
                            ->maxLength(255),
                        TextInput::make('alt_text')
                            ->label('Alt text')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
