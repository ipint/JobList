<?php

namespace App\Filament\Resources\Jobs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
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
                        TextInput::make('reference')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('department')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'expired' => 'Expired',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                        Select::make('employment_type')
                            ->options([
                                'full_time' => 'Full-time',
                                'part_time' => 'Part-time',
                                'contract' => 'Contract',
                                'temporary' => 'Temporary',
                                'internship' => 'Internship',
                            ])
                            ->required(),
                        Select::make('work_mode')
                            ->options([
                                'on_site' => 'On-site',
                                'hybrid' => 'Hybrid',
                                'remote' => 'Remote',
                            ])
                            ->required(),
                        Select::make('experience_level')
                            ->options([
                                'entry' => 'Entry',
                                'junior' => 'Junior',
                                'mid' => 'Mid',
                                'senior' => 'Senior',
                                'lead' => 'Lead',
                            ]),
                        Toggle::make('is_featured')
                            ->label('Featured job')
                            ->default(false),
                        Toggle::make('is_salary_visible')
                            ->label('Show salary')
                            ->default(true),
                    ]),
                Section::make('Location')
                    ->columns(2)
                    ->schema([
                        Select::make('county_id')
                            ->relationship('county', 'name', fn ($query) => $query->where('is_active', true)->orderBy('display_order'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('city')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('postcode')
                            ->maxLength(16),
                        TextInput::make('location_name')
                            ->label('Location display name')
                            ->maxLength(255)
                            ->placeholder('e.g. Manchester City Centre'),
                    ]),
                Section::make('Salary and application')
                    ->columns(2)
                    ->schema([
                        TextInput::make('salary_min')
                            ->numeric()
                            ->prefix('GBP'),
                        TextInput::make('salary_max')
                            ->numeric()
                            ->prefix('GBP'),
                        Select::make('salary_period')
                            ->options([
                                'year' => 'Per year',
                                'day' => 'Per day',
                                'hour' => 'Per hour',
                            ]),
                        TextInput::make('salary_text')
                            ->maxLength(255)
                            ->placeholder('e.g. Competitive + bonus'),
                        TextInput::make('application_url')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('application_email')
                            ->email()
                            ->maxLength(255),
                        DatePicker::make('closing_date'),
                        DatePicker::make('expires_at'),
                    ]),
                Section::make('Candidate requirements')
                    ->columns(2)
                    ->schema([
                        Toggle::make('visa_sponsorship_available')
                            ->label('Visa sponsorship available')
                            ->default(false),
                        Toggle::make('right_to_work_required')
                            ->label('Right to work required')
                            ->default(true),
                        DatePicker::make('published_at'),
                    ]),
                Section::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),
                        Textarea::make('requirements')
                            ->rows(6)
                            ->columnSpanFull(),
                        Textarea::make('benefits')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
