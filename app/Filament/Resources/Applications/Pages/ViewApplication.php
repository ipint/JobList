<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use App\Models\JobAttribute;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;

class ViewApplication extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ApplicationResource::class;

    protected string $view = 'filament.resources.applications.pages.view-application';

    public string $newNote = '';
    public ?string $status = null;
    public ?string $flag = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->loadMissing(['job', 'company', 'notes.user']);
        $this->status = $this->record->status;
        $this->flag = $this->record->flag;
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();

        if (! ($user?->canAccess('applications', 'view') ?? false)) {
            return false;
        }

        $recordParam = $parameters['record'] ?? null;
        if (blank($recordParam)) {
            return false;
        }

        $record = null;

        if ($recordParam instanceof Application) {
            $record = $recordParam;
        } else {
            $recordId = is_array($recordParam) ? ($recordParam[0] ?? null) : $recordParam;

            if (filled($recordId)) {
                $record = Application::query()->whereKey($recordId)->first();
            }
        }

        return filled($record) && $user->hasCompanyAccess($record->company_id);
    }

    public function getTitle(): string
    {
        return trim(($this->record->candidate_first_name ?? '') . ' ' . ($this->record->candidate_last_name ?? ''));
    }

    public function getBreadcrumbs(): array
    {
        return [
            ApplicationResource::getUrl('index', request()->query()) => ApplicationResource::getBreadcrumb(),
            '' => $this->getTitle(),
        ];
    }

    public function canManageApplication(): bool
    {
        return auth()->user()?->canAccess('applications', 'edit') ?? false;
    }

    public function addNote(): void
    {
        abort_unless($this->canManageApplication(), 403);

        $this->validate([
            'newNote' => ['required', 'string', 'max:5000'],
        ]);

        $this->record->notes()->create([
            'user_id' => auth()->id(),
            'note' => trim($this->newNote),
        ]);

        $this->record->refresh()->load(['job', 'company', 'notes.user']);
        $this->newNote = '';

        Notification::make()
            ->title('Note added')
            ->success()
            ->send();
    }

    public function updatedStatus(?string $status): void
    {
        abort_unless($this->canManageApplication(), 403);

        if (! array_key_exists((string) $status, JobAttribute::optionsFor('application_status'))) {
            $this->status = $this->record->status;
            return;
        }

        $this->record->update(['status' => $status]);
        $this->record->refresh()->loadMissing(['job', 'company', 'notes.user']);

        Notification::make()
            ->title('Status updated')
            ->success()
            ->send();
    }

    public function updatedFlag(?string $flag): void
    {
        abort_unless($this->canManageApplication(), 403);

        if (filled($flag) && ! array_key_exists((string) $flag, Application::flagOptions())) {
            $this->flag = $this->record->flag;
            return;
        }

        $this->record->update(['flag' => filled($flag) ? $flag : null]);
        $this->record->refresh()->loadMissing(['job', 'company', 'notes.user']);
        $this->flag = $this->record->flag;

        Notification::make()
            ->title('Flag updated')
            ->success()
            ->send();
    }

    public function setFlag(?string $flag): void
    {
        abort_unless($this->canManageApplication(), 403);

        if (filled($flag) && ! array_key_exists((string) $flag, Application::flagOptions())) {
            return;
        }

        $this->record->update(['flag' => filled($flag) ? $flag : null]);
        $this->record->refresh()->loadMissing(['job', 'company', 'notes.user']);
        $this->flag = $this->record->flag;

        Notification::make()
            ->title('Flag updated')
            ->success()
            ->send();
    }

    public function statusOptions(): array
    {
        return JobAttribute::optionsFor('application_status');
    }

    public function flagOptions(): array
    {
        return Application::flagOptions();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->notes()->getQuery()->with('user'))
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System'),
                TextColumn::make('note')
                    ->wrap(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => $this->canManageApplication())
                    ->form([
                        Textarea::make('note')
                            ->required()
                            ->rows(4)
                            ->maxLength(5000),
                    ]),
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->visible(fn (): bool => $this->canManageApplication())
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->delete();

                        Notification::make()
                            ->title('Note deleted')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function cvPreviewUrl(): ?string
    {
        return filled($this->record->cv_path) ? Storage::disk('public')->url($this->record->cv_path) : null;
    }

    public function coverLetterPreviewUrl(): ?string
    {
        return filled($this->record->cover_letter_path) ? Storage::disk('public')->url($this->record->cover_letter_path) : null;
    }

    public function cvIsEmbeddable(): bool
    {
        return in_array($this->cvExtension(), ['pdf', 'doc', 'docx'], true);
    }

    public function coverLetterIsEmbeddable(): bool
    {
        return in_array($this->coverLetterExtension(), ['pdf', 'doc', 'docx'], true);
    }

    public function cvEmbedUrl(): ?string
    {
        $source = $this->cvPreviewUrl();
        if (! filled($source)) {
            return null;
        }

        return $this->embedUrlFor($source, $this->cvExtension());
    }

    public function coverLetterEmbedUrl(): ?string
    {
        $source = $this->coverLetterPreviewUrl();
        if (! filled($source)) {
            return null;
        }

        return $this->embedUrlFor($source, $this->coverLetterExtension());
    }

    protected function embedUrlFor(string $sourceUrl, ?string $extension): ?string
    {
        if ($extension === 'pdf') {
            return $sourceUrl . '#toolbar=1&navpanes=0&scrollbar=1';
        }

        if (in_array($extension, ['doc', 'docx'], true)) {
            return 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($sourceUrl) . '&embedded=true';
        }

        return null;
    }

    protected function cvExtension(): ?string
    {
        return $this->extensionFromPath($this->record->cv_path);
    }

    protected function coverLetterExtension(): ?string
    {
        return $this->extensionFromPath($this->record->cover_letter_path);
    }

    protected function extensionFromPath(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
    }
}
