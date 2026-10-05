@php
    $selectedType = request()->routeIs('vacancies.index') ? request()->query('type', 'all') : 'all';
@endphp
<form class="job-filters" action="{{ route('vacancies.index') }}" method="GET" data-job-filters data-page="{{ request()->routeIs('home') ? 'home' : 'vacancies' }}">
    <div class="select-wrap" data-job-select>
        <input id="job-type-filter" name="type" type="hidden" value="{{ $selectedType }}">
        <button class="talent-select-trigger" type="button" aria-label="Filter job type"
            aria-haspopup="listbox" aria-expanded="false" aria-controls="job-type-options">
            <span data-job-selection>{{ collect(\App\Enums\EmploymentType::cases())->first(fn ($type) => $type->value === $selectedType)?->label() ?? 'All Job Types' }}</span>
            <span class="talent-chevron" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="m4 7 6 6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
        </button>
        <div class="talent-options" id="job-type-options" role="listbox" aria-label="Job type" hidden>
            <button type="button" role="option" aria-selected="{{ $selectedType === 'all' ? 'true' : 'false' }}" data-value="all">All Job Types</button>
            @foreach (\App\Enums\EmploymentType::cases() as $type)
                <button type="button" role="option" aria-selected="{{ $selectedType === $type->value ? 'true' : 'false' }}" data-value="{{ $type->value }}">{{ $type->label() }}</button>
            @endforeach
        </div>
    </div>
    <div class="search-wrap"><label class="sr-only" for="job-search">Search by keyword</label>
        <input id="job-search" name="q" type="search" placeholder="Search by keyword" value="{{ request()->routeIs('vacancies.index') ? request()->query('q', '') : '' }}">
        <button class="search-icon" type="submit" aria-label="Search jobs"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
            <path d="m16 16 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg></button>
    </div>
</form>
