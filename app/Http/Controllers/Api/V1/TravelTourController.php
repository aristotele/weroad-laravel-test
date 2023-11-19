<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResourceCollection;
use App\Models\Tour;
use Illuminate\Validation\Rule;
use Validator;

class TravelTourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($travelSlug)
    {
        // validate
        $validator = Validator::make(request()->all(), [
            'priceFrom' => ['integer'],
            'priceTo' => ['integer'],
            'dateFrom' => ['date'],
            'dateTo' => ['date'],
            'sortField' => Rule::in(['price', 'date']),
            'sortDirection' => Rule::in(['asc', 'desc']),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        // execute
        $travelTours = Tour::query()
            ->with('travel')
            ->whereRelation('travel', 'slug', $travelSlug);

        if (request()->has('priceFrom')) {
            $travelTours->where('price', '>=', request()->query('priceFrom'));
        }

        if (request()->has('priceTo')) {
            $travelTours->where('price', '<=', request()->query('priceTo'));
        }

        if (request()->has('dateFrom')) {
            $travelTours->where('startingDate', '>=', request()->query('dateFrom'));
        }

        if (request()->has('dateTo')) {
            $travelTours->where('startingDate', '<=', request()->query('dateTo'));
        }

        if (request()->has('sortField')) {
            $sortField = request()->query('sortField');
            $sortDirection = request()->query('sortDirection') ?? 'asc';

            $map = [
                'price' => 'price',
                'date' => 'startingDate',
            ];
            $dbField = $map[$sortField];

            $travelTours->orderBy($dbField, $sortDirection);
        }

        // apply date sorting if not already specified in the request
        if (request()->query('sortField') !== 'date') {
            logger(request()->query('sortField'));
            $travelTours->orderBy('startingDate', 'asc');
        }

        // return
        return new TourResourceCollection($travelTours->paginate());
    }
}
