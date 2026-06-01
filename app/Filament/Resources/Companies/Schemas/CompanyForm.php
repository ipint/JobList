<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        RichEditor::make('overview')
                            ->label('Overview / About Company')
                            ->columnSpanFull(),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('companies/logos')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
                Section::make('Job fields')
                    ->columns(2)
                    ->visible(fn (): bool => auth()->user()?->canAccess('job_fields', 'view') ?? false)
                    ->disabled(fn (): bool => ! (auth()->user()?->canAccess('job_fields', 'edit') ?? false))
                    ->schema([
                        Toggle::make('job_field_settings.department')
                            ->label('Department')
                            ->default(true),
                        Toggle::make('job_field_settings.experience_level')
                            ->label('Experience level')
                            ->default(true),
                        Toggle::make('job_field_settings.postcode')
                            ->label('Postcode')
                            ->default(true),
                        Toggle::make('job_field_settings.location_name')
                            ->label('Location display name')
                            ->default(true),
                        Toggle::make('job_field_settings.salary')
                            ->label('Salary fields')
                            ->default(true),
                        Toggle::make('job_field_settings.application_url')
                            ->label('Application URL')
                            ->default(true),
                        Toggle::make('job_field_settings.application_email')
                            ->label('Application email')
                            ->default(true),
                        Toggle::make('job_field_settings.closing_date')
                            ->label('Closing date')
                            ->default(true),
                        Toggle::make('job_field_settings.expires_at')
                            ->label('Expiry date')
                            ->default(true),
                        Toggle::make('job_field_settings.visa_sponsorship_available')
                            ->label('Visa sponsorship')
                            ->default(true),
                        Toggle::make('job_field_settings.right_to_work_required')
                            ->label('Right to work')
                            ->default(true),
                    ]),
            ]);
    }
}
