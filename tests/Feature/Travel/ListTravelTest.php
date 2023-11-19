<?php

use App\Models\Travel;

use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertEquals;

test('it returns a list of public travels', function () {
    // Given some Tours both public and private
    $publicTravels = Travel::factory()->public()->count(2)->create();

    $privateTravels = Travel::factory()->private()->count(2)->create();

    // When hit the endpoint
    // Then a paginate list of only public Tours are returned
    $response = getJson(
        route('api.v1.travels.index')
    )
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ])
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'name',
                    'description',
                    'numberOfDays',
                    'moods',
                ],
            ],
        ]);

    $data = $response->json('data');

    assertEquals(
        $publicTravels->pluck('id')->sort()->all(),
        collect($data)->pluck('id')->sort()->all(),
    );
});
