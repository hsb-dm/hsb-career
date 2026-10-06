<?php

use App\Models\Applicant;
use App\Models\Job;
use App\Models\TalentPoolEntry;
use Database\Seeders\RecruitmentDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('demo seeder fills each job and talent pool to five without duplicating records', function () {
    Storage::fake('local');

    $firstJob = Job::factory()->create();
    $secondJob = Job::factory()->create();
    $existingApplicant = Applicant::factory()->for($firstJob)->create();

    $this->seed(RecruitmentDemoSeeder::class);
    $this->seed(RecruitmentDemoSeeder::class);

    expect($firstJob->applicants()->count())->toBe(5)
        ->and($secondJob->applicants()->count())->toBe(5)
        ->and(Applicant::query()->find($existingApplicant->id))->not->toBeNull()
        ->and(TalentPoolEntry::query()->count())->toBe(5);

    Storage::disk('local')->assertExists('talent-pool-cvs/demo-cv.pdf');
    expect(Storage::disk('local')->get('talent-pool-cvs/demo-cv.pdf'))->toStartWith('%PDF-1.4');
});
