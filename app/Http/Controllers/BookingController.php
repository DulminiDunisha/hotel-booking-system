<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['guest', 'room'])->get();
        $guests = User::where('role', 'guest')->orWhere('role', 'user')->get();
        $rooms = Room::all();

        return Inertia::render('Admin/BookingManagement', [
            'bookings' => $bookings,
            'guests' => $guests,
            'rooms' => $rooms,
        ]);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'nights' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out',
            'payment_method' => 'required|string|max:100',
            'payment_status' => 'required|in:paid,pending,failed,refunded'
        ]);

        // Generate unique booking code
        $validated['booking_code'] = 'BK' . strtoupper(uniqid());

        $booking = Booking::create($validated);

        return redirect()->back()->with('success', 'Booking created successfully');
    }

    /**
     * Show booking confirmation page
     */
    public function showConfirmation(Request $request)
    {
        $booking = null;
        
        // If booking ID is provided in session or query, load it
        if ($request->session()->has('booking_id')) {
            $bookingId = $request->session()->get('booking_id');
            $booking = Booking::with(['guest', 'room'])->find($bookingId);
            $request->session()->forget('booking_id');
        }
        
        return Inertia::render('BookingConfirmation', [
            'booking' => $booking
        ]);
    }

    /**
     * Store a guest booking (creates user account and booking)
     */
    public function storeGuestBooking(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'special_requests' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Check if user already exists
            $user = User::where('email', $validated['guest_email'])->first();
            
            if (!$user) {
                // Create new user account
                $user = User::create([
                    'name' => $validated['guest_name'],
                    'email' => $validated['guest_email'],
                    'phone' => $validated['guest_phone'],
                    'password' => Hash::make(Str::random(12)), // Generate random password
                    'role' => 'guest',
                    'email_verified_at' => now(), // Auto-verify for guest bookings
                ]);
            }

            // Get room details for pricing
            $room = Room::findOrFail($validated['room_id']);
            
            // Calculate nights and total amount
            $checkIn = new \DateTime($validated['check_in_date']);
            $checkOut = new \DateTime($validated['check_out_date']);
            $nights = $checkIn->diff($checkOut)->days;
            $totalAmount = $room->base_price * $nights;

            // Create booking
            $booking = Booking::create([
                'booking_code' => 'BK' . strtoupper(uniqid()),
                'guest_id' => $user->id,
                'room_id' => $validated['room_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'nights' => $nights,
                'adults' => $validated['adults'],
                'children' => $validated['children'],
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'special_requests' => $validated['special_requests'],
                'emergency_contact' => $validated['emergency_contact'],
                'emergency_phone' => $validated['emergency_phone'],
            ]);

            DB::commit();

            // Store booking ID in session and redirect to confirmation page
            $request->session()->put('booking_id', $booking->id);
            return redirect()->route('room.booking.confirmation')
                ->with('success', 'Booking confirmed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create booking. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return $booking->load(['guest', 'room', 'events']);
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
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'nights' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out',
            'payment_method' => 'required|string|max:100',
            'payment_status' => 'required|in:paid,pending,failed,refunded'
        ]);

        $booking->update($validated);

        return redirect()->back()->with('success', 'Booking updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->back()->with('success', 'Booking deleted successfully');
    }
}
