<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HotelImage;

class HotelImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            // Rooms & Suites
            [
                'title' => 'Deluxe Ocean View Suite',
                'description' => 'Spacious suite with breathtaking ocean views and premium amenities',
                'image_path' => 'hotel-images/deluxe-ocean-suite.jpg',
                'category' => HotelImage::CATEGORY_ROOMS,
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Executive King Room',
                'description' => 'Elegant king room with modern furnishings and city views',
                'image_path' => 'hotel-images/executive-king-room.jpg',
                'category' => HotelImage::CATEGORY_ROOMS,
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Presidential Suite',
                'description' => 'Ultimate luxury with panoramic views and exclusive services',
                'image_path' => 'hotel-images/presidential-suite.jpg',
                'category' => HotelImage::CATEGORY_ROOMS,
                'is_featured' => true,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Family Suite',
                'description' => 'Perfect for families with connecting rooms and kid-friendly amenities',
                'image_path' => 'hotel-images/family-suite.jpg',
                'category' => HotelImage::CATEGORY_ROOMS,
                'is_featured' => false,
                'sort_order' => 4,
                'is_active' => true,
            ],

            // Hotel Facilities
            [
                'title' => 'Infinity Pool',
                'description' => 'Stunning infinity pool overlooking the ocean with poolside service',
                'image_path' => 'hotel-images/infinity-pool.jpg',
                'category' => HotelImage::CATEGORY_FACILITIES,
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Spa & Wellness Center',
                'description' => 'Luxury spa offering traditional and modern treatments',
                'image_path' => 'hotel-images/spa-wellness.jpg',
                'category' => HotelImage::CATEGORY_FACILITIES,
                'is_featured' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Fine Dining Restaurant',
                'description' => 'Award-winning restaurant serving international cuisine',
                'image_path' => 'hotel-images/fine-dining.jpg',
                'category' => HotelImage::CATEGORY_FACILITIES,
                'is_featured' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Fitness Center',
                'description' => '24/7 state-of-the-art fitness center with personal trainers',
                'image_path' => 'hotel-images/fitness-center.jpg',
                'category' => HotelImage::CATEGORY_FACILITIES,
                'is_featured' => false,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Conference Hall',
                'description' => 'Modern conference facilities for business events and meetings',
                'image_path' => 'hotel-images/conference-hall.jpg',
                'category' => HotelImage::CATEGORY_FACILITIES,
                'is_featured' => false,
                'sort_order' => 5,
                'is_active' => true,
            ],

            // Local Attractions
            [
                'title' => 'Beachfront Access',
                'description' => 'Direct access to pristine beaches with water sports activities',
                'image_path' => 'hotel-images/beachfront.jpg',
                'category' => HotelImage::CATEGORY_ATTRACTIONS,
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Historic City Center',
                'description' => 'Explore the rich history and culture of the local area',
                'image_path' => 'hotel-images/city-center.jpg',
                'category' => HotelImage::CATEGORY_ATTRACTIONS,
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Shopping District',
                'description' => 'Premium shopping experience with local and international brands',
                'image_path' => 'hotel-images/shopping-district.jpg',
                'category' => HotelImage::CATEGORY_ATTRACTIONS,
                'is_featured' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // General
            [
                'title' => 'Hotel Lobby',
                'description' => 'Elegant lobby with stunning architecture and warm hospitality',
                'image_path' => 'hotel-images/hotel-lobby.jpg',
                'category' => HotelImage::CATEGORY_GENERAL,
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Garden View',
                'description' => 'Beautiful landscaped gardens with tropical plants and flowers',
                'image_path' => 'hotel-images/garden-view.jpg',
                'category' => HotelImage::CATEGORY_GENERAL,
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Sunset View',
                'description' => 'Breathtaking sunset views from our rooftop terrace',
                'image_path' => 'hotel-images/sunset-view.jpg',
                'category' => HotelImage::CATEGORY_GENERAL,
                'is_featured' => true,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($images as $image) {
            HotelImage::create($image);
        }
    }
}
