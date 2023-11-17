<?php

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Collection;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

test('it may have multiple tours associated in different period of time', function () {
    $travel = Travel::factory()->create();

    Tour::factory()
        ->count(3)
        ->create([
            'travelId' => $travel->id,
        ]);

    dd($travel->toArray(), $travel->tours->toArray());

    tap($travel->tours, function ($tours) {
        assertEquals(3, count($tours));
        assertInstanceOf(Collection::class, $tours);
        assertInstanceOf(Tour::class, $tours->first());
    });
});
