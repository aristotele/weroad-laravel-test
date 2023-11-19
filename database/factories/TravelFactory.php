<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Travel>
 */
class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(10);
        $slug = str()->slug($name);

        return [
            'isPublic' => rand(0, 1),
            'slug' => $slug,
            'name' => $name,
            'description' => $this->faker->sentence(10),
            'numberOfDays' => rand(3, 12),
            'moods' => [
                'nature' => rand(1, 10) * 10,
                'relax' => rand(1, 10) * 10,
                'history' => rand(1, 10) * 10,
                'culture' => rand(1, 10) * 10,
                'party' => rand(1, 10) * 10,
            ],
        ];
    }

    public function public(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'isPublic' => true,
            ];
        });
    }

    public function private(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'isPublic' => false,
            ];
        });
    }
}
