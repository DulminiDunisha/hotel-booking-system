<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'guest')->get();
        $rooms = Room::all();

        if ($users->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        $sampleBookings = [
            [
                'guest_id' => $users[0]->id,
                'room_id' => $rooms[0]->id,
                'check_in_date' => now()->addDays(1)->format('Y-m-d'),
                'check_out_date' => now()->addDays(3)->format('Y-m-d'),
                'nights' => 2,
                'adults' => 2,
                'children' => 0,
                'total_amount' => 240.00,
                'status' => 'confirmed',
                'payment_method' => 'credit_card',
                'payment_status' => 'paid'
            ],
            [
                'guest_id' => $users[1]->id,
                'room_id' => $rooms[2]->id,
                'check_in_date' => now()->addDays(2)->format('Y-m-d'),
                'check_out_date' => now()->addDays(5)->format('Y-m-d'),
                'nights' => 3,
                'adults' => 2,
                'children' => 1,
                'total_amount' => 540.00,
                'status' => 'pending',
                'payment_method' => 'debit_card',
                'payment_status' => 'pending'
            ],
            [
                'guest_id' => $users[2]->id,
                'room_id' => $rooms[4]->id,
                'check_in_date' => now()->addDays(7)->format('Y-m-d'),
                'check_out_date' => now()->addDays(10)->format('Y-m-d'),
                'nights' => 3,
                'adults' => 2,
                'children' => 2,
                'total_amount' => 1050.00,
                'status' => 'confirmed',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid'
            ]
        ];

        foreach ($sampleBookings as $bookingData) {
            $bookingData['booking_code'] = 'BK' . strtoupper(uniqid());
            Booking::create($bookingData);
        }
    }
}
