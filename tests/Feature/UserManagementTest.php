<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('admins can create users with a hashed password', function () {
    $this->actingAs(User::factory()->create());

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'New HR',
            'email' => 'new.hr@example.com',
            'password' => 'SecurePassword123!',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'new.hr@example.com')->firstOrFail();

    expect($user->name)->toBe('New HR')
        ->and($user->password)->not->toBe('SecurePassword123!')
        ->and(Hash::check('SecurePassword123!', $user->password))->toBeTrue();
});

test('admins can edit users without changing their password unless they enter a new one', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create(['password' => 'OriginalPassword123!']);
    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->id])
        ->assertFormSet(['password' => null])
        ->fillForm(['name' => 'Updated HR'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->name)->toBe('Updated HR')
        ->and(Hash::check('OriginalPassword123!', $user->fresh()->password))->toBeTrue();

    livewire(EditUser::class, ['record' => $user->id])
        ->fillForm(['password' => 'ReplacementPassword123!'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('ReplacementPassword123!', $user->fresh()->password))->toBeTrue();
});

test('users cannot be created with a duplicate email or without a password', function () {
    $existing = User::factory()->create();
    $this->actingAs($existing);

    livewire(CreateUser::class)
        ->fillForm(['name' => 'Another HR', 'email' => $existing->email])
        ->call('create')
        ->assertHasFormErrors(['email' => 'unique', 'password' => 'required']);
});

test('guest cannot open user management', function () {
    $this->get(UserResource::getUrl('index'))->assertRedirect();
});

test('initial admin is hidden and other users can be deleted', function () {
    config()->set('auth.initial_admin.email', 'initial@example.com');

    $initialAdmin = User::factory()->create(['email' => 'initial@example.com']);
    $user = User::factory()->create();
    $this->actingAs($initialAdmin);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords([$user])
        ->assertCanNotSeeTableRecords([$initialAdmin])
        ->callTableAction('delete', $user);

    expect($user->fresh())->toBeNull()
        ->and($initialAdmin->fresh())->not->toBeNull();

    $this->get(UserResource::getUrl('edit', ['record' => $initialAdmin]))->assertNotFound();
});
