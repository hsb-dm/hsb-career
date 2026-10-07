<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>Open Positions at HSB Investasi</title>
    @include('partials.seo', [
        'title' => 'Open Positions at HSB Investasi',
        'description' => 'Explore open positions and build your career with HSB Investasi.',
        'canonical' => route('vacancies.index'),
    ])
    <link rel="preload" href="{{ asset('fonts/hsb/Juturu-VariableVF.woff2?v=20261001') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlRegular.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlMedium.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header')

    <main class="vacancies-page">
        <section class="hero vacancies-hero section-black" aria-labelledby="vacancies-hero-title">
            <div class="hero-top container">
                <aside class="hero-fraud-warning" role="status" id="hero-fraud-warning">
                    <p>Waspada penipuan yang mengatasnamakan HSB. <a
                            href="https://www.hsb.co.id/fraud-warning#pengumuman" target="_blank"
                            rel="noopener noreferrer">Klik di sini untuk melihat klarifikasi lengkapnya.</a></p>
                    <button type="button" aria-label="Tutup peringatan penipuan"
                        onclick="this.closest('#hero-fraud-warning').remove()"><svg width="12" height="12"
                            viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 2L10 10M10 2L2 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" />
                        </svg></button>
                </aside>
                <nav class="hero-breadcrumb" aria-label="Breadcrumb">
                    <a href="https://www.hsb.co.id/">Home</a>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <a href="{{ route('home') }}">Career</a>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span aria-current="page">All Careers</span>
                </nav>
            </div>
            <div class="container hero-inner">
                <div class="hero-copy">
                    <h1 id="vacancies-hero-title">BUILD YOUR <span>CAREER</span><br>WITH CONFIDENCE</h1>
                </div>
                <img class="hero-image" src="{{ asset('images/home/hero.webp') }}"
                    alt="Dua karyawan HSB membawa perangkat kerja" width="576" height="443" fetchpriority="high">
            </div>
        </section>

        <section class="vacancies-positions section-deep" id="open-positions" aria-labelledby="positions-title">
            <div class="container">
                <h2 class="section-title" id="positions-title"><span>OPEN POSITIONS</span> AT HSB</h2>
                @include('partials.job-filters')
                <div class="job-list vacancies-grid" id="job-list">
                    @forelse ($jobs as $job)
                        <article class="job-card vacancies-card"
                            data-title="{{ strtolower($job->title . ' ' . $job->department . ' ' . $job->location) }}"
                            data-type="{{ $job->employment_type->value }}">
                            <h3><a href="{{ route('vacancies.show', $job->slug) }}">{{ $job->title }}</a></h3>
                            <div class="vacancies-card-details">
                                <p><svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><rect x="3.5" y="6" width="13" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 6V4.5A1.5 1.5 0 0 1 8.5 3h3A1.5 1.5 0 0 1 13 4.5V6" stroke="currentColor" stroke-width="1.5"/></svg>{{ $job->department }}</p>
                                <p><svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M4.5 16v-1.5a5.5 5.5 0 0 1 11 0V16h-11Z" stroke="currentColor" stroke-width="1.5"/></svg>{{ $job->employment_type->label() }}</p>
                                <p><svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><path d="M15 8.5c0 4-5 8-5 8s-5-4-5-8a5 5 0 1 1 10 0Z" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="8.5" r="1.6" stroke="currentColor" stroke-width="1.5"/></svg>{{ $job->location }}</p>
                            </div>
                            <a class="button button-lime button-small" href="{{ route('vacancies.show', $job->slug) }}">Apply Now
                                <svg class="button-arrow" width="20" height="20" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" /><path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" /></svg>
                            </a>
                        </article>
                    @empty
                        <p class="jobs-empty">No open positions right now. Please check back soon.</p>
                    @endforelse
                    <p class="jobs-empty" id="job-filter-empty" hidden>No roles match your search.</p>
                </div>
            </div>
        </section>
        @include('partials.talent-pool')
    </main>

    @include('partials.footer')
</body>

</html>
