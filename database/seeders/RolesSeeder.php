<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'org_admin',
            'club_admin',
            'club_manager',
            'coach',
            'team_manager',
            'guardian',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}
