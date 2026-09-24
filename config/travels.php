<?php

return [

    'brand' => [
        'name' => 'Travels',
        'tagline' => 'Travel With Faith',
    ],

    'contact' => [
        'location' => 'India, Telangana, India',
        'email' => 'bookings@travels.com',
        'phone_primary' => '+91-0000000000',
        'phone_secondary' => '+91-0000000000',
        'whatsapp' => '+910000000000',
    ],

    'social' => [
        'facebook' => '#',
        'youtube' => '#',
        'instagram' => '#',
        'whatsapp' => '#',
    ],

    'booking' => [
        'tax_percent' => env('BOOKING_TAX_PERCENT', 0),
        'payment_hold_minutes' => env('BOOKING_PAYMENT_HOLD_MINUTES', 15),
    ],

    'tour_categories' => [
    [
        'label' => 'Spiritual & Pilgrimage',
        'category' => 'spiritual',
    ],
    [
        'label' => 'Holidays',
        'category' => 'holidays',
    ],
    [
        'label' => 'School & College',
        'category' => 'school-college',
    ],
    [
        'label' => 'Business Trips',
        'category' => 'business',
    ],
    [
        'label' => 'Monthly Tours',
        'category' => 'monthly',
    ],
],

];
