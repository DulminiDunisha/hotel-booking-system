<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomApiController extends Controller
{
    /**
     * Get all rooms with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::with(['seasonalRates', 'availabilities']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $rooms = $query->paginate($perPage);

        return RoomResource::collection($rooms)->response();
    }

    /**
     * Get a specific room
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json(new RoomResource($room->load(['seasonalRates', 'availabilities', 'bookings'])));
    }

    /**
     * Get room statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'renovation_rooms' => Room::where('status', 'renovation')->count(),
            'average_price' => Room::avg('base_price'),
            'room_types' => Room::selectRaw('type, COUNT(*) as count')->groupBy('type')->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get room types
     */
    public function getRoomTypes(): JsonResponse
    {
        $types = Room::distinct()->pluck('type');
        return response()->json($types);
    }

    /**
     * Get available rooms for a date range
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $checkIn = $request->check_in_date;
        $checkOut = $request->check_out_date;

        // Get rooms that are available and not blocked for the selected dates
        $availableRooms = Room::where('status', 'available')
            ->whereDoesntHave('availabilities', function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('date', [$checkIn, $checkOut])
                      ->where('status', 'blocked');
            })
            ->get();

        return RoomResource::collection($availableRooms)->response();
    }
}
