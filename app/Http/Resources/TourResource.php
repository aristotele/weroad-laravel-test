<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'travel' => new TravelResource($this->whenLoaded('travel')),
            'name' => $this->name,
            'startingDate' => $this->startingDate,
            'endingDate' => $this->endingDate,
            'price' => (string) ($this->price / 100),
        ];
    }
}
