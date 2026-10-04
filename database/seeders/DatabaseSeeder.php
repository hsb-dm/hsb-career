<?php

namespace Database\Seeders;

use App\Enums\ApplicantStatus;
use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('local')->put(
            'cvs/sample-cv.pdf',
            file_get_contents(storage_path('app/private/cvs/sample-cv.pdf')) ?: ''
        );

        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'HR Admin',
            'password' => Hash::make('password'),
        ]);

        $jobs = collect([
            ['Frontend Engineer', 'Engineering', 'Jakarta', EmploymentType::FullTime, WorkArrangement::Hybrid, JobStatus::Active],
            ['Backend Engineer', 'Engineering', 'Remote', EmploymentType::FullTime, WorkArrangement::Remote, JobStatus::Active],
            ['QA Engineer', 'Engineering', 'Bandung', EmploymentType::Contract, WorkArrangement::Hybrid, JobStatus::Active],
            ['UI/UX Designer', 'Product', 'Jakarta', EmploymentType::FullTime, WorkArrangement::Hybrid, JobStatus::Inactive],
            ['Product Manager', 'Product', 'Jakarta', EmploymentType::FullTime, WorkArrangement::Onsite, JobStatus::Active],
            ['Digital Marketing Specialist', 'Marketing', 'Surabaya', EmploymentType::Contract, WorkArrangement::Onsite, JobStatus::Inactive],
            ['SEO Specialist', 'Marketing', 'Remote', EmploymentType::Freelance, WorkArrangement::Remote, JobStatus::Active],
            ['Finance Staff', 'Finance', 'Jakarta', EmploymentType::FullTime, WorkArrangement::Onsite, JobStatus::Inactive],
            ['Customer Support', 'Customer Experience', 'Bandung', EmploymentType::Contract, WorkArrangement::Hybrid, JobStatus::Active],
            ['Human Resources Officer', 'Human Resources', 'Jakarta', EmploymentType::FullTime, WorkArrangement::Onsite, JobStatus::Inactive],
        ])->map(fn (array $job) => Job::query()->create([
            'title' => $job[0],
            'department' => $job[1],
            'location' => $job[2],
            'employment_type' => $job[3],
            'work_arrangement' => $job[4],
            'description' => "<p>We are looking for a {$job[0]} to join our {$job[1]} team and help build reliable hiring outcomes for the company.</p><p>The role works closely with cross-functional teams and needs strong ownership, communication, and execution discipline.</p>",
            'status' => $job[5],
            'published_at' => $job[5] === JobStatus::Active ? now()->subDays(fake()->numberBetween(5, 80)) : null,
            'closed_at' => $job[5] === JobStatus::Inactive ? now()->subDays(fake()->numberBetween(1, 20)) : null,
        ]));

        foreach ($jobs as $index => $job) {
            Applicant::factory()
                ->count($index < 5 ? 12 : 8)
                ->create([
                    'job_id' => $job->id,
                    'status' => fake()->randomElement(ApplicantStatus::cases()),
                ]);
        }
    }
}
