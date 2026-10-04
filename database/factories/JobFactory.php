<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(JobStatus::cases());

        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Product', 'Marketing', 'Finance', 'Customer Experience', 'Human Resources']),
            'location' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Remote']),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'work_arrangement' => fake()->randomElement(WorkArrangement::cases()),
            'description' => '<p>'.fake()->paragraph().'</p><ul><li>'.fake()->sentence().'</li><li>'.fake()->sentence().'</li></ul>',
            'status' => $status,
            'published_at' => $status === JobStatus::Active ? fake()->dateTimeBetween('-120 days', '-5 days') : null,
            'closed_at' => $status === JobStatus::Inactive ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
