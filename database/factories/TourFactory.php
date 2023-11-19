<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'travelId' => function () {
                return Travel::factory()->create()->id;
            },
            // 'name' => 'overridden-later',
            'name' => $this->faker->sentence(6),
            'startingDate' => now()->toDateString(),
            'endingDate' => now()->toDateString(),
            'price' => rand(500, 3000) * 100,
        ];
    }

    // public function configure(): static
    // {
    //     return $this->afterCreating(function (Tour $tour) {
    //         dd('ehi');
    //         // ensure that each dummy tour has the same numberOfDays specified in the travel
    //         $numberOfDays = $tour->travel->numberOfDays;

    //         $baseStartingRandomMonths = rand(1, 5);
    //         $baseStartingRandomDays = rand(1, 15);

    //         $startingDate = now()
    //             ->toImmutable()
    //             ->addMonth($baseStartingRandomMonths)
    //             ->addDays($baseStartingRandomDays);

    //         $endingDate = $startingDate->addDays($numberOfDays);

    //         $tour->update([
    //             'name' => 'ITJOR' . $startingDate->format('Ymd'),
    //             'startingDate' => $startingDate->toDateString(),
    //             'endingDate' => $endingDate->toDateString(),
    //         ]);
    //     });
    // }
}
