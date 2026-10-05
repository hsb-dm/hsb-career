<?php

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('homepage shows active jobs and hides inactive jobs', function () {
    $active = Job::factory()->create([
        'title' => 'Active Design Role',
        'status' => JobStatus::Active,
    ]);

    Job::factory()->create([
        'title' => 'Inactive Finance Role',
        'status' => JobStatus::Inactive,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('BUILD YOUR')
        ->assertSee('Active Design Role')
        ->assertSee(route('vacancies.show', $active->slug))
        ->assertSee(route('vacancies.index'))
        ->assertDontSee('Inactive Finance Role');
});

test('homepage shows at most five jobs and only offers all vacancies when there are more', function () {
    Job::factory()->count(5)->create(['status' => JobStatus::Active]);

    $this->get('/')
        ->assertOk()
        ->assertSee('class="job-card"', false)
        ->assertDontSee('View all opportunities');

    Job::factory()->create(['title' => 'Newest Role', 'status' => JobStatus::Active]);

    $response = $this->get('/')->assertOk()->assertSee('View all opportunities');
    expect(substr_count($response->getContent(), 'class="job-card"'))->toBe(5);
});

test('homepage loads listing data in one small jobs query', function () {
    Job::factory()->count(6)->create(['status' => JobStatus::Active]);

    $jobQueries = [];
    DB::listen(function ($query) use (&$jobQueries): void {
        if (str_contains(strtolower($query->sql), ' from "jobs"')) {
            $jobQueries[] = strtolower($query->sql);
        }
    });

    $this->get('/')->assertOk();

    expect($jobQueries)->toHaveCount(1)
        ->and($jobQueries[0])->not->toContain('description');
});
