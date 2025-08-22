<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PayHereService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $payHereService;
    protected $notificationService;

    public function __construct(PayHereService $payHereService, NotificationService $notificationService)
    {
        $this->payHereService = $payHereService;
        $this->notificationService = $notificationService;
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|in:credit_card,debit_card,mobile_payment,online_banking',
        ]);

        try {
            $booking = Booking::with(['guest', 'room'])->findOrFail($request->booking_id);
            
            // Check if payment already exists
            if ($booking->payment) {
                return response()->json([
                    'error' => 'Payment already exists for this booking'
                ], 400);
            }

            // Create payment
            $paymentData = $this->payHereService->createPayment($booking, $request->payment_method);

            return response()->json([
                'success' => true,
                'payment' => $paymentData['payment'],
                'form_data' => $paymentData['form_data'],
                'payhere_url' => $paymentData['payhere_url'],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment initiation failed'
            ], 500);
        }
    }

    public function paymentReturn(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $orderId = $request->input('order_id');
            $statusCode = $request->input('status_code');

            $payment = Payment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                return redirect()->route('payment.error')->with('error', 'Payment not found');
            }

            if ($statusCode === '2') { // Success
                // Payment was successful
                $payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                    'payment_details' => $request->all(),
                    'paid_at' => now(),
                ]);

                $payment->booking->update([
                    'payment_status' => Booking::PAYMENT_STATUS_COMPLETED,
                    'status' => Booking::STATUS_CONFIRMED,
                ]);

                // Send notifications
                $this->notificationService->sendPaymentConfirmation($payment->booking);
                $this->notificationService->sendHotelRules($payment->booking);

                return redirect()->route('payment.success')->with('success', 'Payment completed successfully');
            } else {
                // Payment failed
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'payment_details' => $request->all(),
                ]);

                return redirect()->route('payment.error')->with('error', 'Payment failed');
            }

        } catch (\Exception $e) {
            Log::error('Payment return processing failed: ' . $e->getMessage());
            return redirect()->route('payment.error')->with('error', 'Payment processing error');
        }
    }

    public function paymentCancel(Request $request)
    {
        return redirect()->route('payment.error')->with('error', 'Payment was cancelled');
    }

    public function paymentNotify(Request $request)
    {
        try {
            $result = $this->payHereService->processPaymentNotification($request->all());
            
            if ($result) {
                return response('OK', 200);
            } else {
                return response('FAILED', 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment notification processing failed: ' . $e->getMessage());
            return response('ERROR', 500);
        }
    }

    public function paymentSuccess()
    {
        return Inertia::render('PaymentSuccess');
    }

    public function paymentError()
    {
        return Inertia::render('PaymentError');
    }

    public function getPaymentStatus($paymentId)
    {
        try {
            $payment = Payment::with(['booking.guest', 'booking.room'])->findOrFail($paymentId);
            
            return response()->json([
                'success' => true,
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Payment not found'
            ], 404);
        }
    }

    public function refundPayment(Request $request, $paymentId)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        try {
            $payment = Payment::with('booking')->findOrFail($paymentId);
            
            if ($payment->status !== Payment::STATUS_COMPLETED) {
                return response()->json([
                    'error' => 'Payment cannot be refunded'
                ], 400);
            }

            // Process refund
            $payment->update([
                'status' => Payment::STATUS_REFUNDED,
            ]);

            $payment->booking->update([
                'payment_status' => Booking::PAYMENT_STATUS_REFUNDED,
            ]);

            // Send refund notification
            $this->notificationService->sendRefundNotification($payment->booking, $request->refund_amount);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Refund processing failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Refund processing failed'
            ], 500);
        }
    }
}
