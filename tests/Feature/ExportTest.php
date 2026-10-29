<?php

use App\Enums\ApplicantStatus;
use App\Filament\Resources\Applicants\Pages\ListApplicants;
use App\Filament\Resources\TalentPool\Pages\ListTalentPoolEntries;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\TalentPoolEntry;
use App\Models\User;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);
beforeEach(fn () => Storage::fake('local'));

function exportDownloadUrl(Export $export, string $format): string
{
    return URL::signedRoute('filament.exports.download', ['authGuard' => 'web', 'export' => $export, 'format' => $format], absolute: false);
}

function downloadExport(Export $export, string $format): string
{
    return test()->actingAs($export->user)
        ->get(exportDownloadUrl($export, $format))
        ->assertOk()
        ->streamedContent();
}

test('admins can export applicants to csv and xlsx without health data by default', function () {
    $admin = User::factory()->create();
    $job = Job::factory()->create(['title' => 'QA Engineer']);
    Applicant::factory()->for($job)->create([
        'name' => 'Ari Candidate',
        'status' => ApplicantStatus::HrInterview,
        'serious_disease' => true,
        'serious_disease_details' => 'Private medical detail',
    ]);
    $this->actingAs($admin);

    livewire(ListApplicants::class)
        ->callTableAction('export')
        ->assertHasNoTableActionErrors()
        ->assertNotified('Export completed');

    $export = Export::query()->sole();
    $csv = downloadExport($export, 'csv');

    expect($export->user_id)->toBe($admin->id)
        ->and($export->file_disk)->toBe('local')
        ->and($export->successful_rows)->toBe(1)
        ->and($csv)->toContain('Ari Candidate', 'QA Engineer', 'HR Interview')
        ->and($csv)->not->toContain('Private medical detail', 'Diagnosed with Serious Disease');

    expect(downloadExport($export, 'xlsx'))->toStartWith('PK');
});

test('applicant exports neutralize spreadsheet formulas', function () {
    $this->actingAs(User::factory()->create());
    Applicant::factory()->create(['motivation' => '=HYPERLINK("https://evil.example","x")']);

    livewire(ListApplicants::class)->callTableAction('export');

    expect(downloadExport(Export::query()->sole(), 'csv'))
        ->toContain("'=HYPERLINK")
        ->not->toContain(',"=HYPERLINK');
});

test('admins can export the talent pool', function () {
    $this->actingAs(User::factory()->create());
    TalentPoolEntry::query()->create([
        'name' => 'Sari Talent',
        'email' => 'sari@example.com',
        'area_of_interest' => 'Finance',
        'cv_path' => 'talent-pool-cvs/cv.pdf',
        'status' => 'contacted',
    ]);

    livewire(ListTalentPoolEntries::class)
        ->callTableAction('export')
        ->assertHasNoTableActionErrors();

    expect(downloadExport(Export::query()->sole(), 'csv'))
        ->toContain('Sari Talent', 'sari@example.com', 'Finance', 'Contacted');
});

test('only the admin who created an export can download it', function () {
    $this->actingAs(User::factory()->create());
    Applicant::factory()->create();
    livewire(ListApplicants::class)->callTableAction('export');
    $url = exportDownloadUrl(Export::query()->sole(), 'csv');

    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();

    auth()->logout();
    $this->get($url)->assertUnauthorized();
});
