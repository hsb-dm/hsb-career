<?php

use App\Enums\ApplicantStatus;
use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Filament\Resources\Applicants\Pages\ListApplicants;
use App\Filament\Resources\Applicants\Pages\ViewApplicant;
use App\Filament\Resources\Jobs\Pages\CreateJob;
use App\Filament\Widgets\RecruitmentStatsOverview;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\TalentPoolEntry;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('applicant status choices match the HR workflow', function () {
    expect(array_values(ApplicantStatus::options()))->toBe([
        'AI ATS Screened',
        'HR Interview',
        'Forwarded to User',
        'User Interview',
        'Hired',
        'Rejected',
        'On Hold',
        'No show',
        'Withdraw/Decline',
        'Study Case',
    ]);
});

test('guest cannot open admin dashboard', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('admin can open dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('A clear view of your hiring pipeline.')
        ->assertSee('Review applicants')
        ->assertSee('Manage jobs');
});

test('dashboard calculates applicant summary with one aggregate query', function () {
    Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    Applicant::factory()->create(['status' => ApplicantStatus::HrInterview]);
    Applicant::factory()->create(['status' => ApplicantStatus::Hired]);

    $summaryQueries = [];
    DB::listen(function ($query) use (&$summaryQueries): void {
        if (str_contains(strtolower($query->sql), 'sum(case when status')) {
            $summaryQueries[] = $query->sql;
        }
    });

    $this->actingAs(User::factory()->create());

    livewire(RecruitmentStatsOverview::class)
        ->assertSee('Total applicants')
        ->assertSee('HR Interview');

    expect($summaryQueries)->toHaveCount(1);
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

test('applicant detail hides HR notes while keeping stored notes intact', function () {
    $applicant = Applicant::factory()->create(['hr_note' => 'Private screening note']);

    $this->actingAs(User::factory()->create())
        ->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertOk()
        ->assertDontSee('Internal HR note')
        ->assertDontSee('Private screening note');

    expect($applicant->fresh()->hr_note)->toBe('Private screening note');
});

test('applicant without a CV has no upload prompt on the detail page', function () {
    $applicant = Applicant::factory()->create(['cv_path' => null]);

    $this->actingAs(User::factory()->create())
        ->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertOk()
        ->assertDontSee('CV file')
        ->assertDontSee('Drag & Drop your files');
});

test('HR can update each applicant status and filter the list by it', function () {
    $screened = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    $hired = Applicant::factory()->create(['status' => ApplicantStatus::Hired]);
    $this->actingAs(User::factory()->create());

    livewire(ListApplicants::class)
        ->filterTable('status', ApplicantStatus::Hired)
        ->assertCanSeeTableRecords([$hired])
        ->assertCanNotSeeTableRecords([$screened]);

    livewire(ListApplicants::class)
        ->call('updateTableColumnState', 'status', (string) $screened->id, ApplicantStatus::HrInterview->value)
        ->assertHasNoErrors();

    expect($screened->fresh()->status)->toBe(ApplicantStatus::HrInterview);
});

test('HR can change status from the applicant detail page', function () {
    $applicant = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    $this->actingAs(User::factory()->create());

    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->callAction('updateStatus', ['status' => ApplicantStatus::StudyCase->value])
        ->assertHasNoErrors();

    expect($applicant->fresh()->status)->toBe(ApplicantStatus::StudyCase);
});

test('view applicant opens the detail page in a new tab with CV and status actions', function () {
    Storage::fake('local');
    $path = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf')->store('cvs', 'local');
    $applicant = Applicant::factory()->create([
        'cv_path' => $path,
        'status' => ApplicantStatus::AiAtsScreened,
    ]);
    $this->actingAs(User::factory()->create());

    livewire(ListApplicants::class)
        ->assertTableActionHasUrl('view', ApplicantResource::getUrl('view', ['record' => $applicant]), $applicant)
        ->assertTableActionShouldOpenUrlInNewTab('view', $applicant);

    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->assertActionVisible('updateStatus')
        ->assertActionVisible('previewCv')
        ->assertActionVisible('downloadCv');
});

test('existing applicant statuses are migrated to the new workflow', function () {
    $statuses = ['new', 'reviewing', 'interview', 'accepted'];
    $applicants = collect($statuses)->map(function (string $status): Applicant {
        $applicant = Applicant::factory()->create();
        DB::table('applicants')->where('id', $applicant->id)->update(['status' => $status]);

        return $applicant;
    });

    $migration = require database_path('migrations/2026_10_05_000002_migrate_applicant_statuses.php');
    $migration->up();

    expect($applicants->map(fn (Applicant $applicant): ApplicantStatus => $applicant->fresh()->status)->all())->toBe([
        ApplicantStatus::AiAtsScreened,
        ApplicantStatus::HrInterview,
        ApplicantStatus::UserInterview,
        ApplicantStatus::Hired,
    ]);
});

test('applicants list eager loads jobs as the number of applicants grows', function () {
    foreach (range(1, 6) as $number) {
        Applicant::factory()->create([
            'job_id' => Job::factory()->create(['title' => "Job {$number}"])->id,
            'name' => "Candidate {$number}",
        ]);
    }

    $jobQueries = [];
    DB::listen(function ($query) use (&$jobQueries): void {
        if (str_contains(strtolower($query->sql), ' from "jobs"')) {
            $jobQueries[] = $query->sql;
        }
    });

    $this->actingAs(User::factory()->create())
        ->get('/admin/applicants')
        ->assertOk()
        ->assertSee('Candidate 1')
        ->assertSee('Candidate 6');

    expect(count($jobQueries))->toBeLessThanOrEqual(3);
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
    Storage::fake('local');
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Job::query()->count())->toBe(10)
        ->and(Applicant::query()->count())->toBe(50)
        ->and(TalentPoolEntry::query()->count())->toBe(5)
        ->and(Job::query()->orderByDesc('published_at')->pluck('title')->all())->toBe([
            'Legal & Compliance Manager',
            'IT Manager',
            'Product Manager',
            'Performance Marketing',
            'CRM & Growth',
            'Brand Marketing Specialist',
            'SEO & Media Partnership',
            'Brand Partnership',
            'Trading Operation',
            'Head Of FAT (Mandarin Speaker)',
        ])
        ->and(Job::query()->where('location', 'Jakarta')->count())->toBe(10)
        ->and(Job::query()->where('employment_type', EmploymentType::FullTime)->count())->toBe(10)
        ->and(Job::query()->where('status', JobStatus::Active)->count())->toBe(10)
        ->and(Job::query()->where('title', 'Legal & Compliance Manager')->firstOrFail()->description)->toContain('Key Responsibilities')
        ->and(Job::query()->where('title', 'Legal & Compliance Manager')->firstOrFail()->published_at->format('M d, Y'))->toBe('Sep 29, 2026')
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

test('applicant CV preview remains private and serves PDF files', function () {
    Storage::fake('local');
    $path = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf')->store('cvs', 'local');
    $applicant = Applicant::factory()->create(['cv_path' => $path]);

    $this->get(route('admin.applicants.cv.preview', $applicant))->assertRedirect('/login');
    $this->get(route('admin.applicants.cv.download', $applicant))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())
        ->get(route('admin.applicants.cv.preview', $applicant))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
    $this->get(route('admin.applicants.cv.download', $applicant))
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename='.basename($path));
});
