<?php

use App\Enums\JobStatus;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function applicationAnswers(): array
{
    return [
        'name' => 'Ari Candidate',
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
        ->assertSee('Reference Check Contact Details From Latest Company')
        ->assertDontSee('Applying for Job Position');

    $this->post(route('vacancies.apply.store', $job->slug), [
        ...applicationAnswers(),
        'job_id' => 9999,
        'status' => 'accepted',
    ])->assertRedirect(route('vacancies.apply', $job->slug));

    $applicant = Applicant::query()->sole();
    expect($applicant->job_id)->toBe($job->id)
        ->and($applicant->status->value)->toBe('new')
        ->and($applicant->email)->toBeNull()
        ->and($applicant->current_salary_answer)->toBe('Rp 8.000.000')
        ->and($applicant->serious_disease)->toBeFalse();

    $this->get(route('vacancies.apply', $job->slug))->assertSee('Application submitted');
    $this->actingAs(User::factory()->create())
        ->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertOk()
        ->assertSee('Budi, Manager, budi@example.com')
        ->assertSee('Rp 10.000.000 negotiable');
});

test('application validates required answers and other choices', function () {
    $job = Job::factory()->create(['status' => JobStatus::Active]);

    $this->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [])
        ->assertSessionHasErrors(['name', 'current_age', 'marital_status', 'latest_company_reference']);

    $this->from(route('vacancies.apply', $job->slug))
        ->post(route('vacancies.apply.store', $job->slug), [
            ...applicationAnswers(),
            'marital_status' => 'Other',
            'serious_disease' => 'yes',
        ])->assertSessionHasErrors(['marital_status_other', 'serious_disease_details']);

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
