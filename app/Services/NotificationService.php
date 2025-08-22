<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function sendBookingConfirmation(Booking $booking)
    {
        try {
            $guest = $booking->guest;
            $room = $booking->room;

            // Email notification
            $this->sendEmailNotification($guest, [
                'type' => Notification::TYPE_EMAIL,
                'title' => 'Booking Confirmation - Dumidu Hotel',
                'message' => $this->generateBookingEmailMessage($booking),
                'metadata' => ['email' => $guest->email]
            ]);

            // SMS notification (if phone number exists)
            if ($guest->phone) {
                $this->sendSMSNotification($guest, [
                    'type' => Notification::TYPE_SMS,
                    'title' => 'Booking Confirmed',
                    'message' => $this->generateBookingSMSMessage($booking),
                    'metadata' => ['phone' => $guest->phone]
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Booking confirmation notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentConfirmation(Booking $booking)
    {
        try {
            $guest = $booking->guest;

            // Email notification
            $this->sendEmailNotification($guest, [
                'type' => Notification::TYPE_EMAIL,
                'title' => 'Payment Confirmation - Dumidu Hotel',
                'message' => $this->generatePaymentEmailMessage($booking),
                'metadata' => ['email' => $guest->email]
            ]);

            // SMS notification
            if ($guest->phone) {
                $this->sendSMSNotification($guest, [
                    'type' => Notification::TYPE_SMS,
                    'title' => 'Payment Confirmed',
                    'message' => $this->generatePaymentSMSMessage($booking),
                    'metadata' => ['phone' => $guest->phone]
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Payment confirmation notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendHotelRules(Booking $booking)
    {
        try {
            $guest = $booking->guest;

            // Email notification with hotel rules
            $this->sendEmailNotification($guest, [
                'type' => Notification::TYPE_EMAIL,
                'title' => 'Hotel Rules & Regulations - Dumidu Hotel',
                'message' => $this->generateHotelRulesMessage($booking),
                'metadata' => ['email' => $guest->email]
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Hotel rules notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendEmergencyNotification(Booking $booking, $emergencyType, $description)
    {
        try {
            $guest = $booking->guest;

            // Email notification
            $this->sendEmailNotification($guest, [
                'type' => Notification::TYPE_EMAIL,
                'title' => 'Emergency Case - Dumidu Hotel',
                'message' => $this->generateEmergencyEmailMessage($booking, $emergencyType, $description),
                'metadata' => ['email' => $guest->email]
            ]);

            // SMS notification for urgent cases
            if ($guest->phone) {
                $this->sendSMSNotification($guest, [
                    'type' => Notification::TYPE_SMS,
                    'title' => 'Emergency Case',
                    'message' => $this->generateEmergencySMSMessage($booking, $emergencyType),
                    'metadata' => ['phone' => $guest->phone]
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Emergency notification failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendEmailNotification(User $user, array $data)
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'status' => Notification::STATUS_PENDING,
                'metadata' => $data['metadata'],
            ]);

            // Send email using Laravel Mail
            Mail::send('emails.notification', [
                'title' => $data['title'],
                'message' => $data['message'],
                'user' => $user
            ], function ($message) use ($user, $data) {
                $message->to($user->email, $user->name)
                        ->subject($data['title']);
            });

            // Update notification status
            $notification->update([
                'status' => Notification::STATUS_SENT,
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Email notification failed: ' . $e->getMessage());
            
            if (isset($notification)) {
                $notification->update(['status' => Notification::STATUS_FAILED]);
            }
            
            return false;
        }
    }

    protected function sendSMSNotification(User $user, array $data)
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'status' => Notification::STATUS_PENDING,
                'metadata' => $data['metadata'],
            ]);

            // Send SMS using configured SMS service
            $this->sendSMS($user->phone, $data['message']);

            // Update notification status
            $notification->update([
                'status' => Notification::STATUS_SENT,
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('SMS notification failed: ' . $e->getMessage());
            
            if (isset($notification)) {
                $notification->update(['status' => Notification::STATUS_FAILED]);
            }
            
            return false;
        }
    }

    protected function sendSMS($phone, $message)
    {
        // This would integrate with your SMS service provider
        // For now, we'll log the SMS
        Log::info("SMS sent to {$phone}: {$message}");
        
        // Example integration with SMS service:
        // $response = Http::post(config('services.sms.endpoint'), [
        //     'phone' => $phone,
        //     'message' => $message,
        //     'api_key' => config('services.sms.api_key'),
        // ]);
    }

    protected function generateBookingEmailMessage(Booking $booking)
    {
        return "
            Dear {$booking->guest->name},

            Your booking has been confirmed at Dumidu Hotel!

            Booking Details:
            - Booking Code: {$booking->booking_code}
            - Room: {$booking->room->name}
            - Check-in: {$booking->check_in_date->format('M d, Y')}
            - Check-out: {$booking->check_out_date->format('M d, Y')}
            - Nights: {$booking->nights}
            - Total Amount: LKR {$booking->total_amount}

            We look forward to welcoming you!

            Best regards,
            Dumidu Hotel Team
        ";
    }

    protected function generateBookingSMSMessage(Booking $booking)
    {
        return "Your booking {$booking->booking_code} is confirmed. Check-in: {$booking->check_in_date->format('M d')}. Dumidu Hotel";
    }

    protected function generatePaymentEmailMessage(Booking $booking)
    {
        return "
            Dear {$booking->guest->name},

            Payment received successfully for your booking!

            Payment Details:
            - Booking Code: {$booking->booking_code}
            - Amount Paid: LKR {$booking->total_amount}
            - Payment Method: {$booking->payment_method}
            - Status: Confirmed

            Your room is now reserved and ready for your arrival.

            Best regards,
            Dumidu Hotel Team
        ";
    }

    protected function generatePaymentSMSMessage(Booking $booking)
    {
        return "Payment confirmed for booking {$booking->booking_code}. Amount: LKR {$booking->total_amount}. Dumidu Hotel";
    }

    protected function generateHotelRulesMessage(Booking $booking)
    {
        return "
            Dear {$booking->guest->name},

            Please review our hotel rules and regulations:

            Check-in/Check-out:
            - Check-in time: 2:00 PM
            - Check-out time: 11:00 AM
            - Early check-in subject to availability

            Cancellation Policy:
            - Free cancellation up to 24 hours before check-in
            - Emergency cancellations: Contact hotel directly
            - Partial refunds available for emergency cases

            Hotel Rules:
            - Smoking is not allowed in rooms
            - Pets are not permitted
            - Quiet hours: 10:00 PM - 7:00 AM
            - Room service available 24/7

            For emergencies, call: +94 11 234 5678

            Best regards,
            Dumidu Hotel Team
        ";
    }

    protected function generateEmergencyEmailMessage(Booking $booking, $emergencyType, $description)
    {
        return "
            Dear {$booking->guest->name},

            We have received your emergency notification.

            Emergency Details:
            - Type: {$emergencyType}
            - Description: {$description}
            - Booking Code: {$booking->booking_code}

            Our team is handling your case and will contact you shortly.
            For immediate assistance, call: +94 11 234 5678

            Best regards,
            Dumidu Hotel Emergency Team
        ";
    }

    protected function generateEmergencySMSMessage(Booking $booking, $emergencyType)
    {
        return "Emergency case registered for booking {$booking->booking_code}. Type: {$emergencyType}. We'll contact you soon.";
    }
}
