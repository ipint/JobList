@php
    $status = $record->status;
    $label = \App\Models\JobAttribute::labelFor('application_status', $status) ?: '-';
    $color = \App\Models\JobAttribute::colorFor('application_status', $status);
    $supportedColors = ['danger', 'success', 'warning', 'info', 'primary', 'gray'];
    $color = in_array($color, $supportedColors, true) ? $color : 'gray';
@endphp

<div class="fi-ta-text-item fi-ta-text-has-badges fi-ta-text">
    <span class="fi-color fi-color-{{ $color }} fi-text-color-600 dark:fi-text-color-200 fi-badge fi-size-sm">
        {{ $label }}
    </span>
</div>
