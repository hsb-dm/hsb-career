<?php

use App\Filament\Resources\TalentPool\TalentPoolResource;
use App\Models\TalentPoolEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('visitor can join talent pool and see success state', function () {
    Storage::fake('local');

    $response = $this->post(route('talent-pool.store'), [
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('home').'#talent-pool');

    $entry = TalentPoolEntry::query()->firstOrFail();
    expect($entry->name)->toBe('Nadia Candidate')
        ->and($entry->area_of_interest)->toBe('Technology');
    Storage::disk('local')->assertExists($entry->cv_path);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('success-talentpool.png')
        ->assertSee("Thanks, we've got your details.", false);
});

test('invalid talent pool submission returns to form without saving', function () {
    Storage::fake('local');

    $this->post(route('talent-pool.store'), [
        'name' => 'Nadia Candidate',
        'email' => 'invalid',
        'area_of_interest' => 'Unknown',
        'cv' => UploadedFile::fake()->create('picture.jpg', 100, 'image/jpeg'),
    ])
        ->assertRedirect(route('home').'#talent-pool')
        ->assertSessionHasErrors(['email', 'area_of_interest', 'cv']);

    expect(TalentPoolEntry::query()->count())->toBe(0);
});

test('talent pool entries are visible in the admin menu and CV requires login', function () {
    Storage::fake('local');
    $path = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf')->store('talent-pool-cvs', 'local');
    $entry = TalentPoolEntry::create([
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv_path' => $path,
    ]);

    $this->get(route('admin.talent-pool.cv.preview', $entry))
        ->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(TalentPoolResource::getUrl('index'))
        ->assertOk()
        ->assertSee('Nadia Candidate');

    $this->get(TalentPoolResource::getUrl('view', ['record' => $entry]))
        ->assertOk()
        ->assertSee('Technology');
});
