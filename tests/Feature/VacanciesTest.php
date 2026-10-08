<?php

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('vacancies page shows active jobs with the shared filters', function () {
    Job::factory()->create(['title' => 'Open Product Role', 'status' => JobStatus::Active]);
    Job::factory()->create(['title' => 'Closed Finance Role', 'status' => JobStatus::Inactive]);

    $this->get('/vacancies')
        ->assertOk()
        ->assertSee('All Careers')
        ->assertSee('<a href="https://www.hsb.co.id/about" target="_blank" rel="noopener noreferrer">About Us</a>', false)
        ->assertSee('<a href="https://www.hsb.co.id/contact" target="_blank" rel="noopener noreferrer">Contact us</a>', false)
        ->assertSee('<a href="https://www.hsb.co.id/newsroom" target="_blank" rel="noopener noreferrer">Media Info</a>', false)
        ->assertSee('<a href="https://www.hsb.co.id/newsroom/awards" target="_blank" rel="noopener noreferrer">Awards</a>', false)
        ->assertSee('Awards')
        ->assertSee('All Job Types')
        ->assertSee('Open Product Role')
        ->assertSee(route('vacancies.show', 'open-product-role'))
        ->assertSeeInOrder(['id="talent-pool"', 'class="awards-strip"'], false)
        ->assertDontSee('Closed Finance Role');
});

test('vacancies page keeps search and job type query values in the filters', function () {
    $this->get('/vacancies?q=Product&type=full_time')
        ->assertOk()
        ->assertSee('name="q" type="search" placeholder="Search by keyword" value="Product"', false)
        ->assertSee('name="type" type="hidden" value="full_time"', false)
        ->assertSee('<span data-job-selection>Full Time</span>', false);
});

test('active vacancy has a detail page and inactive vacancy is hidden', function () {
    $active = Job::factory()->create([
        'title' => 'Legal & Compliance Manager',
        'status' => JobStatus::Active,
        'description' => '<h2>About the Role</h2><p>Lead our compliance team.</p><script>alert(1)</script>',
    ]);
    $inactive = Job::factory()->create(['status' => JobStatus::Inactive]);

    $this->get(route('vacancies.show', $active->slug))
        ->assertOk()
        ->assertSee('Date Posted')
        ->assertSee('About the Role')
        ->assertSee('class="vacancy-apply-card"', false)
        ->assertSee('class="vacancy-card-details"', false)
        ->assertSee($active->department)
        ->assertSee(str_replace(' ', '-', $active->employment_type->label()))
        ->assertSee($active->location)
        ->assertSee(route('vacancies.apply', $active->slug))
        ->assertDontSee('data-vacancy-read-more', false)
        ->assertDontSee('Read More')
        ->assertSee('Lead our compliance team.')
        ->assertDontSee('<script>', false);

    $this->get(route('vacancies.show', $inactive->slug))->assertNotFound();
    $this->get('/vacancies/unknown-role')->assertNotFound();
});

test('talent pool submission from vacancies returns to vacancies', function () {
    Storage::fake('local');

    $this->post(route('talent-pool.store'), [
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        'return_to' => 'vacancies.index',
    ])->assertRedirect(route('vacancies.index').'#talent-pool');

    $this->get(route('vacancies.index'))
        ->assertOk()
        ->assertSee("Thanks, we've got your details.", false);
});
