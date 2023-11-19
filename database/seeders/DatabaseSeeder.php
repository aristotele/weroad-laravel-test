<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::factory()->create(['name' => Role::ADMIN]);
        $editorRole = Role::factory()->create(['name' => Role::EDITOR]);

        $admin = User::factory()->create([
            'email' => 'test@mail.com',
        ])->create();

        $admin->roles()->attach($adminRole);
    }
}
