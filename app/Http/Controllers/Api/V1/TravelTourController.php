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
            ->whereRelation('travel', 'slug', $travelSlug)
            ->paginate();

        return response()->json(
            $travelTours,
            Response::HTTP_OK
        );
    }
}
