<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hotel Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings specific to Dumidu Hotel
    |
    */

    'name' => env('HOTEL_NAME', 'Dumidu Hotel'),
    'phone' => env('HOTEL_PHONE', '+94 11 234 5678'),
    'email' => env('HOTEL_EMAIL', 'info@dumiduhotel.com'),
    'address' => env('HOTEL_ADDRESS', '123 Hotel Street, Colombo, Sri Lanka'),
    'website' => env('HOTEL_WEBSITE', 'www.dumiduhotel.com'),

    /*
    |--------------------------------------------------------------------------
    | Booking Settings
    |--------------------------------------------------------------------------
    */
    'booking' => [
        'check_in_time' => '14:00',
        'check_out_time' => '11:00',
        'max_advance_booking_days' => 365,
        'min_advance_booking_hours' => 2,
        'cancellation_hours_before' => 24,
        'tax_rate' => 0.15, // 15%
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency Settings
    |--------------------------------------------------------------------------
    */
    'emergency' => [
        'phone_number' => '+94 11 234 5678',
        'email' => 'emergency@dumiduhotel.com',
        'response_time_minutes' => 15,
        'refund_processing_days' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'currency' => 'LKR',
        'supported_methods' => [
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'mobile_payment' => 'Mobile Payment',
            'online_banking' => 'Online Banking',
        ],
        'refund_processing_days' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'sms_enabled' => env('SMS_ENABLED', true),
        'email_enabled' => env('EMAIL_ENABLED', true),
        'booking_confirmation' => true,
        'payment_confirmation' => true,
        'hotel_rules' => true,
        'emergency_alerts' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Settings
    |--------------------------------------------------------------------------
    */
    'gallery' => [
        'max_file_size' => 5120, // 5MB
        'allowed_formats' => ['jpeg', 'png', 'jpg', 'gif'],
        'storage_path' => 'hotel-images',
        'thumbnail_width' => 300,
        'thumbnail_height' => 200,
    ],
];
