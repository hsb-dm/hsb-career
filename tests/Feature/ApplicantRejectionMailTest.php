<?php

use App\Enums\ApplicantStatus;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Filament\Resources\Applicants\Pages\ListApplicants;
use App\Filament\Resources\Applicants\Pages\ViewApplicant;
use App\Mail\ApplicantRejected;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => Mail::fake());

test('rejection email is personal, formal, and names the vacancy', function () {
    config()->set('app.name', 'HSB HR Dashboard');
    $mail = new ApplicantRejected('Ari Candidate', 'QA Engineer');

    $mail->assertHasSubject('Update on Your Application for QA Engineer | HSB Investasi')
        ->assertSeeInHtml('Dear Ari Candidate,')
        ->assertSeeInHtml('QA Engineer')
        ->assertSeeInHtml('we will not be moving forward with your candidacy')
        ->assertSeeInText('HSB Investasi Recruitment Team')
        ->assertDontSeeInHtml('Dashboard')
        ->assertDontSeeInText('Dashboard');
});

test('changing status to rejected reveals a manual email action on the detail page', function () {
    $job = Job::factory()->create(['title' => 'QA Engineer']);
    $applicant = Applicant::factory()->create([
        'job_id' => $job->id,
        'name' => 'Ari Candidate',
        'email' => 'ari@example.com',
        'status' => ApplicantStatus::AiAtsScreened,
    ]);
    $this->actingAs(User::factory()->create());

    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->callAction('updateStatus', ['status' => ApplicantStatus::Rejected->value])
        ->assertHasNoErrors()
        ->assertActionVisible('sendRejectionEmail');

    Mail::assertNothingSent();
    expect($applicant->fresh()->rejection_email_sent_at)->toBeNull();

    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->callAction('sendRejectionEmail')
        ->assertHasNoErrors()
        ->assertActionHidden('sendRejectionEmail');

    Mail::assertSent(ApplicantRejected::class, fn (ApplicantRejected $mail) => $mail->hasTo('ari@example.com')
        && $mail->candidateName === 'Ari Candidate'
        && $mail->jobTitle === 'QA Engineer');
    expect($applicant->fresh()->rejection_email_sent_at)->not->toBeNull();
    $this->get(ApplicantResource::getUrl('view', ['record' => $applicant]))
        ->assertSee('Rejection email sent at')
        ->assertSee('WIB');

    $applicant->refresh()->update(['status' => ApplicantStatus::HrInterview]);
    $applicant->refresh()->update(['status' => ApplicantStatus::Rejected]);
    Mail::assertSentCount(1);
});

test('table and bulk status changes never send emails, and only eligible rows show the send action', function () {
    $first = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    $second = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    $withoutEmail = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened, 'email' => null]);
    $this->actingAs(User::factory()->create());

    livewire(ListApplicants::class)
        ->call('updateTableColumnState', 'status', (string) $first->id, ApplicantStatus::Rejected->value)
        ->assertHasNoErrors();

    livewire(ListApplicants::class)
        ->callTableBulkAction('updateStatus', [$first, $second, $withoutEmail], ['status' => ApplicantStatus::Rejected->value])
        ->assertHasNoErrors();

    Mail::assertNothingSent();
    expect($first->fresh()->rejection_email_sent_at)->toBeNull()
        ->and($second->fresh()->rejection_email_sent_at)->toBeNull()
        ->and($withoutEmail->fresh()->rejection_email_sent_at)->toBeNull();

    livewire(ListApplicants::class)
        ->assertTableActionVisible('sendRejectionEmail', $first)
        ->assertTableActionVisible('sendRejectionEmail', $second)
        ->assertTableActionHidden('sendRejectionEmail', $withoutEmail)
        ->callTableAction('sendRejectionEmail', $first)
        ->assertHasNoErrors()
        ->assertTableActionHidden('sendRejectionEmail', $first);

    Mail::assertSentCount(1);
    expect($first->fresh()->rejection_email_sent_at)->not->toBeNull();
});

test('HR can send a rejection email for an already rejected applicant who has not received one', function () {
    $applicant = Applicant::factory()->create(['status' => ApplicantStatus::Rejected]);
    $this->actingAs(User::factory()->create());

    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->assertActionVisible('sendRejectionEmail')
        ->callAction('sendRejectionEmail')
        ->assertHasNoErrors()
        ->assertActionHidden('sendRejectionEmail');

    Mail::assertSentCount(1);
    expect($applicant->fresh()->rejection_email_sent_at)->not->toBeNull();
});

test('a failed SMTP send leaves a retry available', function () {
    $applicant = Applicant::factory()->create(['status' => ApplicantStatus::AiAtsScreened]);
    $mailManager = Mail::getFacadeRoot()->manager;
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));

    $applicant->update(['status' => ApplicantStatus::Rejected]);

    expect($applicant->fresh()->status)->toBe(ApplicantStatus::Rejected)
        ->and($applicant->rejection_email_sent_at)->toBeNull();

    expect(fn () => $applicant->sendRejectionEmail())
        ->toThrow(RuntimeException::class, 'SMTP unavailable');

    expect($applicant->fresh()->rejection_email_sent_at)->toBeNull();

    Mail::swap($mailManager);
    Mail::fake();
    $this->actingAs(User::factory()->create());
    livewire(ViewApplicant::class, ['record' => $applicant->id])
        ->assertActionVisible('sendRejectionEmail')
        ->callAction('sendRejectionEmail')
        ->assertHasNoErrors();

    Mail::assertSentCount(1);
});
