<footer class="jl-admin-footer">
    @if (filled($settings?->footerLogoUrl()))
        <img
            src="{{ $settings->footerLogoUrl() }}"
            alt="{{ config('app.name', 'JobList') }}"
            class="jl-admin-footer-logo"
        >
    @endif

    <span>{{ $settings?->copyright_text ?: '(c) ' . now()->year . ' JobList. All rights reserved.' }}</span>
</footer>
