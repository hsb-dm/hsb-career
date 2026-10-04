<?php

namespace Database\Factories;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currentSalary = fake()->boolean(85) ? fake()->numberBetween(4, 25) * 1000000 : null;
        $expectedSalary = $currentSalary
            ? $currentSalary + (fake()->numberBetween(1, 8) * 1000000)
            : fake()->numberBetween(6, 28) * 1000000;

        return [
            'job_id' => Job::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+62'.fake()->numerify('8##########'),
            'current_salary' => $currentSalary,
            'expected_salary' => $expectedSalary,
            'notice_period' => fake()->randomElement([
                'Available immediately',
                '1 week',
                '2 weeks',
                '1 month',
                '2 months',
                '3 months',
                '45 days',
                'Negotiable',
            ]),
            'cv_path' => fake()->boolean(70)
                ? 'cvs/sample-cv.pdf'
                : 'cvs/sample-cv-'.fake()->numberBetween(1, 100).'.pdf',
            'status' => fake()->randomElement(ApplicantStatus::cases()),
            'hr_note' => fake()->boolean(35) ? fake()->sentence(12) : null,
            'applied_at' => fake()->dateTimeBetween('-90 days', 'now'),
        ];
    }
}
