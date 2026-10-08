<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>{{ $job->title }} | Careers at HSB Investasi</title>
    @include('partials.seo', [
        'title' => $job->title.' | Careers at HSB Investasi',
        'description' => 'Explore the '.$job->title.' role at HSB Investasi in '.$job->location.'.',
        'canonical' => route('vacancies.show', $job->slug),
        'type' => 'article',
    ])
    @if ($job->published_at)
        @php
            $jobPosting = [
                '@context' => 'https://schema.org',
                '@type' => 'JobPosting',
                'title' => $job->title,
                'description' => \Illuminate\Support\Str::sanitizeHtml($job->description),
                'datePosted' => $job->published_at->toIso8601String(),
                'employmentType' => match ($job->employment_type) {
                    \App\Enums\EmploymentType::FullTime => 'FULL_TIME',
                    \App\Enums\EmploymentType::Contract => 'CONTRACTOR',
                    \App\Enums\EmploymentType::Internship => 'INTERN',
                    \App\Enums\EmploymentType::Freelance => 'OTHER',
                },
                'hiringOrganization' => [
                    '@type' => 'Organization',
                    'name' => 'HSB Investasi',
                    'sameAs' => 'https://www.hsb.co.id/',
                    'logo' => asset('images/header/logo-hsb-v2.webp'),
                ],
                'jobLocation' => [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'ID',
                    ],
                ],
                'url' => route('vacancies.show', $job->slug),
                'directApply' => true,
            ];

            if ($job->work_arrangement === \App\Enums\WorkArrangement::Remote) {
                $jobPosting['jobLocationType'] = 'TELECOMMUTE';
                $jobPosting['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => 'ID'];
            } else {
                $jobPosting['jobLocation']['address']['addressLocality'] = $job->location;
            }
        @endphp
        <script type="application/ld+json">{!! json_encode($jobPosting, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!}</script>
    @endif
    <link rel="preload" href="{{ asset('fonts/hsb/Juturu-VariableVF.woff2?v=20261001') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlRegular.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlMedium.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="vacancy-detail-screen">
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
                    <div class="vacancy-description">
                        {!! \Illuminate\Support\Str::sanitizeHtml($job->description) !!}
                    </div>
                </div>
                <aside class="vacancy-apply-card" aria-label="Apply for this role">
                    <h2>{{ $job->title }}</h2>
                    <ul class="vacancy-card-details">
                        <li><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><rect x="4" y="10" width="24" height="18" rx="2" stroke="currentColor" stroke-width="2.5"/><path d="M12 10V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/></svg><span>{{ $job->department }}</span></li>
                        <li><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="9" r="5" stroke="currentColor" stroke-width="2.5"/><path d="M5 28v-3a9 9 0 0 1 9-9h4a9 9 0 0 1 9 9v3H5Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/></svg><span>{{ str_replace(' ', '-', $job->employment_type->label()) }}</span></li>
                        <li><svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M16 29s10-9.1 10-18a10 10 0 1 0-20 0c0 8.9 10 18 10 18Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><circle cx="16" cy="11" r="3.5" stroke="currentColor" stroke-width="2.5"/></svg><span>{{ $job->location }}</span></li>
                    </ul>
                    <a class="button button-lime vacancy-apply" href="{{ route('vacancies.apply', $job->slug) }}"><span>Apply Now</span>
                        <svg class="button-arrow" viewBox="0 0 44 44" fill="none" aria-hidden="true"><rect width="44" height="44" rx="8" fill="#A7CB19" /><path d="M12 32 32 12M16 12h16v16" stroke="#0F322C" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </a>
                </aside>
            </div>
        </section>
    </main>

    @include('partials.footer')
</body>

</html>
