<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@example.com';
        $password = 'Password123!';

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Super Admin', 'password' => Hash::make($password)]
        );

        // Ensure password is hashed even if user existed
        if (!str_starts_with($user->password, '$2y$')) {
            $user->password = Hash::make($password);
            $user->save();
        }

        if (!$user->hasRole('org_admin')) {
            $user->assignRole('org_admin');
        }
    }
}
