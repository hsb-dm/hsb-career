<?php

use App\Filament\Resources\TalentPool\Pages\ListTalentPoolEntries;
use App\Filament\Resources\TalentPool\TalentPoolResource;
use App\Models\TalentPoolEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

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

test('talent pool stores CVs on S3 when configured', function () {
    config()->set('filesystems.cv_disk', 's3');
    Storage::fake('local');
    Storage::fake('s3');

    $this->post(route('talent-pool.store'), [
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
    ])->assertRedirect(route('home').'#talent-pool');

    $entry = TalentPoolEntry::query()->sole();
    Storage::disk('s3')->assertExists($entry->cv_path);
    Storage::disk('local')->assertMissing($entry->cv_path);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.talent-pool.cv.preview', $entry))->assertOk();
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

test('talent pool rejects oversized CV with a clear message', function () {
    Storage::fake('local');

    $this->followingRedirects()->post(route('talent-pool.store'), [
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv' => UploadedFile::fake()->create('large.pdf', 6000, 'application/pdf'),
    ])
        ->assertSee('CV file must be no larger than 5 MB.')
        ->assertSee('aria-describedby="talent-cv-client-error talent-cv-error"', false);

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

test('HR can set either talent pool status', function () {
    Storage::fake('local');
    $entry = TalentPoolEntry::create([
        'name' => 'Nadia Candidate',
        'email' => 'nadia@example.com',
        'area_of_interest' => 'Technology',
        'cv_path' => 'talent-pool-cvs/resume.pdf',
    ]);
    $this->actingAs(User::factory()->create());

    livewire(ListTalentPoolEntries::class)
        ->call('updateTableColumnState', 'status', (string) $entry->id, 'contacted')
        ->assertHasNoErrors();

    expect($entry->fresh()->status)->toBe('contacted')
        ->and(array_values(TalentPoolEntry::STATUSES))->toBe(['No suitable opportunities', 'Contacted']);
});
