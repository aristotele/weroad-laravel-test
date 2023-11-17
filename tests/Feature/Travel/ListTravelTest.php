<?php

use App\Models\Travel;

use function Pest\Laravel\getJson;

test('it returns a list of public travels', function () {
    // Given some Tours both public and private
    $publicTravels = Travel::factory()->public()->count(2)->create();

    $privateTravels = Travel::factory()->private()->count(2)->create();

    // When hit the endpoint
    // Then a paginate list of only public Tours are returned
    getJson('/api/v1/travels')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJson([
            'data' => [
                $publicTravels[0]->toArray(),
                $publicTravels[1]->toArray(),
            ],
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    "id",
                    "isPublic",
                    "slug",
                    "name",
                    "description",
                    "numberOfDays",
                    "moods",
                    "created_at",
                    "updated_at",
                ]
            ]
        ]);
});
