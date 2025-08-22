<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with(['seasonalRates', 'availabilities'])->get();
        return RoomResource::collection($rooms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/RoomInventory');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number',
            'name' => 'required|string|max:100',
            'type' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Normalize status to lowercase
        $data['status'] = strtolower($data['status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room-images', 'public');
            $data['image'] = $imagePath;
        }

        $room = Room::create($data);
        return response()->json($room->load(['seasonalRates', 'availabilities']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return $room->load(['seasonalRates', 'availabilities', 'bookings']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $room = Room::findOrFail($id);
        return Inertia::render('Admin/RoomInventory', ['room' => $room]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        // Validate the data
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number,' . $room->id,
            'name' => 'required|string|max:100',
            'type' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Normalize status to lowercase
        $validated['status'] = strtolower($validated['status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image && Storage::disk('public')->exists($room->image)) {
                Storage::disk('public')->delete($room->image);
            }
            
            $imagePath = $request->file('image')->store('room-images', 'public');
            $validated['image'] = $imagePath;
        }

        $room->update($validated);
        return response()->json($room->load(['seasonalRates', 'availabilities']));
    }

    /**
     * Update room with FormData (for file uploads)
     */
    public function updateWithFormData(Request $request, Room $room)
    {
        // Validate the data
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number,' . $room->id,
            'name' => 'required|string|max:100',
            'type' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Normalize status to lowercase
        $validated['status'] = strtolower($validated['status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image && Storage::disk('public')->exists($room->image)) {
                Storage::disk('public')->delete($room->image);
            }
            
            $imagePath = $request->file('image')->store('room-images', 'public');
            $validated['image'] = $imagePath;
        }

        $room->update($validated);
        return response()->json($room->load(['seasonalRates', 'availabilities']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Delete image if exists
        if ($room->image && Storage::disk('public')->exists($room->image)) {
            Storage::disk('public')->delete($room->image);
        }
        
        $room->delete();
        return response()->noContent();
    }

    /**
     * Display room listing page for guests
     */
    public function listing()
    {
        $rooms = Room::where('status', 'available')
                    ->with(['seasonalRates', 'availabilities'])
                    ->get();

        return Inertia::render('RoomListing', [
            'rooms' => RoomResource::collection($rooms)->toArray(request()),
            'roomTypes' => Room::distinct()->pluck('type'),
        ]);
    }

    /**
     * Display room details page
     */
    public function details($id)
    {
        $room = Room::with(['seasonalRates', 'availabilities', 'bookings'])->findOrFail($id);
        
        return Inertia::render('RoomDetails', [
            'room' => (new RoomResource($room))->toArray(request()),
            'relatedRooms' => RoomResource::collection(
                Room::where('type', $room->type)
                    ->where('id', '!=', $room->id)
                    ->where('status', 'available')
                    ->limit(3)
                    ->get()
            )->toArray(request()),
        ]);
    }
}
