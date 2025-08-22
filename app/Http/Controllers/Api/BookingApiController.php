<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingApiController extends Controller
{
    /**
     * Get all bookings with pagination and filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['guest', 'room']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_type')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('type', $request->room_type);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($guestQuery) use ($search) {
                      $guestQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($roomQuery) use ($search) {
                      $roomQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_range')) {
            $dates = explode(',', $request->date_range);
            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('check_in_date', [$startDate, $endDate])
                      ->orWhereBetween('check_out_date', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->where('check_in_date', '<=', $startDate)
                                ->where('check_out_date', '>=', $endDate);
                      });
                });
            }
        }

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $bookings = $query->paginate($perPage);

        return response()->json($bookings);
    }

    /**
     * Get available rooms for a date range
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'exclude_booking_id' => 'nullable|integer'
        ]);

        $checkIn = $request->check_in_date;
        $checkOut = $request->check_out_date;
        $excludeId = $request->exclude_booking_id;

        // Get rooms that are already booked for the selected dates
        $bookedRoomIds = Booking::where(function ($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function ($subQuery) use ($checkIn, $checkOut) {
                      $subQuery->where('check_in_date', '<=', $checkIn)
                               ->where('check_out_date', '>=', $checkOut);
                  });
        })
        ->when($excludeId, function ($query) use ($excludeId) {
            $query->where('id', '!=', $excludeId);
        })
        ->pluck('room_id');

        // Get available rooms
        $availableRooms = Room::whereNotIn('id', $bookedRoomIds)
                              ->where('status', 'available')
                              ->get();

        return response()->json($availableRooms);
    }

    /**
     * Get all guests
     */
    public function getGuests(): JsonResponse
    {
        $guests = User::where('role', 'guest')->orWhere('role', 'user')->get();
        return response()->json($guests);
    }

    /**
     * Get all rooms
     */
    public function getRooms(): JsonResponse
    {
        $rooms = Room::all();
        return response()->json($rooms);
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
     * Get booking statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'checked_in_bookings' => Booking::where('status', 'checked_in')->count(),
            'checked_out_bookings' => Booking::where('status', 'checked_out')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => Booking::where('payment_status', 'pending')->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}
