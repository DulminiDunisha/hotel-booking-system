<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayHereService
{
    protected $merchantId;
    protected $merchantSecret;
    protected $sandbox;

    public function __construct()
    {
        $this->merchantId = config('services.payhere.merchant_id');
        $this->merchantSecret = config('services.payhere.merchant_secret');
        $this->sandbox = config('services.payhere.sandbox', true);
    }

    public function createPayment(Booking $booking, $paymentMethod)
    {
        try {
            $paymentData = [
                'merchant_id' => $this->merchantId,
                'return_url' => route('payment.return'),
                'cancel_url' => route('payment.cancel'),
                'notify_url' => route('payment.notify'),
                'order_id' => $booking->booking_code,
                'items' => "Room {$booking->room->name} - {$booking->nights} nights",
                'currency' => 'LKR',
                'amount' => $booking->total_amount,
                'first_name' => $booking->guest->name,
                'last_name' => '',
                'email' => $booking->guest->email,
                'phone' => $booking->guest->phone ?? '',
                'address' => '',
                'city' => '',
                'country' => 'Sri Lanka',
                'custom_1' => $booking->id,
                'custom_2' => $paymentMethod,
            ];

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_id' => 'PH_' . uniqid(),
                'amount' => $booking->total_amount,
                'currency' => 'LKR',
                'payment_method' => $paymentMethod,
                'status' => Payment::STATUS_PENDING,
            ]);

            // Generate Pay Here form data
            $formData = $this->generatePaymentForm($paymentData);

            return [
                'payment' => $payment,
                'form_data' => $formData,
                'payhere_url' => $this->getPayHereUrl(),
            ];
        } catch (\Exception $e) {
            Log::error('Pay Here payment creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generatePaymentForm($data)
    {
        $hash = $this->generateHash($data);
        $data['hash'] = $hash;

        return $data;
    }

    protected function generateHash($data)
    {
        $hashString = '';
        foreach ($data as $key => $value) {
            if ($key !== 'hash') {
                $hashString .= $value;
            }
        }
        $hashString .= $this->merchantSecret;

        return strtoupper(sha256($hashString));
    }

    protected function getPayHereUrl()
    {
        return $this->sandbox 
            ? 'https://sandbox.payhere.lk/pay/checkout'
            : 'https://www.payhere.lk/pay/checkout';
    }

    public function verifyPayment($paymentId, $merchantId, $orderId, $payhereAmount, $payhereCurrency, $statusCode, $md5sig)
    {
        $localMd5sig = strtoupper(md5($merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . strtoupper(md5($this->merchantSecret))));

        if ($localMd5sig === $md5sig) {
            return $statusCode === '2'; // 2 = success
        }

        return false;
    }

    public function processPaymentNotification($data)
    {
        try {
            $payment = Payment::where('payment_id', $data['payment_id'])->first();
            
            if (!$payment) {
                Log::error('Payment not found for Pay Here notification: ' . $data['payment_id']);
                return false;
            }

            if ($this->verifyPayment(
                $data['payment_id'],
                $data['merchant_id'],
                $data['order_id'],
                $data['payhere_amount'],
                $data['payhere_currency'],
                $data['status_code'],
                $data['md5sig']
            )) {
                $payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                    'payment_details' => $data,
                    'paid_at' => now(),
                ]);

                // Update booking status
                $payment->booking->update([
                    'payment_status' => Booking::PAYMENT_STATUS_COMPLETED,
                    'status' => Booking::STATUS_CONFIRMED,
                ]);

                return true;
            }

            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'payment_details' => $data,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Pay Here notification processing failed: ' . $e->getMessage());
            return false;
        }
    }
}
