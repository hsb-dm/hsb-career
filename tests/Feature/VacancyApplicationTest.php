<?php

use App\Enums\JobStatus;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
beforeEach(fn () => Storage::fake('local'));

function applicationAnswers(): array
{
    return [
        'name' => 'Ari Candidate',
        'email' => 'ari@example.com',
        'phone' => '+62 812-3456-7890',
        'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        'current_age' => 27,
        'marital_status' => 'Single',
        'current_status' => 'Still Working / employed',
        'current_domicile' => 'Jakarta Area',
        'english_fluency' => 'Professional',
        'foreign_language_fluency' => 'Mandarin',
        'current_salary_answer' => 'Rp 8.000.000',
        'additional_benefits' => 'Health insurance',
        'expected_salary_answer' => 'Rp 10.000.000 negotiable',
        'notice_period' => '1 month notice',
        'motivation' => 'I want to grow with HSB.',
        'reason_for_leaving' => 'Looking for a new challenge.',
        'latest_company_reference' => 'Budi, Manager, budi@example.com',
        'second_latest_company_reference' => 'Sari, Lead, sari@example.com',
        'serious_disease' => 'no',
    ];
}

test('apply page follows the screening form and submits to the selected job', function () {
    $job = Job::factory()->create(['title' => 'QA Engineer', 'slug' => 'qa-engineer', 'status' => JobStatus::Active]);

    $this->get(route('vacancies.show', $job->slug))
        ->assertOk()
        ->assertSee(route('vacancies.apply', $job->slug));

    $this->get(route('vacancies.apply', $job->slug))
        ->assertOk()
        ->assertSee('Application Screening Interview')
        ->assertSee('CV / Resume (PDF, max 5 MB)')
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('Reference Check Contact Details From Latest Company')
        ->assertDontSee('class="awards-strip"', false)
        ->assertDontSee('Applying for Job Position');

    $this->post(route('vacancies.apply.store', $job->slug), [
        ...applicationAnswers(),
        'job_id' => 9999,
        'status' => 'hired',
    ])->assertRedirect(route('vacancies.apply', $job->slug));

    $applicant = Applicant::query()->sole();
    expect($applicant->job_id)->toBe($job->id)
        ->and($applicant->status->value)->toBe('ai_ats_screened')
        ->and($applicant->email)->toBe('ari@example.com')
        ->and($applicant->phone)->toBe('+62 812-3456-7890')
        ->and($applicant->cv_path)->toStartWith('cvs/')
        ->and($applicant->current_salary_answer)->toBe('Rp 8.000.000')
        ->and($applicant->serious_disease)->toBeFalse();
    Storage::disk('local')->assertExists($applicant->cv_path);

    $this->get(route('vacancies.apply', $job->slug))->assertSee('Application submitted');
    $this->actingAs(User::factory()->create())
        ->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertOk()
        ->assertSee('Budi, Manager, budi@example.com')
        ->assertSee('Rp 10.000.000 negotiable');
    $this->get(route('admin.applicants.cv.preview', $applicant))->assertOk();
});

test('application validates required answers and other choices', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [])
        ->assertSessionHasErrors(['name', 'email', 'phone', 'cv', 'current_age', 'marital_status', 'latest_company_reference']);

    $this->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [
            ...applicationAnswers(),
            'marital_status' => 'Other',
            'serious_disease' => 'yes',
        ])->assertSessionHasErrors(['marital_status_other', 'serious_disease_details']);

    expect(Applicant::query()->count())->toBe(0);
});

test('application requires a PDF CV no larger than 5 MB', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    foreach ([
        null,
        UploadedFile::fake()->create('resume.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        UploadedFile::fake()->create('resume.pdf', 5121, 'application/pdf'),
    ] as $cv) {
        $answers = applicationAnswers();
        if ($cv === null) {
            unset($answers['cv']);
        } else {
            $answers['cv'] = $cv;
        }

        $this->post(route('vacancies.apply.store', $job->slug), $answers)
            ->assertSessionHasErrors('cv');
    }

    expect(Applicant::query()->count())->toBe(0);
    Storage::disk('local')->assertDirectoryEmpty('cvs');
});

test('application rejects invalid email and phone number', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [
            ...applicationAnswers(),
            'email' => 'invalid-email',
            'phone' => 'not-a-phone',
        ])->assertSessionHasErrors(['email', 'phone']);

    expect(Applicant::query()->count())->toBe(0);
});

test('application rejects invalid age and choice with clear field errors', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->followingRedirects()->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [
            ...applicationAnswers(),
            'current_age' => 0,
            'english_fluency' => 'Unknown level',
            'motivation' => str_repeat('A', 5001),
        ])
        ->assertSee('Current Age must be between 1 and 120.')
        ->assertSee('href="#current_age"', false)
        ->assertSee('aria-describedby="current_age-error"', false)
        ->assertSee('value="0"', false);

    expect(Applicant::query()->count())->toBe(0);
});

test('other answers are saved and salary calculations stay empty for free text amounts', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->post(route('vacancies.apply.store', $job->slug), [
        ...applicationAnswers(),
        'current_domicile' => 'Other',
        'current_domicile_other' => 'Bandung',
        'serious_disease' => 'yes',
        'serious_disease_details' => 'A past diagnosis.',
    ])->assertSessionHasNoErrors();

    $applicant = Applicant::query()->sole();
    expect($applicant->current_domicile)->toBe('Bandung')
        ->and($applicant->serious_disease)->toBeTrue()
        ->and($applicant->serious_disease_details)->toBe('A past diagnosis.')
        ->and($applicant->salaryIncrease())->toBeNull()
        ->and($applicant->salaryIncreasePercentage())->toBeNull();
});

test('inactive and unknown vacancies cannot receive applications', function () {
    $job = Job::factory()->create(['status' => JobStatus::Inactive]);

    $this->get(route('vacancies.apply', $job->slug))->assertNotFound();
    $this->post(route('vacancies.apply.store', $job->slug), applicationAnswers())->assertNotFound();
    $this->get(route('vacancies.apply', 'unknown-role'))->assertNotFound();
});
