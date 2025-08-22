<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'number' => '101',
                'name' => 'Deluxe Single Room',
                'type' => 'Single',
                'base_price' => 120.00,
                'capacity' => 2,
                'status' => 'available',
                'description' => 'Comfortable single room with modern amenities',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ],
            [
                'number' => '102',
                'name' => 'Deluxe Single Room',
                'type' => 'Single',
                'base_price' => 120.00,
                'capacity' => 2,
                'status' => 'available',
                'description' => 'Comfortable single room with modern amenities',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ],
            [
                'number' => '201',
                'name' => 'Premium Double Room',
                'type' => 'Double',
                'base_price' => 180.00,
                'capacity' => 3,
                'status' => 'available',
                'description' => 'Spacious double room with city view',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ],
            [
                'number' => '202',
                'name' => 'Premium Double Room',
                'type' => 'Double',
                'base_price' => 180.00,
                'capacity' => 3,
                'status' => 'available',
                'description' => 'Spacious double room with city view',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ],
            [
                'number' => '301',
                'name' => 'Executive Suite',
                'type' => 'Suite',
                'base_price' => 350.00,
                'capacity' => 4,
                'status' => 'available',
                'description' => 'Luxury suite with separate living area',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ],
            [
                'number' => '302',
                'name' => 'Executive Suite',
                'type' => 'Suite',
                'base_price' => 350.00,
                'capacity' => 4,
                'status' => 'available',
                'description' => 'Luxury suite with separate living area',
                'image' => 'room-images/IlRcmO9uTTdNXhXM3e73hDn04qTVOsWvWMaUP5zp.png'
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
