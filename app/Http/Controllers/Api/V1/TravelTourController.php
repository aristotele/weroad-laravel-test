<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TravelTourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($travelSlug)
    {
        $travelTours = Tour::query()
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

        $travelTours = $travelTours->paginate();

        return response()->json(
            $travelTours,
            Response::HTTP_OK
        );
    }
}
