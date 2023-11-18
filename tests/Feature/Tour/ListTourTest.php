<?php

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Sequence;

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


test('it can filters tour related to a travel based on price criteria', function ($queryString, $expectedCount, $matchingPrices) {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory()
        ->has(
            Tour::factory()
                ->count(4)
                ->state(new Sequence(
                    ['price' => 100000],
                    ['price' => 120000],
                    ['price' => 150000],
                    ['price' => 200000],
                ))
        )
        ->create();

    // When hit the endpoint
    // Then only the related Tours are returned
    getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )
        ->assertOk()
        ->assertJsonCount($expectedCount, 'data')
        ->assertJson([
            'data' => Travel::whereIn('price', $matchingPrices)->get()->toArray(),
        ]);
})->with([
    'priceFrom' => [
        'queryString' => [
            'priceFrom' => 150000,
        ],
        'expectedCount' => 2,
        'matchingPrices' => [150000, 200000]
    ],
    'priceTo' => [
        'queryString' => [
            'priceTo' => 150000,
        ],
        'expectedCount' => 3,
        'matchingPrices' => [100000, 120000, 150000]
    ],
    'priceFrom and priceTo' => [
        'queryString' => [
            'priceFrom' => 120000,
            'priceTo' => 150000,
        ],
        'expectedCount' => 2,
        'matchingPrices' => [120000, 150000]
    ],
]);

test('it can filters tour related to a travel based on date criteria', function ($queryString, $matchingStartingDates) {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory()
        ->has(
            Tour::factory()
                ->count(4)
                ->state(new Sequence(
                    ['startingDate' => '2023-11-26', 'endingDate' => '2023-12-03'],
                    ['startingDate' => '2024-01-18', 'endingDate' => '2024-01-22'],
                    ['startingDate' => '2024-01-28', 'endingDate' => '2024-01-04'],
                    ['startingDate' => '2024-02-10', 'endingDate' => '2024-02-17'],
                ))
        )
        ->create();

    // When hit the endpoint
    // Then only the related Tours are returned
    getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )
        ->assertOk()
        ->assertJsonCount(count($matchingStartingDates), 'data')
        ->assertJson([
            'data' => Travel::whereIn('startingDate', $matchingStartingDates)->get()->toArray(),
        ]);
})->with([
    'dateFrom' => [
        'queryString' => [
            'dateFrom' => '2024-01-01'
        ],
        'matchingStartingDates' => [
            '2024-01-18', '2024-01-28', '2024-02-10',
        ],
    ],
    'dateTo' => [
        'queryString' => [
            'dateTo' => '2023-12-01'
        ],
        'matchingStartingDates' => [
            '2023-11-26',
        ],
    ],
    'dateFrom and dateTo' => [
        'queryString' => [
            'dateFrom' => '2024-01-01',
            'dateTo' => '2024-01-31',
        ],
        'matchingStartingDates' => [
            '2024-01-18', '2024-01-28',
        ],
    ],
]);
