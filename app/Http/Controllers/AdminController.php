<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Payment;
use App\Models\EmergencyCase;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get current date
        $today = Carbon::today();
        
        // Calculate occupancy rate
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('status', 'checked_in')
                                ->where('check_in_date', '<=', $today)
                                ->where('check_out_date', '>=', $today)
                                ->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Calculate revenue
        $currentMonthRevenue = Payment::where('status', 'completed')
                                     ->whereMonth('created_at', $today->month)
                                     ->whereYear('created_at', $today->year)
                                     ->sum('amount');

        // Get booking statistics
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $checkedInBookings = Booking::where('status', 'checked_in')->count();
        $checkedOutBookings = Booking::where('status', 'checked_out')->count();

        // Calculate average room rate
        $averageRoomRate = Room::avg('base_price');

        // Get upcoming check-ins
        $upcomingCheckins = Booking::with(['guest', 'room'])
                                  ->where('status', 'confirmed')
                                  ->where('check_in_date', '>=', $today)
                                  ->where('check_in_date', '<=', $today->addDays(7))
                                  ->orderBy('check_in_date')
                                  ->limit(6)
                                  ->get()
                                  ->map(function ($booking) {
                                      return [
                                          'name' => $booking->guest->name,
                                          'room' => $booking->room->number,
                                          'time' => '2:00 PM',
                                          'date' => $booking->check_in_date->format('M j'),
                                          'bookingId' => $booking->booking_code,
                                          'guests' => $booking->adults + $booking->children
                                      ];
                                  });

        // Get upcoming check-outs
        $upcomingCheckouts = Booking::with(['guest', 'room'])
                                   ->where('status', 'checked_in')
                                   ->where('check_out_date', '>=', $today)
                                   ->where('check_out_date', '<=', $today->addDays(7))
                                   ->orderBy('check_out_date')
                                   ->limit(5)
                                   ->get()
                                   ->map(function ($booking) {
                                       $nights = $booking->check_in_date->diffInDays($booking->check_out_date);
                                       return [
                                           'name' => $booking->guest->name,
                                           'room' => $booking->room->number,
                                           'time' => '11:00 AM',
                                           'date' => $booking->check_out_date->format('M j'),
                                           'bookingId' => $booking->booking_code,
                                           'nights' => $nights
                                       ];
                                   });

        // Get recent notifications
        $notifications = collect([
            [
                'type' => 'new',
                'bgClass' => 'bg-green-500',
                'title' => 'New Booking',
                'description' => 'New booking received for Room 201',
                'time' => '10 minutes ago'
            ],
            [
                'type' => 'emergency',
                'bgClass' => 'bg-orange-500',
                'title' => 'Emergency Request',
                'description' => 'Room 305 requests immediate assistance',
                'time' => '25 minutes ago'
            ],
            [
                'type' => 'cancellation',
                'bgClass' => 'bg-red-500',
                'title' => 'Booking Cancellation',
                'description' => 'Booking #BK123456 has been cancelled',
                'time' => '1 hour ago'
            ]
        ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'occupancy_rate' => $occupancyRate,
                'revenue' => $currentMonthRevenue,
                'total_bookings' => $totalBookings,
                'confirmed_bookings' => $confirmedBookings,
                'pending_bookings' => $pendingBookings,
                'checked_in_bookings' => $checkedInBookings,
                'checked_out_bookings' => $checkedOutBookings,
                'average_room_rate' => round($averageRoomRate, 2),
            ],
            'upcomingCheckins' => $upcomingCheckins,
            'upcomingCheckouts' => $upcomingCheckouts,
            'notifications' => $notifications,
        ]);
    }
}
