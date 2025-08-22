<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\RoomSeasonalRateResource;
use App\Http\Resources\RoomAvailabilityResource;

class RoomResource extends JsonResource
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
            'number' => $this->number,
            'name' => $this->name,
            'type' => $this->type,
            'base_price' => (float) $this->base_price,
            'capacity' => (int) $this->capacity,
            'status' => ucfirst($this->status), // Capitalize status for frontend
            'description' => $this->description,
            'amenities' => $this->amenities,
            'image' => $this->image ? Storage::url($this->image) : null,

            // Related data
            'seasonalRates' => RoomSeasonalRateResource::collection($this->whenLoaded('seasonalRates')),
            'availabilities' => RoomAvailabilityResource::collection($this->whenLoaded('availabilities')),
        ];
    }
}
