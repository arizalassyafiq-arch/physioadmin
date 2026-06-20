<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) env('ADMIN_NAME', 'Admin Fisioterapi'));
        $email = Str::lower(trim((string) env('ADMIN_EMAIL', 'admin@physio.com')));
        $password = (string) env('ADMIN_PASSWORD', '');

        if ($name === '') {
            throw new RuntimeException('ADMIN_NAME must not be empty.');
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException('ADMIN_EMAIL must be a valid email address.');
        }

        if (blank($password)) {
            throw new RuntimeException('ADMIN_PASSWORD must be set before seeding admin user.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => 'admin',
                'password' => Hash::make($password),
            ]
        );
    }
}
