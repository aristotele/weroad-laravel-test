<?php

use App\Models\Tour;
use App\Models\Travel;

use function Pest\Laravel\getJson;

test('it returns a list of tours for the given travel', function () {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory()
        ->has(Tour::factory()->count(4))
        ->create();

    // and some unrelated Tours
    Tour::factory()->count(3)->create();

    // When hit the endpoint
    // Then only the related Tours are returned
    getJson(
        route('api.v1.travels.tours.index', ['travelSlug' => $travel->slug])
    )
        ->assertOk()
        ->assertJsonCount(4, 'data')
        ->assertJson([
            'data' => $travel->tours->toArray()
        ]);
});
