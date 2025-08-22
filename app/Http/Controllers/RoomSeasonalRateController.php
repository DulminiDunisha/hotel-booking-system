<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomSeasonalRate;
use Illuminate\Http\Request;

class RoomSeasonalRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Room $room)
    {
        return $room->seasonalRates;
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
            'seasonalRates' => 'required|array|min:1',
            'seasonalRates.*.name' => 'required|string|max:100',
            'seasonalRates.*.start_date' => 'required|date',
            'seasonalRates.*.end_date' => 'required|date|after_or_equal:seasonalRates.*.start_date',
            'seasonalRates.*.multiplier' => 'required|numeric|min:0.1|max:10',
        ]);

        // Remove all previous seasonal rates for this room
        $room->seasonalRates()->delete();

        // Create new seasonal rates
        $createdRates = [];
        foreach ($validated['seasonalRates'] as $rateData) {
            $createdRates[] = $room->seasonalRates()->create($rateData);
        }

        return response()->json($createdRates, 201);
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
    public function update(Request $request, Room $room, RoomSeasonalRate $seasonalRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'multiplier' => 'required|numeric|min:0.1|max:10',
        ]);

        $seasonalRate->update($validated);
        return response()->json($seasonalRate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room, RoomSeasonalRate $seasonalRate)
    {
        $seasonalRate->delete();
        return response()->noContent();
    }
}
