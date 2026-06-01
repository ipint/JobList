<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Models\Application;
use App\Models\JobAttribute;
use App\Models\Job;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application')
                    ->columns(2)
                    ->schema([
                        Select::make('job_id')
                            ->label('Job')
                            ->options(fn (): array => self::jobOptions())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(fn (): array => JobAttribute::optionsFor('application_status'))
                            ->default('new')
                            ->required(),
                        Select::make('flag')
                            ->options(Application::flagOptions())
                            ->placeholder('No flag')
                            ->selectablePlaceholder()
                            ->helperText('Use this for quick triage on the applications table.'),
                        DateTimePicker::make('applied_at')
                            ->seconds(false)
                            ->default(now()),
                        TextInput::make('source')
                            ->maxLength(255)
                            ->placeholder('e.g. Website, LinkedIn, referral'),
                    ]),
                Section::make('Candidate')
                    ->columns(2)
                    ->schema([
                        TextInput::make('candidate_first_name')
                            ->label('First name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('candidate_last_name')
                            ->label('Last name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('candidate_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('candidate_phone')
                            ->tel()
                            ->maxLength(255),
                        FileUpload::make('cv_path')
                            ->label('CV')
                            ->disk('public')
                            ->directory('applications/cvs')
                            ->acceptedFileTypes(self::documentMimeTypes())
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->maxSize(10240)
                            ->helperText('Allowed: PDF, DOC, DOCX. Maximum 10 MB.'),
                    ]),
                Section::make('Details')
                    ->schema([
                        FileUpload::make('cover_letter_path')
                            ->label('Cover letter')
                            ->disk('public')
                            ->directory('applications/cover-letters')
                            ->acceptedFileTypes(self::documentMimeTypes())
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->maxSize(10240)
                            ->helperText('Allowed: PDF, DOC, DOCX. Maximum 10 MB.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function jobOptions(): array
    {
        $user = auth()->user();

        return Job::query()
            ->when(! $user?->canManageAllCompanies(), fn ($query) => $query->whereIn('company_id', $user?->accessibleCompanyIds() ?? []))
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    protected static function documentMimeTypes(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }
}
