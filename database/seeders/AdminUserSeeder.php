<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('auth.initial_admin.email');
        $password = config('auth.initial_admin.password');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! is_string($password) || blank($password)) {
            throw new RuntimeException('Set a valid INITIAL_ADMIN_EMAIL and INITIAL_ADMIN_PASSWORD before seeding the database.');
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            ['name' => 'HR Admin', 'password' => Hash::make($password)],
        );
    }
}
