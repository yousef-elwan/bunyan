<?php

use Illuminate\Support\Facades\Storage;

$properties = [
    [
        'slug' => '1',
        'type_name' => 'For Sale',
        'image_url' => Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images/house-13.jpg"),
        'description' => '
        <p>
        Luxury apartment for sale in central Damascus, featuring modern finishes and stunning city views, close to universities and essential services.
        </p>',
        'title' => 'Luxury Apartment for Sale in Damascus - Al-Mazzeh District',
        'rooms_count' => 4,
        'baths' => 2,
        'area' => 180,
        'owner' => 'Mohammed Ali',
        'price' => '300,000,000',
        'governorate_name' => 'Damascus'
    ],
];
$property =  [
    'slug' => '1',
    'image_url' => Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images/house-13.jpg"),
    'description' => '
        <p>
        Elegant apartment for sale in one of Aleppo\'s finest neighborhoods, Al-Furqan district, featuring modern design, beautiful views, and proximity to essential services, universities, and commercial centers. Located in a modern building with complete facilities ensuring comfortable living.
        </p>',
    'title' => 'Residential Apartment for Sale in Aleppo - Al-Furqan Area',
    'rooms_count' => 3,
    'floor' => 3,
    'view_count' => '100',
    'area' => '50',
    'area_unite' => 'Square Meters',
    'type_name' => 'For Rent',
    'category_name' => 'Apartment',
    'orientation_name' => 'North-South',
    'year_built' => '2010',
    'owner' => 'Arlene McCoy',
    'price' => '7,250.00',
    'rating_avg' => '70',
    'condition_name' => 'Deluxe',
    'rating_count' => '183',
    'floors_plan' => [
        [
            'image_url' =>  Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/floors/floor.png"),
            'description' => 'House Plan',
        ]
    ],
    'video_url' => 'https://youtu.be/MLpWrANjFbI',
    'images' => [
        Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images") . "/banner-property-5.jpg",
        Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images") . "/banner-property-6.jpg",
        Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images") . "/banner-property-7.jpg",
        Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images") . "/banner-property-8.jpg",
        Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images") . "/banner-property-9.jpg",
    ],
    'floor_plan' => [],
    'attributes' => [
        [
            'name' => 'Finishing Type',
            'value' => 'Ceramic',
        ],
        [
            'name' => 'Heating',
            'value' => 'Central',
        ],
        [
            'name' => 'Available Networks',
            'value' => '24/7 Electricity, Water, Internet, Phone Line, Fiber Optic',
        ],
        [
            'name' => 'Elevator',
            'value' => 'Available',
        ],
        [
            'name' => 'Generator',
            'value' => 'Available',
        ],
        [
            'name' => 'Contract Type',
            'value' => 'Court Ruling',
        ],
    ],
    'nearby' => [
        [
            'name' => 'Aleppo University - Faculty of Architecture',
            'distance' => '2.5 km',
        ],
        [
            'name' => 'Private Educational Institutes',
            'distance' => '1.1 km',
        ],
        [
            'name' => 'University Government Hospital',
            'distance' => '1.5 km',
        ],
        [
            'name' => 'Al-Furqan Pharmacy',
            'distance' => '0.4 km',
        ],
        [
            'name' => 'Private Medical Center',
            'distance' => '0.9 km',
        ],
        [
            'name' => 'City Center Mall',
            'distance' => '3.5 km',
        ],
        [
            'name' => 'Al-Furqan Commercial Market',
            'distance' => '1.2 km',
        ],
        [
            'name' => 'Main Bus Stop',
            'distance' => '0.3 km',
        ],
        [
            'name' => 'Al-Furqan Public Park',
            'distance' => '1.0 km',
        ],
        [
            'name' => 'Aleppo Real Estate Registry',
            'distance' => '2.7 km',
        ],
    ],
    'latitude' => '36.2021',
    'longitude' => '37.1343',
    'location' => 'Al-Furqan, Aleppo, Syria',
    'amenities' => [
        'Air Conditioning',
        'Parking',
        'Swimming Pool',
        'Garden',
        'Balcony',
        'Furnished',
        'Internet Service',
        'With Roof',
        'Water Tank',
        'Kitchen Storage',
        'Landline Phone',
        'Network Coverage',
    ],
    'owner' => [
        'first_name' => 'Hassan',
        'last_name' => 'Watar',
        'mobile' => '+963962713870',
        'whatsapp' => '+963962713870',
        'email' => 'hmsoft2000@gmail.com',
    ]
];
$owlProperties = [
    'center' => false,
    'rtl' => false,
    'items' => 2.1,
    'loop' => true,
    'autoplay' => true,
    'autoplayHoverPause' => true,
    'margin' => 5,
    'nav' => false,
    'dots' => false,
    'responsive' => [
        '0' => ['items' => 1.2],
        '576' => ['items' => 2.2],
        '768' => ['items' => 2.2],
        '992' => ['items' => 3.2],
        '1200' => ['items' => 4.2],
        '1400' => ['items' => 4.6],
    ],
];
$categories = [
    [
        'id' => 1,
        'slug' => 'Apartments',
        'title' => 'Apartments',
        'image_url' =>  Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/apartment.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 2,
        'slug' => 'Villa',
        'title' => 'Villa',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/villa.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 3,
        'slug' => 'Studio',
        'title' => 'Studio',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/studio.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 4,
        'slug' => 'Townhouse',
        'title' => 'Townhouse',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/townhouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 5,
        'slug' => 'Commercial',
        'title' => 'Commercial',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/commercial.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 6,
        'slug' => 'Family Home',
        'title' => 'Family Home',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/family-home.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 7,
        'slug' => 'Land/Plot',
        'title' => 'Land/Plot',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/land-plot.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 8,
        'slug' => 'Penthouse',
        'title' => 'Penthouse',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/penthouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 9,
        'slug' => 'Warehouse',
        'title' => 'Warehouse',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/warehouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 10,
        'slug' => 'Farm/Ranch',
        'title' => 'Farm/Ranch',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/farm-ranch.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 11,
        'slug' => 'Office',
        'title' => 'Office',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 12,
        'slug' => 'shalet',
        'title' => 'Chalet',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 13,
        'slug' => 'clinic',
        'title' => 'Clinic',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
];
$types = [
    [
        'id' => 1,
        'title' => 'For Rent'
    ],
    [
        'id' => 2,
        'title' => 'For Sale'
    ],
    [
        'id' => 3,
        'title' => 'For Mortgage'
    ],
];
$roomsCount = [1, 2, 3, 4, 5, 6, 7];
$partners = [
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-1.svg"),
    ],
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-2.svg"),
    ],
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-3.svg"),
    ],
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-4.svg"),
    ],
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-5.svg"),
    ],
    [
        'href' => '',
        'image_url' => Storage::disk('public')->url(PARTNER_IMAGE_NAME . "/partner-6.svg"),
    ]
];
$amenities = [
    'Air Conditioning',
    'Parking',
    'Swimming Pool',
    'Garden',
    'Balcony',
    'Furnished',
    'Internet Service',
    'With Roof',
    'Water Tank',
    'Kitchen Storage',
    'Landline Phone',
    'Network Coverage',
];
$orientations = [
    'East',
    'West',
    'North',
    'South',
];
$condition = [
    "Super Deluxe",
    "Deluxe",
    "Very Good",
    "Good",
    "Normal",
    "Needs Renovation",
];
$floors = [1, 2, 3, 4, 5];
$types = [
    [
        'id' => 1,
        'title' => 'For Rent'
    ],
    [
        'id' => 2,
        'title' => 'For Sale'
    ],
    [
        'id' => 3,
        'title' => 'For Mortgage'
    ],
    [
        'id' => 4,
        'title' => 'Invest'
    ],
];
$reviews = [
    [
        "name" => "Mohammed Khaled",
        "rating" => "100%",
        "feedback" => "The house is wonderful and very quiet, perfect for families. The neighborhood is safe and close to all essential facilities. We loved the backyard garden and the spacious living room. Highly recommended for those looking for comfort and safety."
    ],
    [
        "name" => "Rima Al-Ahmad",
        "rating" => "80%",
        "feedback" => "Excellent location, close to shops and schools. The interior design is beautiful, but the kitchen needed some maintenance. Overall it was a good living experience."
    ],
    [
        "name" => "Ahmed Al-Sayed",
        "rating" => "100%",
        "feedback" => "Spacious house with a wonderful view of the city. Nearby services like restaurants and markets made daily life easier. The owner was very cooperative and easy to communicate with."
    ],
    [
        "name" => "Layla Nasser",
        "rating" => "60%",
        "feedback" => "The place is good, but the sound insulation wasn't as I expected. Noise from outside can be heard. It would be better to improve this issue. However, the neighborhood is nice and provides a safe environment for the family."
    ],
    [
        "name" => "Mahmoud Jaber",
        "rating" => "100%",
        "feedback" => "The best living experience I've had. The house is very clean and equipped with all modern amenities. The neighborhood is quiet and the neighbors are friendly. Highly recommended for those looking for a comfortable and practical home."
    ]
];
$cities = [
    [
        'id' => 1,
        'name' => 'Damascus',
        'properties_count' => 5286,
    ],
    [
        'id' => 2,
        'name' => 'Rif Dimashq',
        'properties_count' => 5286,
    ],
    [
        'id' => 3,
        'name' => 'Aleppo',
        'properties_count' => 5286,
    ],
    [
        'id' => 4,
        'name' => 'Homs',
        'properties_count' => 5286,
    ],
    [
        'id' => 5,
        'name' => 'Hama',
        'properties_count' => 5286,
    ],
    [
        'id' => 6,
        'name' => 'Latakia',
        'properties_count' => 5286,
    ],
    [
        'id' => 7,
        'name' => 'Tartus',
        'properties_count' => 5286,
    ],
    [
        'id' => 8,
        'name' => 'Idlib',
        'properties_count' => 5286,
    ],
    [
        'id' => 9,
        'name' => 'Deir ez-Zor',
        'properties_count' => 5286,
    ],
    [
        'id' => 10,
        'name' => 'Al-Hasakah',
        'properties_count' => 5286,
    ],
    [
        'id' => 11,
        'name' => 'Raqqa',
        'properties_count' => 5286,
    ],
    [
        'id' => 12,
        'name' => 'Daraa',
        'properties_count' => 5286,
    ],
    [
        'id' => 13,
        'name' => 'As-Suwayda',
        'properties_count' => 5286,
    ],
    [
        'id' => 14,
        'name' => 'Quneitra',
        'properties_count' => 5286,
    ]
];
$sliders = [
    [
        'id' => 1,
        'image_url' =>  Storage::disk('public')->url(SLIDER_IMAGE_NAME . "/slider-2-1.jpg")
    ],
    [
        'id' => 1,
        'image_url' =>  Storage::disk('public')->url(SLIDER_IMAGE_NAME . "/slider-2-3.jpg")
    ],
    [
        'id' => 1,
        'image_url' =>  Storage::disk('public')->url(SLIDER_IMAGE_NAME . "/slider-2.jpg")
    ],
];


