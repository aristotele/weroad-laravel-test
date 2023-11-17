<?php

use App\Models\Tour;
use App\Models\Travel;

use function PHPUnit\Framework\assertInstanceOf;

test('it belongs to a travel', function () {
    $travel = Travel::factory()->create();
    $tour = Tour::factory()->create(['travelId' => $travel->id]);

    assertInstanceOf(Travel::class, $tour->travel);
});
