<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelResourceCollection;
use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publicTravels = Travel::query()->public();

        return new TravelResourceCollection($publicTravels->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // authorize
        if ($request->user()->cannot('create', Travel::class)) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // validate
        $validated = $request->validate([
            'isPublic' => ['required', 'boolean'],
            'slug' => ['required', 'string', 'max:255', 'unique:travels,slug'], // could be suggested by front-end, so it can also be tweaked
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'numberOfDays' => ['required', 'integer'],
            'moods.nature' => ['required', 'integer'],
            'moods.relax' => ['required', 'integer'],
            'moods.history' => ['required', 'integer'],
            'moods.culture' => ['required', 'integer'],
            'moods.party' => ['required', 'integer'],
        ]);

        // save
        $tour = Travel::create($validated);

        // response
        return response()->json(
            new TravelResource($tour),
            Response::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Travel $travel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Travel $travel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Travel $travel)
    {
        //
    }
}
