<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\Tour;
use App\Models\User;
use App\Models\Travel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

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
        ]);

        $admin->roles()->attach($adminRole);

        foreach (range(1, 10) as $i) {
            $travel = Travel::factory(['numberOfDays' => rand(5, 9)])->create();
            $travelTours = Tour::factory()
                ->for($travel)
                ->count(4)
                ->state(
                    new Sequence(
                        ['price' => rand(500, 3000) * 100, 'startingDate' => now()->addDays(10), 'endingDate' => now()->addDays(10 + $travel->numberOfDays)],
                        ['price' => rand(500, 3000) * 100, 'startingDate' => now()->addDays(20), 'endingDate' => now()->addDays(20 + $travel->numberOfDays)],
                        ['price' => rand(500, 3000) * 100, 'startingDate' => now()->addDays(30), 'endingDate' => now()->addDays(30 + $travel->numberOfDays)],
                        ['price' => rand(500, 3000) * 100, 'startingDate' => now()->addDays(40), 'endingDate' => now()->addDays(40 + $travel->numberOfDays)],
                    )
                )
                ->create();
        }
    }
}
