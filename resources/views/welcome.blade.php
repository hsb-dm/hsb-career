<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>Careers at HSB Investasi</title>
    <meta name="description" content="Build your career with confidence at HSB Investasi. Discover our culture, benefits, and open positions.">
    <link rel="preload" href="{{ asset('fonts/hsb/Juturu-VariableVF.woff2?v=20261001') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlRegular.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/hsb/StolzlMedium.ttf?v=20261001') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.header')

    <main>
        <section class="hero section-black" aria-labelledby="hero-title">
            <div class="container hero-top">
                <aside class="hero-fraud-warning" role="status" id="hero-fraud-warning">
                    <p>Waspada penipuan yang mengatasnamakan HSB. <a href="https://www.hsb.co.id/fraud-warning#pengumuman" target="_blank" rel="noopener noreferrer">Klik di sini untuk melihat klarifikasi lengkapnya.</a></p>
                    <button type="button" aria-label="Tutup peringatan penipuan" onclick="this.closest('#hero-fraud-warning').remove()"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 2L10 10M10 2L2 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" /></svg></button>
                </aside>
                <nav class="hero-breadcrumb" aria-label="Breadcrumb"><a href="https://www.hsb.co.id/">Home</a><svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m4 2 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg><span aria-current="page">Career</span></nav>
            </div>
            <div class="container hero-inner">
                <div class="hero-copy">
                    <h1 id="hero-title">BUILD YOUR <span>CAREER</span><br>WITH CONFIDENCE</h1>
                    <p>Join HSB Investasi and shape your future through continuous learning and meaningful work.</p>
                    <a class="hero-button" href="#open-positions"><span>See Open Roles</span><svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" /><path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" /></svg></a>
                </div>
                <img class="hero-image" src="{{ asset('images/home/hero.webp') }}" alt="Dua karyawan HSB membawa perangkat kerja" width="576" height="443" fetchpriority="high">
            </div>
        </section>

        <section class="mission section-black" aria-labelledby="mission-title"><div class="container">
            <h2 class="section-title" id="mission-title">A MISSION LED BY <span>VALUES</span></h2>
            <div class="mission-grid">
                @foreach ([['F','Futures','Dedicated to finding forward-thinking ways to improve and evolve.'],['O','Ownership','Owning the tasks and its impacts.'],['C','Candor','Foster open dialogue with integrity and maturity.'],['U','Unyielding','Stay resilient and committed to overcoming challenges proactively.'],['S','Solver','Solution-driven approach.']] as [$letter, $name, $description])
                    <article class="mission-card"><div class="mission-icon"><span>{{ $letter }}</span></div><h3>{{ $name }}</h3><p>{{ $description }}</p></article>
                @endforeach
            </div>
        </div></section>

        <section class="values section-deep" id="values" aria-labelledby="values-title"><div class="container">
            <h2 class="section-title" id="values-title">THE <span>VALUES</span> WE LIVE BY</h2>
            <div class="values-layout">
                <img class="values-pyramid" src="{{ asset('images/home/values-pyramid.svg') }}" alt="Urgency, Adaptability, and Thrive Together" width="522" height="456">
                <div class="values-copy">
                    <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34" height="34"><p>We move fast to seize them, whether it’s launching a feature or spreading a market message. Every task counts and every moment matters. Your speed turns ideas into reality.</p></div>
                    <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34" height="34"><p>Means pivoting when feedback hits, learning new tools, and growing stronger with every challenge, It’s how we stayed ahead.</p></div>
                    <div class="value-point"><img src="{{ asset('images/home/value-point.svg') }}" alt="" width="34" height="34"><p>We’re not on solo; we’re a crew. From developers to compliance experts, every role fuels our win. We support each other, share ideas, and celebrate as one. Together, we’re unstoppable.</p></div>
                </div>
            </div>
        </div></section>

        <section class="about section-deep" id="about" aria-labelledby="about-title"><div class="container about-layout">
            <div class="about-copy"><h2 class="section-title section-title-left" id="about-title"><span>ABOUT</span> HSB INVESTASI</h2><p class="lead-accent">Grow with a team that’s always moving forward.</p><p>Since 2018, HSB Investasi has grown through the people behind it. A team of curious and ambitious individuals who continue to innovate, simplify experiences, and drive lasting impact in the finance and trading industry.</p><p>We believe growth is about more than business. It’s about creating opportunities for our people to learn, contribute, and build careers they’re proud of. Join us to take on new challenges and grow together.</p></div>
            <div class="about-media" aria-label="Company photo assets coming soon"><div class="media-slot about-photo-one"><span>Company photo</span></div><div class="about-media-caption">Discover life at HSB<br><span>through our people.</span></div><div class="media-slot about-photo-two"><span>Team photo</span></div></div>
        </div></section>

        <section class="people section-deep" id="people" aria-labelledby="people-title"><div class="container">
            <h2 class="section-title" id="people-title"><span>MEET THE PEOPLE</span><br>BEHIND HSB</h2><p class="section-intro">Discover the people and stories that bring our culture to life.</p>
            <div class="people-grid">@foreach ([['Nofia','Finance, Accounting & Tax Manager'],['Tasya','Legal & Compliance Officer'],['Cio','Product Manager'],['Iqbal','Performance Marketing Specialist'],['Raden','IT Support Officer'],['Sima','Relationship Manager']] as [$name, $role])<article class="person-card"><span class="quote-mark" aria-hidden="true">“</span><h3>{{ $name }}</h3><p class="person-role">{{ $role }}</p><p class="person-placeholder">Their story is coming soon.</p></article>@endforeach</div>
        </div></section>

        <section class="benefits section-dark" id="benefits" aria-labelledby="benefits-title"><div class="container">
            <h2 class="section-title" id="benefits-title">WHAT COMES<br>WITH <span>THE ROLE</span></h2><p class="section-intro">We support your growth with competitive benefits, a great environment,<br>and exclusive perks rewarding top performance.</p>
            <div class="benefits-grid">@foreach ([['BPJS COVERAGE','Full BPJS Kesehatan & Ketenagakerjaan from day one.'],['LEARNING & TRAINING BUDGET','Courses, certifications, and training to invest in your growth.'],['CAREER DEVELOPMENT PROGRAM','Full orientation and a clear path to grow.'],['TEAM EVENTS & ACTIVITIES','From regular gatherings and celebrations to team activities, we make time to connect.'],['UNLIMITED SNACKS & COFFEE','The little things matter. Fuel your focus whenever you need it.']] as [$name, $description])<article class="benefit-card"><h3>{{ $name }}</h3><p>{{ $description }}</p></article>@endforeach</div>
        </div></section>

        <section class="life section-deep" id="life" aria-labelledby="life-title"><div class="container"><h2 class="section-title" id="life-title">GET A <span>GLIMPSE</span> OF LIFE AT HSB</h2><div class="gallery-wrap"><button class="gallery-arrow gallery-prev" type="button" aria-label="Previous photos">‹</button><div class="gallery-track" data-gallery>@foreach (['Team gathering','Company event','Team activities','Life at HSB','Our people','Office moments'] as $item)<div class="media-slot gallery-item"><span>{{ $item }}</span></div>@endforeach</div><button class="gallery-arrow gallery-next" type="button" aria-label="Next photos">›</button></div></div></section>

        <section class="open-positions section-deep" id="open-positions" aria-labelledby="positions-title"><div class="container">
            <h2 class="section-title" id="positions-title"><span>OPEN POSITIONS</span> AT HSB</h2>
            <div class="job-filters"><label class="select-wrap"><span class="sr-only">Filter job type</span><select id="job-type-filter"><option value="all">All Job Types</option>@foreach (\App\Enums\EmploymentType::cases() as $type)<option value="{{ $type->value }}">{{ $type->label() }}</option>@endforeach</select></label><label class="search-wrap"><span class="sr-only">Search by keyword</span><input id="job-search" type="search" placeholder="Search by keyword"><span class="search-icon" aria-hidden="true">⌕</span></label></div>
            <div class="job-list" id="job-list">@forelse ($jobs as $job)<article class="job-card" data-title="{{ strtolower($job->title.' '.$job->department.' '.$job->location) }}" data-type="{{ $job->employment_type->value }}"><div><h3>{{ $job->title }}</h3><p>{{ $job->department }} <span>•</span> {{ $job->employment_type->label() }} <span>•</span> <span class="pin" aria-hidden="true">⌖</span> {{ $job->location }}</p></div><a class="button button-lime button-small" href="mailto:hr@hsb.co.id?subject={{ rawurlencode('Application for '.$job->title) }}">Apply Now <span aria-hidden="true">↗</span></a></article>@empty<p class="jobs-empty">No open positions right now. Please check back soon.</p>@endforelse<p class="jobs-empty" id="job-filter-empty" hidden>No roles match your search.</p></div>
            @if ($jobs->isNotEmpty())<button class="button button-lime all-jobs-button" id="reset-job-filters" type="button">View all opportunities <span aria-hidden="true">↗</span></button>@endif
            <p class="jobs-note">Don’t see the right role yet? Send your CV to <a href="mailto:hr@hsb.co.id">hr@hsb.co.id</a>.<br>We’re always keen to hear from talented people.</p>
        </div></section>

        <section class="talent section-black" id="talent-pool" aria-labelledby="talent-title"><div class="talent-box"><h2 class="section-title" id="talent-title">DON’T SEE <span>YOUR ROLE</span> YET?</h2><p>We’re growing fast and new roles open regularly.<br>Leave your details and we’ll reach out when something that fits comes up.</p><div class="talent-form" aria-label="Talent pool form preview"><input type="text" placeholder="Full Name" aria-label="Full Name" disabled><input type="email" placeholder="Email Address" aria-label="Email Address" disabled><input type="text" placeholder="Area of interest" aria-label="Area of interest" disabled><div class="upload-placeholder">Upload CV <span>Browse</span></div></div><span class="button button-lime talent-button" aria-disabled="true">Join Our Talent Pool <span aria-hidden="true">↗</span></span><p class="talent-hint">Talent Pool submissions open soon.</p></div></section>
    </main>

    @include('partials.footer')
</body>
</html>
