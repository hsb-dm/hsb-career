<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public pages and the admin panel cannot be framed by other origins', function (string $path) {
    $this->get($path)->assertHeader('X-Frame-Options', 'SAMEORIGIN');
})->with(['/', '/vacancies', '/admin/login']);

test('authenticated admin pages cannot be framed by other origins', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});
