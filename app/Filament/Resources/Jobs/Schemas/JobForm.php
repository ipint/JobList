<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Models\Company;
use App\Models\JobAttribute;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        Select::make('company_id')
                            ->label('Company')
                            ->options(fn (): array => self::companyOptions())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn (): bool => count(self::companyOptions()) > 1 || (auth()->user()?->canManageAllCompanies() ?? false))
                            ->visible(fn (): bool => count(self::companyOptions()) > 1 || (auth()->user()?->canManageAllCompanies() ?? false))
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $set('company_name', Company::query()->find($state)?->name);
                            }),
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
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('company_name')
                            ->required(fn (): bool => blank(auth()->user()?->accessibleCompanyIds()) && ! (auth()->user()?->canManageAllCompanies() ?? false))
                            ->maxLength(255)
                            ->hidden(fn (): bool => filled(auth()->user()?->accessibleCompanyIds()) || (auth()->user()?->canManageAllCompanies() ?? false)),
                        Select::make('department')
                            ->options(fn (): array => JobAttribute::optionsFor('department'))
                            ->searchable()
                            ->required()
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('department', $get)),
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
                            ->options(fn (): array => JobAttribute::optionsFor('employment_type'))
                            ->searchable()
                            ->required(),
                        Select::make('work_mode')
                            ->options(fn (): array => JobAttribute::optionsFor('work_mode'))
                            ->searchable()
                            ->required(),
                        Select::make('experience_level')
                            ->options(fn (): array => JobAttribute::optionsFor('experience_level'))
                            ->searchable()
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('experience_level', $get)),
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
                            ->required()
                            ->maxLength(16)
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('postcode', $get)),
                        TextInput::make('location_name')
                            ->label('Location display name')
                            ->maxLength(255)
                            ->placeholder('e.g. Manchester City Centre')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('location_name', $get)),
                    ]),
                Section::make('Salary and application')
                    ->columns(2)
                    ->schema([
                        TextInput::make('salary_min')
                            ->numeric()
                            ->prefix('GBP')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('salary', $get)),
                        TextInput::make('salary_max')
                            ->numeric()
                            ->prefix('GBP')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('salary', $get)),
                        Select::make('salary_period')
                            ->options([
                                'year' => 'Per year',
                                'day' => 'Per day',
                                'hour' => 'Per hour',
                            ])
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('salary', $get)),
                        TextInput::make('salary_text')
                            ->maxLength(255)
                            ->placeholder('e.g. Competitive + bonus')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('salary', $get)),
                        TextInput::make('application_url')
                            ->url()
                            ->maxLength(255)
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('application_url', $get)),
                        TextInput::make('application_email')
                            ->email()
                            ->maxLength(255)
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('application_email', $get)),
                        DatePicker::make('closing_date')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('closing_date', $get)),
                        DatePicker::make('expires_at')
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('expires_at', $get)),
                    ]),
                Section::make('Candidate requirements')
                    ->columns(2)
                    ->schema([
                        Toggle::make('visa_sponsorship_available')
                            ->label('Visa sponsorship available')
                            ->default(false)
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('visa_sponsorship_available', $get)),
                        Toggle::make('right_to_work_required')
                            ->label('Right to work required')
                            ->default(true)
                            ->hidden(fn (Get $get): bool => ! self::isCompanyFieldEnabled('right_to_work_required', $get)),
                        DatePicker::make('published_at'),
                    ]),
                Section::make('Content')
                    ->schema([
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function isCompanyFieldEnabled(string $field, Get $get): bool
    {
        $companyId = $get('company_id') ?: (auth()->user()?->accessibleCompanyIds()[0] ?? null);

        if (! $companyId) {
            return Company::DEFAULT_JOB_FIELD_SETTINGS[$field] ?? true;
        }

        $company = Company::query()->find($companyId);

        return $company?->enabledJobFields()[$field] ?? true;
    }

    protected static function companyOptions(): array
    {
        $user = auth()->user();

        return Company::query()
            ->where('is_active', true)
            ->when(
                ! $user?->canManageAllCompanies(),
                fn ($query) => $query->whereIn('id', $user?->accessibleCompanyIds() ?? []),
            )
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
