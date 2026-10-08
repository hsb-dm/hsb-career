<?php

use App\Enums\JobStatus;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public pages expose canonical and sharing metadata', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active, 'title' => 'SEO Specialist', 'published_at' => now()]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
        ->assertSee('<link rel="icon" type="image/svg+xml" href="'.asset('favicon.svg').'">', false)
        ->assertSee('<meta property="og:title" content="Careers at HSB Investasi">', false);

    $this->get(route('vacancies.index', ['q' => 'SEO']))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('vacancies.index').'">', false);

    $response = $this->get(route('vacancies.show', $job->slug))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('vacancies.show', $job->slug).'">', false);

    preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
    $posting = json_decode($matches[1] ?? '', true, flags: JSON_THROW_ON_ERROR);

    expect($posting['@type'])->toBe('JobPosting')
        ->and($posting['title'])->toBe('SEO Specialist')
        ->and($posting['datePosted'])->toBe($job->published_at->toIso8601String())
        ->and($posting['jobLocation']['address']['addressCountry'])->toBe('ID');
});

test('sitemap lists active jobs only and robots advertises it', function () {
    $active = Job::factory()->create(['status' => JobStatus::Active]);
    $inactive = Job::factory()->create(['status' => JobStatus::Inactive]);

    $response = $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('vacancies.show', $active->slug))
        ->assertDontSee(route('vacancies.show', $inactive->slug))
        ->assertDontSee('/apply');

    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();

    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: '.route('sitemap'));
});

test('an active job gets a publication date when first published', function () {
    $job = Job::factory()->create(['status' => JobStatus::Inactive, 'published_at' => null]);
    expect($job->published_at)->toBeNull();

    $job->update(['status' => JobStatus::Active]);

    expect($job->published_at)->not->toBeNull();
});

test('application and admin pages signal noindex', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->get(route('vacancies.apply', $job->slug))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, follow')
        ->assertSee('<meta name="robots" content="noindex, follow">', false);

    $this->get('/admin/login')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, follow');

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, follow');
});