$legal = [
    'privacyPolicy' =>'We respect your privacy and are committed to protecting your personal information. This policy explains how we collect, use, and protect your data when you use our real estate app.</p>
    
    <p><strong>Information We Collect:</strong> We may collect your name, phone number, email, and other details when you contact us or fill out a form.</p>
    
    <p><strong>How We Use Your Information:</strong></p>
    
    <ul>
      <li>To contact you regarding your inquiries</li>
      <li>To improve our services</li>
      <li>To send updates or promotional content (if you agree)</li>
    </ul>

    <p><strong>Data Protection:</strong> We take all necessary steps to protect your data and ensure it\'s not shared with unauthorized parties.</p>

    <p><strong>Third Parties:</strong> We do not sell or share your data with third parties without your consent, except as required by law.</p>',
    'termsOfUse' => 'By accessing or using our website, you agree to the following terms:</p>
        <ul>
          <li><strong>Use of Content:</strong> All content on the website is for informational purposes only and may not be copied or reused without permission.</li>
          <li><strong>Property Listings:</strong> We try to ensure that all property listings are accurate and up to date. However, we are not liable for any inaccuracies.</li>
          <li><strong>User Conduct:</strong> You agree not to misuse the site, post false information, or attempt to hack or harm the system.</li>
          <li><strong>Changes to Terms:</strong> We may update these terms from time to time. Continued use of the site means you accept any changes.</li>
        </ul>'
];
