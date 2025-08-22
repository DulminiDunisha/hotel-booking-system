<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\EmergencyCase;
use App\Services\EmergencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EmergencyController extends Controller
{
    protected $emergencyService;

    public function __construct(EmergencyService $emergencyService)
    {
        $this->emergencyService = $emergencyService;
    }

    public function index()
    {
        $emergencyCases = EmergencyCase::with(['booking.guest', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statistics = $this->emergencyService->getEmergencyStatistics();

        return Inertia::render('Admin/EmergencyCases', [
            'emergencyCases' => $emergencyCases,
            'statistics' => $statistics,
        ]);
    }

    public function createEmergencyCancellation(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'required|string|max:500',
            'phone_call' => 'boolean',
        ]);

        try {
            $booking = Booking::findOrFail($request->booking_id);
            
            $emergencyCase = $this->emergencyService->processEmergencyCancellation(
                $booking,
                $request->reason,
                $request->phone_call ?? false
            );

            return response()->json([
                'success' => true,
                'emergency_case' => $emergencyCase,
                'message' => 'Emergency cancellation processed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Emergency cancellation creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Emergency cancellation processing failed'
            ], 500);
        }
    }

    public function createIllnessEmergency(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'illness_description' => 'required|string|max:500',
            'requires_early_checkout' => 'boolean',
        ]);

        try {
            $booking = Booking::findOrFail($request->booking_id);
            
            $emergencyCase = $this->emergencyService->processIllnessEmergency(
                $booking,
                $request->illness_description,
                $request->requires_early_checkout ?? false
            );

            return response()->json([
                'success' => true,
                'emergency_case' => $emergencyCase,
                'message' => 'Illness emergency processed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Illness emergency creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Illness emergency processing failed'
            ], 500);
        }
    }

    public function resolveEmergencyCase(Request $request, $id)
    {
        $request->validate([
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $emergencyCase = EmergencyCase::findOrFail($id);
            
            $resolvedCase = $this->emergencyService->resolveEmergencyCase(
                $emergencyCase,
                $request->resolution_notes
            );

            return response()->json([
                'success' => true,
                'emergency_case' => $resolvedCase,
                'message' => 'Emergency case resolved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Emergency case resolution failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Emergency case resolution failed'
            ], 500);
        }
    }

    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
        ]);

        try {
            $emergencyCase = EmergencyCase::findOrFail($id);
            $booking = $emergencyCase->booking;
            
            $result = $this->emergencyService->processRefund($booking, $request->refund_amount);
            
            if ($result) {
                $emergencyCase->update([
                    'refund_status' => EmergencyCase::REFUND_STATUS_COMPLETED,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Refund processed successfully',
                ]);
            } else {
                return response()->json([
                    'error' => 'Refund processing failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Refund processing failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Refund processing failed'
            ], 500);
        }
    }

    public function show($id)
    {
        $emergencyCase = EmergencyCase::with(['booking.guest', 'booking.room', 'booking.payment'])
            ->findOrFail($id);

        return Inertia::render('Admin/EmergencyCaseDetail', [
            'emergencyCase' => $emergencyCase,
        ]);
    }

    public function getEmergencyStatistics()
    {
        try {
            $statistics = $this->emergencyService->getEmergencyStatistics();
            
            return response()->json([
                'success' => true,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            Log::error('Emergency statistics retrieval failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Statistics retrieval failed'
            ], 500);
        }
    }

    public function guestEmergencyForm()
    {
        return Inertia::render('GuestEmergencyForm');
    }

    public function submitGuestEmergency(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string',
            'emergency_type' => 'required|in:illness,cancellation,early_checkout',
            'description' => 'required|string|max:500',
            'guest_name' => 'required|string',
            'guest_phone' => 'required|string',
            'guest_email' => 'required|email',
        ]);

        try {
            $booking = Booking::where('booking_code', $request->booking_code)->first();
            
            if (!$booking) {
                return response()->json([
                    'error' => 'Booking not found'
                ], 404);
            }

            // Verify guest details
            if ($booking->guest->email !== $request->guest_email) {
                return response()->json([
                    'error' => 'Guest details do not match'
                ], 400);
            }

            $emergencyCase = $this->emergencyService->createEmergencyCase(
                $booking,
                $request->emergency_type,
                $request->description,
                [
                    'guest_name' => $request->guest_name,
                    'guest_phone' => $request->guest_phone,
                    'guest_email' => $request->guest_email,
                    'submitted_at' => now()->toISOString(),
                ]
            );

            return response()->json([
                'success' => true,
                'emergency_case' => $emergencyCase,
                'message' => 'Emergency case submitted successfully. Our team will contact you shortly.',
            ]);

        } catch (\Exception $e) {
            Log::error('Guest emergency submission failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Emergency case submission failed'
            ], 500);
        }
    }
}
