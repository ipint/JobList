<x-filament-panels::page>
    <div style="display: flex; flex-wrap: wrap; align-items: flex-start; gap: 24px; width: 100%;">
        <div style="flex: 1 1 320px; min-width: 0;">
            <x-filament::section>
                <x-slot name="heading">Application</x-slot>

            <div class="fi-section-content-ctn">
                <div
                    class="fi-sc fi-sc-has-gap fi-grid lg:fi-grid-cols fi-section-content"
                    style="--cols-lg: repeat(2, minmax(0, 1fr)); --cols-default: repeat(1, minmax(0, 1fr));"
                >
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">ID</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->id }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Status</span></div>
                                </div>
                                <div class="fi-fo-field-content-col">
                                    @if($this->canManageApplication())
                                        <div class="fi-input-wrp fi-fo-select">
                                            <div class="fi-input-wrp-content-ctn">
                                                <select wire:model.live="status" class="fi-select-input fi-input">
                                                    @foreach($this->statusOptions() as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="fi-sc-text">{{ \App\Models\JobAttribute::labelFor('application_status', $this->record->status) ?: '-' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">First name</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->candidate_first_name ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Last name</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->candidate_last_name ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Email</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->candidate_email ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Phone</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->candidate_phone ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Job</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->job?->title ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Company</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ $this->record->company?->name ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Flag</span></div>
                                </div>
                                <div class="fi-fo-field-content-col">
                                    @php
                                        $flagColors = \App\Models\Application::flagColors();
                                        $colorMap = [
                                            'danger' => '#dc2626',
                                            'success' => '#16a34a',
                                            'warning' => '#d97706',
                                            'info' => '#0284c7',
                                            'primary' => '#2563eb',
                                            'gray' => '#64748b',
                                        ];
                                    @endphp
                                    <div class="flex items-center gap-1" x-on:click.stop.prevent>
                                        @foreach($this->flagOptions() as $value => $label)
                                            @php
                                                $isActive = $this->record->flag === $value;
                                                $activeColor = $colorMap[$flagColors[$value] ?? 'gray'] ?? $colorMap['gray'];
                                                $inactiveColor = '#94a3b8';
                                                $buttonStyle = 'font-size: 40px; line-height: 1; color: ' . ($isActive ? $activeColor : $inactiveColor) . '; background-color: transparent; border: 0;';
                                                if ($isActive && $value === 'reject') {
                                                    $buttonStyle = 'font-size: 40px; line-height: 1; color: #dc2626; background-color: transparent; border: 0;';
                                                }
                                            @endphp
                                            @if($this->canManageApplication())
                                                <button
                                                    type="button"
                                                    wire:click.stop.prevent="setFlag('{{ $value }}')"
                                                    x-on:click.stop.prevent
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-md transition"
                                                    title="{{ $label }}"
                                                    style="{{ $buttonStyle }}"
                                                >⚑</button>
                                            @else
                                                <span
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-md"
                                                    title="{{ $label }}"
                                                    style="{{ $buttonStyle }}"
                                                >⚑</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fi-grid-col">
                        <div class="fi-sc-component">
                            <div class="fi-fo-field">
                                <div class="fi-fo-field-label-col">
                                    <div class="fi-fo-field-label-ctn"><span class="fi-fo-field-label-content">Applied at</span></div>
                                </div>
                                <div class="fi-fo-field-content-col"><div class="fi-sc-text">{{ optional($this->record->applied_at)?->format('Y-m-d H:i') ?: '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </x-filament::section>
        </div>

        <div style="flex: 2 1 520px; min-width: 0;">
            <x-filament::section>
                <x-slot name="heading">Notes History</x-slot>

                @if($this->canManageApplication())
                    <form wire:submit.prevent="addNote" class="fi-space-y-3" style="padding-bottom: 24px;">
                        <x-filament::input.wrapper>
                            <textarea
                                wire:model.defer="newNote"
                                rows="4"
                                class="fi-input block w-full rounded-lg bg-white px-3 py-2 text-sm dark:bg-gray-900"
                                style="border: 1px solid #9ca3af; min-height: 96px;"
                                placeholder="Add a note..."
                            ></textarea>
                        </x-filament::input.wrapper>
                        @error('newNote')
                            <p class="fi-text-sm fi-text-danger-600">{{ $message }}</p>
                        @enderror
                        <div style="margin-bottom: 16px;">
                            <x-filament::button type="submit">Add Note</x-filament::button>
                        </div>
                    </form>
                @endif

                <div class="fi-mt-5 fi-space-y-3">
                    {{ $this->table }}
                </div>
            </x-filament::section>
        </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; align-items: flex-start; gap: 24px; padding-top: 12px; width: 100%;">
        <div style="flex: 1 1 420px; min-width: 0;">
            <x-filament::section>
                <x-slot name="heading">CV Preview</x-slot>
            @if($this->cvPreviewUrl())
                @if($this->cvIsEmbeddable())
                    <div class="w-full min-h-[600px] h-[70vh]">
                        <iframe src="{{ $this->cvEmbedUrl() }}" class="block h-full w-full rounded-lg border border-gray-200 dark:border-gray-700" style="height: 600px; width: 100%;"></iframe>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Inline preview is available for PDF, DOC, and DOCX files. This file can be opened in a new tab.</p>
                    <div class="fi-mt-3">
                        <x-filament::button tag="a" href="{{ $this->cvPreviewUrl() }}" target="_blank">Open CV</x-filament::button>
                    </div>
                @endif
            @else
                <p class="text-sm text-gray-500">No CV uploaded.</p>
            @endif
            </x-filament::section>
        </div>

        <div style="flex: 1 1 420px; min-width: 0;">
            <x-filament::section>
                <x-slot name="heading">Cover Letter Preview</x-slot>
            @if($this->coverLetterPreviewUrl())
                @if($this->coverLetterIsEmbeddable())
                    <div class="w-full min-h-[600px] h-[70vh]">
                        <iframe src="{{ $this->coverLetterEmbedUrl() }}" class="block h-full w-full rounded-lg border border-gray-200 dark:border-gray-700" style="height: 600px; width: 100%;"></iframe>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Inline preview is available for PDF, DOC, and DOCX files. This file can be opened in a new tab.</p>
                    <div class="fi-mt-3">
                        <x-filament::button tag="a" href="{{ $this->coverLetterPreviewUrl() }}" target="_blank">Open Cover Letter</x-filament::button>
                    </div>
                @endif
            @else
                <p class="text-sm text-gray-500">No cover letter uploaded.</p>
            @endif
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
