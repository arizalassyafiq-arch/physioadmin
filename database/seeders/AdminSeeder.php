<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (blank($password)) {
            throw new RuntimeException('ADMIN_PASSWORD must be set before seeding admin user.');
        }

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@physio.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin Fisioterapi'),
                'role' => 'admin',
                'password' => bcrypt($password),
            ]
        );
    }
}
