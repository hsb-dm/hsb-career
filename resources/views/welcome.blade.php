<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>Careers at HSB Investasi</title>
    @include('partials.seo', [
        'title' => 'Careers at HSB Investasi',
        'description' => 'Build your career with confidence at HSB Investasi. Discover our culture, benefits, and open positions.',
        'canonical' => route('home'),
    ])
    <link rel="preload" href="{{ asset('fonts/hsb/Juturu-VariableVF.woff2?v=20261001') }}" as="font" type="font/woff2"
        crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlRegular.ttf?v=20261001') }}" as="font" type="font/ttf"
        crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlMedium.ttf?v=20261001') }}" as="font" type="font/ttf"
        crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header')

    <main>
        <section class="hero home-hero section-black" aria-labelledby="hero-title">
            <div class="hero-top container">
                <aside class="hero-fraud-warning" role="status" id="hero-fraud-warning">
                    <p>Beware of fraud in the name of HSB. <a
                            href="https://www.hsb.co.id/fraud-warning#pengumuman" target="_blank"
                            rel="noopener noreferrer">Click here to see the full clarification.</a></p>
                    <button type="button" aria-label="Close fraud warning"
                        onclick="this.closest('#hero-fraud-warning').remove()"><svg width="12" height="12"
                            viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 2L10 10M10 2L2 10" stroke="currentColor" stroke-width="1.25"
                                stroke-linecap="round" />
                        </svg></button>
                </aside>
                <nav class="hero-breadcrumb" aria-label="Breadcrumb"><a href="https://www.hsb.co.id/">Home</a><svg
                        width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg><span aria-current="page">Career</span></nav>
            </div>
            <div class="container hero-inner">
                <div class="hero-copy">
                    <h1 id="hero-title">BUILD YOUR <span>CAREER</span><br>WITH CONFIDENCE</h1>
                    <p>Join HSB Investasi and shape your future through continuous learning and meaningful work.</p>
                    <a class="hero-button" href="#open-positions"><span>See Open Roles</span><svg width="28" height="28"
                            viewBox="0 0 28 28" fill="none" aria-hidden="true">
                            <rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" />
                            <path
                                d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z"
                                fill="#0F322C" />
                        </svg></a>
                </div>
                <img class="hero-image" src="{{ asset('images/home/hero.webp') }}"
                    alt="Dua karyawan HSB membawa perangkat kerja" width="576" height="443" fetchpriority="high">
            </div>
        </section>

        <section class="mission section-black" aria-labelledby="mission-title">
            <div class="container">
                <h2 class="section-title" id="mission-title">A MISSION LED BY <span>VALUES</span></h2>
                <div class="mission-grid">
                    @foreach ([['F', 'Futures', 'Dedicated to finding forward-thinking ways to improve and evolve.'], ['O', 'Ownership', 'Owning the tasks and its impacts.'], ['C', 'Candor', 'Foster open dialogue with integrity and maturity.'], ['U', 'Unyielding', 'Stay resilient and committed to overcoming challenges proactively.'], ['S', 'Solver', 'Solution-driven approach.']] as [$letter, $name, $description])
                        <article class="mission-card">
                            <div class="mission-icon"><span>{{ $letter }}</span></div>
                            <h3>{{ $name }}</h3>
                            <p>{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="values section-deep" id="values" aria-labelledby="values-title">
            <div class="container">
                <h2 class="section-title" id="values-title">THE <span>VALUES</span> WE LIVE BY</h2>
                <div class="values-layout">
                    <img class="values-pyramid" src="{{ asset('images/home/values-pyramid.svg') }}"
                        alt="Urgency, Adaptability, and Thrive Together" width="522" height="456">
                    <div class="values-copy">
                        <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34"
                                height="34">
                            <p>We move fast to seize them, whether it’s launching a feature or spreading a market
                                message. Every task counts and every moment matters. Your speed turns ideas into
                                reality.</p>
                        </div>
                        <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34"
                                height="34">
                            <p>Means pivoting when feedback hits, learning new tools, and growing stronger with every
                                challenge, It’s how we stayed ahead.</p>
                        </div>
                        <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34"
                                height="34">
                            <p>We’re not on solo; we’re a crew. From developers to compliance experts, every role fuels
                                our win. We support each other, share ideas, and celebrate as one. Together, we’re
                                unstoppable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about section-deep" id="about" aria-labelledby="about-title">
            <div class="container about-layout">
                <div class="about-copy">
                    <h2 class="section-title-left section-title" id="about-title"><span>ABOUT</span> HSB INVESTASI</h2>
                    <p class="lead-accent">Grow with a team that's always moving forward.</p>
                    <p>Since 2018, HSB Investasi has grown through the people behind it. A team of curious and ambitious
                        individuals who continue to innovate, including becoming the first broker in Indonesia to bring
                        AI-powered trading insights to retail investors.</p>
                    <p>We believe growth is about more than business. It's about creating opportunities for our people
                        to learn, contribute, and build careers they're proud of. Join us to take on new challenges and
                        grow together.</p>
                </div>
                <div class="about-media"><a href="https://www.instagram.com/hsb.life/" target="_blank"
                        rel="nofollow noopener noreferrer" aria-label="Visit HSB Life on Instagram"><img
                            src="{{ asset('images/home/about.webp') }}"
                            alt="HSB Investasi team celebrating birthdays and a company gathering. Discover Life at HSB: @hsb.life"
                            width="862" height="997" loading="lazy"></a></div>
            </div>
        </section>

        <section class="people section-deep" id="people" aria-labelledby="people-title">
            <div class="container">
                <h2 class="section-title" id="people-title"><span>MEET THE PEOPLE</span><br>BEHIND HSB</h2>
                <p class="section-intro">Discover the people and stories that bring our culture to life.</p>
                <div class="people-grid">
                    @foreach ([
                            ['Fia', 'Finance, Accounting & Tax Manager', 'Proud to be part of HSB Investasi, where I grow and make an impact every day.'],
                            ['Tasya', 'Senior Legal & Compliance Officer', 'I’m proud to protect HSB’s reputation while helping the business move forward with integrity.'],
                            ['Cio', 'Product Manager', 'At HSB, I value the trust to take full ownership, from identifying problems to launching solutions.'],
                            ['Iqbal', 'Performance Marketing Specialist', 'Working at HSB provides tremendous opportunities for growth.'],
                            ['Raden', 'IT Senior Officer', 'HSB has opened new horizons for me in technology.'],
                            ['Sima', 'Relationship Manager (WPB)', 'HSB has tremendous growth potential.'],
                        ] as [$name, $role, $quote])
                        <article class="person-card">
                            <span class="quote-mark" aria-hidden="true"><svg viewBox="0 0 43 33" fill="currentColor"
                                    focusable="false">
                                    <path
                                        d="M12.4 0h9.1L13 16.3c3.9 1.3 6.2 4.2 6.2 8.1 0 5.1-4.1 8.6-9.3 8.6C4.2 33 0 29.1 0 23.5c0-3.2 1.1-6.4 3.3-10L12.4 0Zm21.5 0H43l-8.5 16.3c3.9 1.3 6.2 4.2 6.2 8.1 0 5.1-4.1 8.6-9.3 8.6-5.7 0-9.9-3.9-9.9-9.5 0-3.2 1.1-6.4 3.3-10L33.9 0Z" />
                                </svg></span>
                            <h3>{{ $name }}</h3>
                            <p class="person-role">{{ $role }}</p>
                            <p class="person-quote">{{ $quote }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="benefits section-dark" id="benefits" aria-labelledby="benefits-title">
            <div class="container">
                <h2 class="section-title" id="benefits-title">WHAT COMES<br>WITH <span>THE ROLE</span></h2>
                <p class="section-intro">We support your growth with competitive benefits, a great environment,<br>and
                    exclusive trips rewarding top performance.</p>
                <div class="benefits-grid">
                    @foreach ([['PROFESSIONAL GROWTH', 'Access courses, certifications, and training to build skills that move your career forward.'], ['CAREER DEVELOPMENT PROGRAM', 'Grow your skills through our Specialist Program and unlock new opportunities within your career.'], ['PERFORMANCE INCENTIVE & REWARD SCHEME', 'Get recognized and rewarded for the impact you make with bonuses, trips, events, and more.'], ['TEAM EVENTS & ENGAGEMENTS', 'Connect, collaborate, and have fun through team events, sports, and celebrations.'], ['FREEFLOW SNACKS & COFFEE', 'The pantry is always stocked. Fuel your focus whenever you need it.']] as [$name, $description])
                        <article class="benefit-card">
                            <h3>{{ $name }}</h3>
                            <p>{{ $description }}</p>
                    </article>@endforeach
                </div>
            </div>
        </section>

        <section class="life section-deep" id="life" aria-labelledby="life-title">
            <div class="container">
                <h2 class="section-title" id="life-title">GET A <span>GLIMPSE</span><br class="life-mobile-break"> OF LIFE AT HSB</h2>
                @php
                    $lifeCategories = [
                        ['slug' => 'sport', 'label' => 'Sport Day'],
                        ['slug' => 'birthday', 'label' => 'Birthday Celebration'],
                        ['slug' => 'special', 'label' => 'Special Day Celebration'],
                        ['slug' => 'reward-trip', 'label' => 'BD Reward Trip'],
                    ];
                    $lifeImageIds = ['3498', '3499', '3500', '3501'];
                @endphp
                <div class="life-gallery" data-life-gallery>
                    <div class="life-tabs" role="tablist" aria-label="Life at HSB categories">
                        @foreach ($lifeCategories as $category)
                            <button class="life-tab" type="button" role="tab"
                                id="life-tab-{{ $category['slug'] }}"
                                aria-controls="life-panel-{{ $category['slug'] }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $category['label'] }}</button>
                        @endforeach
                    </div>
                    <div class="life-panels">
                        @foreach ($lifeCategories as $category)
                            <div class="life-panel {{ $loop->first ? 'is-active' : '' }}"
                                id="life-panel-{{ $category['slug'] }}" role="tabpanel"
                                aria-labelledby="life-tab-{{ $category['slug'] }}" tabindex="0" @if (! $loop->first) hidden @endif>
                                @foreach ($lifeImageIds as $imageId)
                                    <img src="{{ asset('images/home/slider/'.$category['slug'].'/Frame 100001'.$imageId.'.webp') }}"
                                        alt="{{ $category['label'] }} at HSB Investasi, photo {{ $loop->iteration }}"
                                        width="573" height="573" loading="lazy" decoding="async">
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="open-positions section-deep" id="open-positions" aria-labelledby="positions-title">
            <div class="container">
                <h2 class="section-title" id="positions-title"><span>OPEN POSITIONS</span> AT HSB</h2>
                @include('partials.job-filters')
                <div class="job-list" id="job-list">@forelse ($jobs->take(5) as $job)
                    <article class="job-card"
                        data-title="{{ strtolower($job->title . ' ' . $job->department . ' ' . $job->location) }}"
                        data-type="{{ $job->employment_type->value }}">
                        <div class="job-card-main">
                            <h3>{{ $job->title }}</h3>
                            <ul class="job-card-details">
                                <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="7" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8"/></svg>{{ $job->department }}</li>
                                <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M5 21v-2a7 7 0 0 1 14 0v2H5Z" stroke="currentColor" stroke-width="1.8"/></svg>{{ $job->employment_type->label() }}</li>
                                <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 22s8-7.3 8-14a8 8 0 1 0-16 0c0 6.7 8 14 8 14Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="8" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>{{ $job->location }}</li>
                            </ul>
                        </div><a class="button button-lime button-small"
                            href="{{ route('vacancies.show', $job->slug) }}">Apply
                            Now <svg class="button-arrow" width="20" height="20" viewBox="0 0 28 28" fill="none"
                                aria-hidden="true">
                                <rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" />
                                <path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" />
                            </svg></a>
                </article>@empty<p class="jobs-empty">No open positions right now. Please check back soon.</p>
                    @endforelse<p class="jobs-empty" id="job-filter-empty" hidden>No roles match your search.</p>
                </div>
                @if ($jobs->count() > 5)<a class="button button-lime all-jobs-button" href="{{ route('vacancies.index') }}">View all opportunities <svg class="button-arrow" width="24" height="24" viewBox="0 0 28 28"
                    fill="none" aria-hidden="true">
                    <rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" />
                    <path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" />
                </svg></a>@endif
            </div>
        </section>

        @include('partials.talent-pool')
    </main>

    @include('partials.footer')
</body>

</html>
