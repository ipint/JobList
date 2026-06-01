@php
    $currentFlag = $record->flag;

    $flagOptions = \App\Models\Application::flagOptions();
    $flagColors = \App\Models\Application::flagColors();

    $buttonBaseClasses = 'inline-flex h-10 w-10 items-center justify-center rounded-md border transition';

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
    @foreach ($flagOptions as $value => $label)
        @php
            $isActive = $currentFlag === $value;
            $activeColor = $colorMap[$flagColors[$value] ?? 'gray'] ?? $colorMap['gray'];
            $inactiveColor = '#94a3b8';
            $buttonLabel = '⚑';
            $buttonStyle = 'font-size: 40px; line-height: 1; color: ' . ($isActive ? $activeColor : $inactiveColor) . '; background-color: transparent; border-color: rgba(148,163,184,0.45);';
            if ($isActive && $value === 'reject') {
                $buttonStyle = 'font-size: 40px; line-height: 1; color: #dc2626; background-color: transparent; border-color: rgba(220,38,38,0.45);';
            }
        @endphp
        <button
            type="button"
            wire:click.stop.prevent="setFlag({{ $record->id }}, '{{ $value }}')"
            x-on:click.stop.prevent
            class="{{ $buttonBaseClasses }}"
            title="{{ $label }}"
            style="{{ $buttonStyle }}"
        >
            {{ $buttonLabel }}
        </button>
    @endforeach
</div>
