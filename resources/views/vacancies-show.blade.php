<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>{{ $job->title }} | Careers at HSB Investasi</title>
    <meta name="description" content="Explore the {{ $job->title }} role at HSB Investasi in {{ $job->location }}.">
    <link rel="preload" href="{{ asset('fonts/hsb/Juturu-VariableVF.woff2?v=20261001') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlRegular.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlMedium.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header')

    <main class="vacancy-detail-page">
        <section class="hero vacancy-detail-hero section-black" aria-labelledby="job-title">
            <div class="hero-top container">
                <aside class="hero-fraud-warning" role="status" id="hero-fraud-warning">
                    <p>Waspada penipuan yang mengatasnamakan HSB. <a
                            href="https://www.hsb.co.id/fraud-warning#pengumuman" target="_blank"
                            rel="noopener noreferrer">Klik di sini untuk melihat klarifikasi lengkapnya.</a></p>
                    <button type="button" aria-label="Tutup peringatan penipuan"
                        onclick="this.closest('#hero-fraud-warning').remove()"><svg width="12" height="12"
                            viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 2L10 10M10 2L2 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" /></svg></button>
                </aside>
                <nav class="hero-breadcrumb" aria-label="Breadcrumb">
                    <a href="https://www.hsb.co.id/">Home</a>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <a href="{{ route('home') }}">Career</a>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <span aria-current="page">{{ $job->title }}</span>
                </nav>
            </div>
            <div class="container hero-inner">
                <div class="hero-copy">
                    <h1 id="job-title">{{ $job->title }}</h1>
                    <p class="vacancy-posted"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 3v4M17 3v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Date Posted : {{ $job->published_at?->format('M d, Y') ?? '—' }}</p>
                </div>
            </div>
        </section>

        <section class="vacancy-detail-body section-deep" aria-label="Job description">
            <div class="container vacancy-detail-layout">
                <div class="vacancy-description-column">
                    <div class="vacancy-description" id="vacancy-description" data-vacancy-description>
                        {!! \Illuminate\Support\Str::sanitizeHtml($job->description) !!}
                    </div>
                    <button class="vacancy-read-more" type="button" aria-controls="vacancy-description"
                        aria-expanded="false" data-vacancy-read-more hidden><span data-read-more-label>Read More</span>
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4 7 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                </div>
                <aside class="vacancy-apply-card" aria-label="Apply for this role">
                    <h2>{{ $job->title }}</h2>
                    <p class="vacancy-card-date"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 3v4M17 3v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Date Posted : {{ $job->published_at?->format('M d, Y') ?? '—' }}</p>
                    <a class="button button-lime vacancy-apply" href="{{ route('vacancies.apply', $job->slug) }}">Apply Now
                        <svg class="button-arrow" width="24" height="24" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" /><path d="M7.5 20.1 20.1 7.5M10.3 7.5h9.8v9.8" stroke="#0F322C" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </a>
                </aside>
            </div>
        </section>
    </main>

    @include('partials.footer')
</body>

</html>
