<?php

namespace App\Services;

use App\Models\EmergencyCase;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmergencyService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function createEmergencyCase(Booking $booking, $type, $description, $emergencyDetails = [])
    {
        try {
            DB::beginTransaction();

            // Create emergency case
            $emergencyCase = EmergencyCase::create([
                'booking_id' => $booking->id,
                'type' => $type,
                'description' => $description,
                'status' => EmergencyCase::STATUS_OPEN,
                'emergency_details' => $emergencyDetails,
            ]);

            // Update booking status
            $booking->update([
                'status' => $this->getEmergencyBookingStatus($type),
            ]);

            // Calculate refund amount if applicable
            if (in_array($type, [EmergencyCase::TYPE_CANCELLATION, EmergencyCase::TYPE_EARLY_CHECKOUT])) {
                $refundAmount = $this->calculateRefundAmount($booking, $type);
                
                if ($refundAmount > 0) {
                    $emergencyCase->update([
                        'refund_amount' => $refundAmount,
                        'refund_status' => EmergencyCase::REFUND_STATUS_PENDING,
                    ]);
                }
            }

            // Send emergency notification
            $this->notificationService->sendEmergencyNotification($booking, $type, $description);

            DB::commit();

            return $emergencyCase;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Emergency case creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processEmergencyCancellation(Booking $booking, $reason, $phoneCall = false)
    {
        try {
            DB::beginTransaction();

            $emergencyDetails = [
                'reason' => $reason,
                'phone_call' => $phoneCall,
                'cancelled_at' => now()->toISOString(),
            ];

            // Create emergency case
            $emergencyCase = $this->createEmergencyCase(
                $booking,
                EmergencyCase::TYPE_CANCELLATION,
                $reason,
                $emergencyDetails
            );

            // Process refund
            $refundAmount = $this->calculateRefundAmount($booking, EmergencyCase::TYPE_CANCELLATION);
            
            if ($refundAmount > 0) {
                $this->processRefund($booking, $refundAmount);
            }

            DB::commit();

            return $emergencyCase;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Emergency cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processIllnessEmergency(Booking $booking, $illnessDescription, $requiresEarlyCheckout = false)
    {
        try {
            DB::beginTransaction();

            $emergencyDetails = [
                'illness_description' => $illnessDescription,
                'requires_early_checkout' => $requiresEarlyCheckout,
                'reported_at' => now()->toISOString(),
            ];

            $type = $requiresEarlyCheckout ? EmergencyCase::TYPE_EARLY_CHECKOUT : EmergencyCase::TYPE_ILLNESS;

            // Create emergency case
            $emergencyCase = $this->createEmergencyCase(
                $booking,
                $type,
                $illnessDescription,
                $emergencyDetails
            );

            // If early checkout is required, calculate refund
            if ($requiresEarlyCheckout) {
                $refundAmount = $this->calculateRefundAmount($booking, EmergencyCase::TYPE_EARLY_CHECKOUT);
                
                if ($refundAmount > 0) {
                    $this->processRefund($booking, $refundAmount);
                }
            }

            DB::commit();

            return $emergencyCase;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Illness emergency processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processRefund(Booking $booking, $refundAmount)
    {
        try {
            DB::beginTransaction();

            // Update payment status to refunded
            if ($booking->payment) {
                $booking->payment->update([
                    'status' => Payment::STATUS_REFUNDED,
                ]);
            }

            // Update booking payment status
            $booking->update([
                'payment_status' => Booking::PAYMENT_STATUS_REFUNDED,
            ]);

            // Update emergency case refund status
            if ($booking->emergencyCase) {
                $booking->emergencyCase->update([
                    'refund_status' => EmergencyCase::REFUND_STATUS_PROCESSED,
                ]);
            }

            // Send refund notification
            $this->notificationService->sendRefundNotification($booking, $refundAmount);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function resolveEmergencyCase(EmergencyCase $emergencyCase, $resolutionNotes = '')
    {
        try {
            $emergencyCase->update([
                'status' => EmergencyCase::STATUS_RESOLVED,
                'resolved_at' => now(),
                'emergency_details' => array_merge(
                    $emergencyCase->emergency_details ?? [],
                    ['resolution_notes' => $resolutionNotes, 'resolved_at' => now()->toISOString()]
                ),
            ]);

            return $emergencyCase;
        } catch (\Exception $e) {
            Log::error('Emergency case resolution failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getEmergencyBookingStatus($emergencyType)
    {
        switch ($emergencyType) {
            case EmergencyCase::TYPE_CANCELLATION:
                return Booking::STATUS_EMERGENCY_CANCELLED;
            case EmergencyCase::TYPE_EARLY_CHECKOUT:
                return Booking::STATUS_CHECKED_OUT;
            case EmergencyCase::TYPE_ILLNESS:
                return Booking::STATUS_CONFIRMED; // Keep confirmed but mark as emergency
            default:
                return Booking::STATUS_CONFIRMED;
        }
    }

    protected function calculateRefundAmount(Booking $booking, $emergencyType)
    {
        $totalAmount = $booking->total_amount;
        $taxRate = 0.15; // 15% tax rate
        $taxAmount = $totalAmount * $taxRate;
        $netAmount = $totalAmount - $taxAmount;

        switch ($emergencyType) {
            case EmergencyCase::TYPE_CANCELLATION:
                // Full refund minus tax
                return $netAmount;
            
            case EmergencyCase::TYPE_EARLY_CHECKOUT:
                // Calculate based on remaining nights
                $remainingNights = $this->calculateRemainingNights($booking);
                $totalNights = $booking->nights;
                $refundPercentage = $remainingNights / $totalNights;
                
                return $netAmount * $refundPercentage;
            
            default:
                return 0;
        }
    }

    protected function calculateRemainingNights(Booking $booking)
    {
        $checkoutDate = now();
        $originalCheckout = $booking->check_out_date;
        
        if ($checkoutDate >= $originalCheckout) {
            return 0;
        }

        return $checkoutDate->diffInDays($originalCheckout);
    }

    public function getEmergencyStatistics()
    {
        $totalEmergencies = EmergencyCase::count();
        $openEmergencies = EmergencyCase::where('status', EmergencyCase::STATUS_OPEN)->count();
        $resolvedEmergencies = EmergencyCase::where('status', EmergencyCase::STATUS_RESOLVED)->count();
        
        $totalRefunds = EmergencyCase::where('refund_status', EmergencyCase::REFUND_STATUS_COMPLETED)->sum('refund_amount');
        
        $emergencyTypes = EmergencyCase::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return [
            'total_emergencies' => $totalEmergencies,
            'open_emergencies' => $openEmergencies,
            'resolved_emergencies' => $resolvedEmergencies,
            'total_refunds' => $totalRefunds,
            'emergency_types' => $emergencyTypes,
        ];
    }
}
