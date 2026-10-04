<?php

use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage shows active jobs and hides inactive jobs', function () {
    Job::factory()->create([
        'title' => 'Active Design Role',
        'status' => JobStatus::Active,
    ]);

    Job::factory()->create([
        'title' => 'Inactive Finance Role',
        'status' => JobStatus::Inactive,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('BUILD YOUR')
        ->assertSee('Active Design Role')
        ->assertDontSee('Inactive Finance Role');
});
