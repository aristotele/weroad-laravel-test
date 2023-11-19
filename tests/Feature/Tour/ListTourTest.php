<?php

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Sequence;

use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

test('it returns a list of tours for the given travel', function () {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory(['numberOfDays' => 5])->create();
    $travelTours = Tour::factory()
        ->for($travel)
        ->count(4)
        ->state(
            new Sequence(
                ['price' => 200000, 'startingDate' => now()->addDays(10), 'endingDate' => now()->addDays(10 + $travel->numberOfDays),],
                ['price' => 100000, 'startingDate' => now()->addDays(20), 'endingDate' => now()->addDays(20 + $travel->numberOfDays),],
                ['price' => 150000, 'startingDate' => now()->addDays(30), 'endingDate' => now()->addDays(30 + $travel->numberOfDays),],
                ['price' => 120000, 'startingDate' => now()->addDays(40), 'endingDate' => now()->addDays(40 + $travel->numberOfDays),],
            )
        )
        ->create();

    // and some unrelated Tours
    Tour::factory()->count(3)->create();

    // When hit the endpoint
    $response = getJson(
        route('api.v1.travels.tours.index', ['travelSlug' => $travel->slug])
    )
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    "id",
                    "travelId",
                    "name",
                    "startingDate",
                    "endingDate",
                    "price",
                    "created_at",
                    "updated_at",
                ]
            ]
        ]);

    $data = $response->json('data');

    // Then only the related Tours are returned
    assertEquals(
        $travelTours->pluck('id')->sort()->all(),
        collect($data)->pluck('id')->sort()->all(),
    );
});


test('it can filters tour related to a travel based on price criteria', function ($queryString, $matchingPrices) {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory()->create();

    $travelTours = Tour::factory()
        ->for($travel)
        ->count(4)
        ->state(
            new Sequence(
                ['price' => 200000, 'startingDate' => now()->addDays(10), 'endingDate' => now()->addDays(10 + $travel->numberOfDays),],
                ['price' => 100000, 'startingDate' => now()->addDays(20), 'endingDate' => now()->addDays(20 + $travel->numberOfDays),],
                ['price' => 150000, 'startingDate' => now()->addDays(30), 'endingDate' => now()->addDays(30 + $travel->numberOfDays),],
                ['price' => 120000, 'startingDate' => now()->addDays(40), 'endingDate' => now()->addDays(40 + $travel->numberOfDays),],
            )
        )
        ->create();

    // When hit the endpoint w/ some price criteria
    $response = getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )->assertOk();

    $data = $response->json('data');
    $expectedData = $travelTours->filter(fn ($tour) => in_array($tour->price, $matchingPrices));

    // Then only matching criteria Tours are returned
    assertEquals(
        $expectedData->pluck('id')->sort()->all(),
        collect($data)->pluck('id')->sort()->all(),
    );
})->with([
    'priceFrom' => [
        'queryString' => [
            'priceFrom' => 150000,
        ],
        'matchingPrices' => [150000, 200000]
    ],
    'priceTo' => [
        'queryString' => [
            'priceTo' => 150000,
        ],
        'matchingPrices' => [100000, 120000, 150000]
    ],
    'priceFrom and priceTo' => [
        'queryString' => [
            'priceFrom' => 120000,
            'priceTo' => 150000,
        ],
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
    $response = getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )->assertOk();

    $travelTours = $travel->tours;
    $data = $response->json('data');
    $expectedData = [];

    foreach ($matchingStartingDates as $date) {
        $expectedData[] = $travelTours->where('startingDate', $date)->first()->toArray();
    }

    // Then only the related Tours are returned
    assertCount(count($matchingStartingDates), $data);
    assertEquals($expectedData, $data);
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

test('it allows asc and desc sorting by price ', function ($queryString, $matchingPrices) {
    // Given a Travel w/ some Tours associated
    $travel = Travel::factory()
        ->has(
            Tour::factory()
                ->count(4)
                ->state(new Sequence(
                    ['price' => 200000],
                    ['price' => 100000],
                    ['price' => 150000],
                    ['price' => 120000],
                ))
        )
        ->create();

    // When hit the endpoint
    // Then only the related Tours are returned
    $response = getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )->assertOk();

    $data = $response->json('data');

    assertCount(count($matchingPrices), $data);
    assertEquals(
        [
            Tour::where('price', $matchingPrices[0])->first()->toArray(),
            Tour::where('price', $matchingPrices[1])->first()->toArray(),
            Tour::where('price', $matchingPrices[2])->first()->toArray(),
            Tour::where('price', $matchingPrices[3])->first()->toArray(),
        ],
        $data,
    );
})->with([
    'price asc' => [
        'queryString' => [
            'sortField' => 'price',
            'sortDirection' => 'asc',
        ],
        'matchingPrices' => [
            100000, 120000, 150000, 200000,
        ],
    ],
    'price desc' => [
        'queryString' => [
            'sortField' => 'price',
            'sortDirection' => 'desc',
        ],
        'matchingPrices' => [
            200000, 150000, 120000, 100000,
        ],
    ],
]);

test('valid parameters values must be passed', function ($queryString) {
    $travel = Travel::factory()->create();

    // When the API request has been made w/ invalid sortField values
    getJson(
        route('api.v1.travels.tours.index', [
            'travelSlug' => $travel->slug,
            ...$queryString,
        ])
    )
        // Then a 422 must be returned
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor(key($queryString));
})->with([
    'priceFrom invalid value' => [
        'queryString' => [
            'priceFrom' => 'not-an-integer',
        ],
    ],
    'priceTo invalid value' => [
        'queryString' => [
            'priceTo' => 'not-an-integer',
        ],
    ],
    'dateFrom invalid value' => [
        'queryString' => [
            'dateFrom' => 'not-a-date',
        ],
    ],
    'dateTo invalid value' => [
        'queryString' => [
            'dateTo' => 'not-a-date',
        ],
    ],
    'sortField invalid value' => [
        'queryString' => [
            'sortField' => 'invalid-field',
        ],
    ],
    'sortDirection invalid value' => [
        'queryString' => [
            'sortDirection' => 'invalid-direction',
        ],
    ],
]);
