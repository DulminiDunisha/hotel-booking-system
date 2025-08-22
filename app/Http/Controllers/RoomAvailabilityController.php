<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomAvailability;
use Illuminate\Http\Request;

class RoomAvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Room $room)
    {
        return $room->availabilities;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'availability' => 'required|array',
            'availability.*.date' => 'required|date',
            'availability.*.status' => 'required|string',
        ]);

        // Delete existing availabilities for this room
        $room->availabilities()->delete();

        // Insert new availabilities
        $createdAvailabilities = [];
        foreach ($validated['availability'] as $entry) {
            // Normalize status to lowercase
            $entry['status'] = strtolower($entry['status']);
            $createdAvailabilities[] = $room->availabilities()->create($entry);
        }

        return response()->json(['message' => 'Availability updated successfully.', 'data' => $createdAvailabilities]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room, RoomAvailability $availability)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|string',
        ]);

        // Normalize status to lowercase
        $validated['status'] = strtolower($validated['status']);

        $availability->update($validated);
        return response()->json($availability);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room, RoomAvailability $availability)
    {
        $availability->delete();
        return response()->noContent();
    }
}
