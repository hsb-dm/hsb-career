<?php

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Filament\Resources\Jobs\Pages\CreateJob;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('guest cannot open admin dashboard', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('admin can open dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dashboard');
});

test('admin can view jobs list', function () {
    Job::factory()->create(['title' => 'Backend Engineer']);

    $this->actingAs(User::factory()->create())
        ->get('/admin/jobs')
        ->assertOk()
        ->assertSee('Backend Engineer');
});

test('admin can create job', function () {
    $this->actingAs(User::factory()->create());

    livewire(CreateJob::class)
        ->fillForm([
            'title' => 'Frontend Engineer',
            'slug' => 'frontend-engineer',
            'department' => 'Engineering',
            'location' => 'Jakarta',
            'employment_type' => EmploymentType::FullTime->value,
            'work_arrangement' => WorkArrangement::Hybrid->value,
            'status' => true,
            'description' => '<p>Build and maintain frontend experiences.</p>',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'closed_at' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Job::class, [
        'title' => 'Frontend Engineer',
        'slug' => 'frontend-engineer',
        'status' => JobStatus::Active->value,
    ]);
});

test('admin can view applicants list', function () {
    $job = Job::factory()->create(['title' => 'QA Engineer']);
    Applicant::factory()->create([
        'job_id' => $job->id,
        'name' => 'Ari Candidate',
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/applicants')
        ->assertOk()
        ->assertSee('Ari Candidate');
});

test('admin cannot open applicant create page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/applicants/create')
        ->assertNotFound();
});

test('applicant belongs to job', function () {
    $job = Job::factory()->create();
    $applicant = Applicant::factory()->create(['job_id' => $job->id]);

    expect($applicant->job)->toBeInstanceOf(Job::class)
        ->and($job->applicants()->whereKey($applicant)->exists())->toBeTrue();
});

test('salary is stored as integer', function () {
    $applicant = Applicant::factory()->create([
        'current_salary' => 8000000,
        'expected_salary' => 10000000,
    ]);

    expect($applicant->refresh()->current_salary)->toBeInt()
        ->and($applicant->expected_salary)->toBeInt();
});

test('database seeder runs without errors', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Job::query()->count())->toBeGreaterThanOrEqual(10)
        ->and(Applicant::query()->count())->toBeGreaterThanOrEqual(100)
        ->and(User::query()->where('email', 'admin@example.com')->exists())->toBeTrue();
});

test('missing dummy cv path does not break applicant detail page', function () {
    $applicant = Applicant::factory()->create([
        'cv_path' => 'cvs/missing-file.pdf',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertOk()
        ->assertSee('File not found');
});
