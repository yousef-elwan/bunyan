<?php

use Illuminate\Support\Facades\Storage;

$properties = [
    [
        'slug' => '1',
        'type_name' => 'للبيع',
        'image_url' => Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images/house-13.jpg"),
        'description' => '
        <p>
        شقة فاخرة للبيع في وسط مدينة دمشق، تتميز بتشطيبات حديثة وإطلالة رائعة على المدينة، وقربها من الجامعات والخدمات الأساسية.
        </p>',
        'title' => 'شقة فاخرة للبيع في دمشق - حي المزة',
        'rooms_count' => 4,
        'baths' => 2,
        'area' => 180,
        'owner' => 'محمد العلي',
        'price' => '300,000,000',
        'governorate_name' => 'دمشق'
    ],
];
$property =  [
    'slug' => '1',
    'image_url' => Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/images/house-13.jpg"),
    'description' => '
        <p>
        شقة راقية للبيع في أحد أرقى أحياء حلب، حي الفرقان
        ، تتميز بتصميم عصري وإطلالة جميلة، وقربها من الخدمات الأساسية، الجامعات، والمراكز التجارية. تقع في بناء حديث ومجهز بمرافق متكاملة تضمن حياة مريحة.
        </p>',
    'title' => 'شقة سكنية للبيع في حلب - منطقة الفرقان',
    'rooms_count' => 3,
    'floor' => 3,
    'view_count' => '100',
    'area' => '50',
    'area_unite' => 'متر مربع',
    'type_name' => 'للإجار',
    'category_name' => 'شقة',
    'orientation_name' => 'شمالي قبلي',
    'year_built' => '2010',
    'owner' => 'Arlene McCoy',
    'price' => 7250.00,
    'rating_avg' => '70',
    'condition_name' => 'ديلوكس',
    'rating_count' => '183',
    'floors_plan' => [
        [
            'image_url' =>  Storage::disk('public')->url(PROPERTY_IMAGE_NAME . "/1/floors/floor.png"),
            'description' => 'مخطط المنزل',
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
            'name' => 'نوع الإكساء',
            'value' => 'سيراميك',
        ],
        [
            'name' => 'التدفئة',
            'value' => 'مركزي',
        ],
        [
            'name' => 'الشبكات المتاحة',
            'value' => 'كهرباء 24/24، ماء، إنترنت، خط هاتف، ألياف ضوئية',
        ],
        [
            'name' => 'المصعد',
            'value' => 'يوجد',
        ],
        [
            'name' => 'المولدة الكهربائية',
            'value' => 'يوجد',
        ],
        [
            'name' => 'نوع العقد',
            'value' => 'حكم محكمة',
        ],
    ],
    'nearby' => [
        [
            'name' => 'جامعة حلب - كلية الهندسة المعمارية',
            'distance' => 'كم 2.5',
        ],
        [
            'name' => 'معاهد تعليمية خاصة',
            'distance' => 'كم 1.1',
        ],
        [
            'name' => 'مشفى الجامعة الحكومي',
            'distance' => 'كم 1.5',
        ],
        [
            'name' => 'صيدلية الفرقان',
            'distance' => 'كم 0.4',
        ],
        [
            'name' => 'مركز طبي خاص',
            'distance' => 'كم 0.9',
        ],
        [
            'name' => 'مول سيتي سنتر',
            'distance' => 'كم 3.5',
        ],
        [
            'name' => 'سوق الفرقان التجاري',
            'distance' => 'كم 1.2',
        ],
        [
            'name' => 'وقف باص رئيسي',
            'distance' => 'كم 0.3',
        ],
        [
            'name' => 'حديقة الفرقان العامة',
            'distance' => 'كم 1.0',
        ],
        [
            'name' => 'دائرة السجل العقاري بحلب',
            'distance' => 'كم 2.7',
        ],

    ],
    'latitude' => '36.2021',
    'longitude' => '37.1343',
    'location' => 'الفرقان، حلب، سوريا',
    'amenities' => [
        'مكيف هواء',
        'موقف سيارات',
        'مسبح',
        'حديقة',
        'شرفة',
        'مفروش',
        'خدمة إنترنت',
        'مع سطح',
        'خزان مياه',
        'خزن مطبخ',
        'هاتف أرضي',
        'تغطية شبكة الإتصالات',
    ],
    'owner' => [
        'first_name' => 'حسن',
        'last_name' => 'وتار',
        'mobile' => '+963962713870',
        'whatsapp' => '+963962713870',
        'email' => 'hmsoft2000@gmail.com',
    ]
];
$owlProperties = [
    'center' => false,
    'rtl' => true,
    'items' => 2.1,
    'loop' => true,
    'autoplay' => true,
    'autoplayHoverPause' => true,
    'margin' => 5,
    'nav' => false,
    'dots' => false,
    'responsive' => [
        // xs
        '0' => [
            'items' => 1.2,
            // "margin" => 0
        ],
        // sm
        '576' => [
            'items' => 2.2,
        ],
        // md
        '768' => [
            'items' => 2.2,
        ],
        // lg
        '992' => [
            'items' => 3.2,
        ],
        // xl
        '1200' => [
            'items' => 4.2,
        ],
        // xxl
        '1400' => [
            'items' => 4.6,
        ],
    ],
];
$categories = [
    [
        'id' => 1,
        'slug' => 'Apartments',
        'title' => 'شقق',
        'image_url' =>  Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/apartment.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 2,
        'slug' => 'Villa',
        'title' => 'فيلا',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/villa.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 3,
        'slug' => 'Studio',
        'title' => 'استوديو',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/studio.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 4,
        'slug' => 'Townhouse',
        'title' => 'تاون هاوس',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/townhouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 5,
        'slug' => 'Commercial',
        'title' => 'تجاري',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/commercial.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 6,
        'slug' => 'Family Home',
        'title' => 'منزل عائلي',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/family-home.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 7,
        'slug' => 'Land/Plot',
        'title' => 'أرض/قطعة أرض',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/land-plot.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 8,
        'slug' => 'Penthouse',
        'title' => 'بنتهاوس',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/penthouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 9,
        'slug' => 'Warehouse',
        'title' => 'مستودع',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/warehouse.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 10,
        'slug' => 'Farm/Ranch',
        'title' => 'مزرعة/مزرعة رعوية',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/farm-ranch.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 11,
        'slug' => 'Office',
        'title' => 'مكتب',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 12,
        'slug' => 'shalet',
        'title' => 'شاليه',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
    [
        'id' => 13,
        'slug' => 'clinic',
        'title' => 'عيادة',
        'image_url' => Storage::disk('public')->url(CATEGORY_IMAGE_NAME . "/office.svg"),
        'properties_count' => 158,
    ],
];
$types = [
    [
        'id' => 1,
        'title' => 'للإجار'
    ],
    [
        'id' => 2,
        'title' => 'للبيع'
    ],
    [
        'id' => 3,
        'title' => 'للرهن'
    ],
];
$roomsCount = [
    1,
    2,
    3,
    4,
    5,
    6,
    7
];
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
    'مكيف هواء',
    'موقف سيارات',
    'مسبح',
    'حديقة',
    'شرفة',
    'مفروش',
    'خدمة إنترنت',
    'مع سطح',
    'خزان مياه',
    'خزن مطبخ',
    'هاتف أرضي',
    'تغطية شبكة الإتصالات',
];
$orientations = [
    'شرقي',
    'غربي',
    'شمالي',
    'قبلي',
];
$condition = [
    "سوبر ديلوكس",
    "ديلوكس",
    "جيد جدًا",
    "جيد",
    "عادي",
    "بحاجة إلى تجديد",
];
$floors = [
    1,
    2,
    3,
    4,
    5,
];
$types = [
    [
        'id' => 1,
        'title' => 'للإجار'
    ],
    [
        'id' => 2,
        'title' => 'للبيع'
    ],
    [
        'id' => 3,
        'title' => 'للرهن'
    ],
    [
        'id' => 4,
        'title' => 'استثمر'
    ],
];
$reviews = [
    [
        "name" => "محمد خالد",
        "rating" => "100%",
        "feedback" => "المنزل رائع وهادئ جدًا، مثالي للعائلات. الحي آمن وقريب من جميع المرافق الأساسية. أحببنا الحديقة الخلفية والمساحة الواسعة لغرفة المعيشة. أنصح به بشدة لمن يبحث عن الراحة والأمان."
    ],
    [
        "name" => "ريما الأحمد",
        "rating" => "80%",
        "feedback" => "الموقع ممتاز، قريب من المحلات والمدارس. التصميم الداخلي جميل، لكن المطبخ كان بحاجة إلى بعض الصيانة. بشكل عام كانت تجربة سكنية جيدة."
    ],
    [
        "name" => "أحمد السيد",
        "rating" => "100%",
        "feedback" => "منزل واسع مع إطلالة رائعة على المدينة. الخدمات القريبة مثل المطاعم والأسواق سهلت الحياة اليومية. المالك كان متعاونًا جدًا وسهل التواصل معه."
    ],
    [
        "name" => "ليلى ناصر",
        "rating" => "60%",
        "feedback" => "المكان جيد، ولكن العزل الصوتي لم يكن كما توقعت. يمكن سماع الضوضاء من الخارج. كان من الأفضل تحسين هذه المشكلة. ومع ذلك، الحي لطيف ويوفر بيئة آمنة للعائلة."
    ],
    [
        "name" => "محمود جابر",
        "rating" => "100%",
        "feedback" => "أفضل تجربة سكن مررت بها. المنزل نظيف جدًا ومجهز بجميع وسائل الراحة الحديثة. الحي هادئ والجيران ودودون. أنصح به بشدة لمن يبحث عن منزل مريح وعملي."
    ]
];
$cities = [
    [
        'id' => 1,
        'name' => 'دمشق',
        'properties_count' => 5286,
    ],
    [
        'id' => 2,
        'name' => 'ريف دمشق',
        'properties_count' => 5286,
    ],
    [
        'id' => 3,
        'name' => 'حلب',
        'properties_count' => 5286,
    ],
    [
        'id' => 4,
        'name' => 'حمص',
        'properties_count' => 5286,
    ],
    [
        'id' => 5,
        'name' => 'حماة',
        'properties_count' => 5286,
    ],
    [
        'id' => 6,
        'name' => 'اللاذقية',
        'properties_count' => 5286,
    ],
    [
        'id' => 7,
        'name' => 'طرطوس',
        'properties_count' => 5286,
    ],
    [
        'id' => 8,
        'name' => 'إدلب',
        'properties_count' => 5286,
    ],
    [
        'id' => 9,
        'name' => 'دير الزور',
        'properties_count' => 5286,
    ],
    [
        'id' => 10,
        'name' => 'الحسكة',
        'properties_count' => 5286,
    ],
    [
        'id' => 11,
        'name' => 'الرقة',
        'properties_count' => 5286,
    ],
    [
        'id' => 12,
        'name' => 'درعا',
        'properties_count' => 5286,
    ],
    [
        'id' => 13,
        'name' => 'السويداء',
        'properties_count' => 5286,
    ],
    [
        'id' => 14,
        'name' => 'القنيطرة',
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
    'privacyPolicy' => '
    <p dir="rtl" style="text-align: right;">نحن نحترم خصوصيتك وملتزمون بحماية معلوماتك الشخصية. توضح هذه السياسة كيف نجمع ونستخدم ونحمي بياناتك عند استخدامك لموقعنا العقاري.</p>

    <p dir="rtl" style="text-align: right;"><strong>المعلومات التي نجمعها:</strong> قد نجمع اسمك ورقم هاتفك وبريدك الإلكتروني ومعلومات أخرى عند الاتصال بنا أو ملء استمارة.</p>

    <p dir="rtl" style="text-align: right;"><strong>كيف نستخدم معلوماتك:</strong></p>

    <ul dir="rtl" style="text-align: right;">
      <li>للتواصل معك بخصوص استفساراتك</li>
      <li>لتحسين خدماتنا</li>
      <li>لإرسال تحديثات أو محتوى ترويجي (إذا وافقت على ذلك)</li>
    </ul>

    <p dir="rtl" style="text-align: right;"><strong>حماية البيانات:</strong> نتخذ جميع الخطوات اللازمة لحماية بياناتك وضمان عدم مشاركتها مع أطراف غير مصرح لها.</p>

    <p dir="rtl" style="text-align: right;"><strong>أطراف ثالثة:</strong> نحن لا نبيع أو نشارك بياناتك مع أطراف ثالثة دون موافقتك، إلا إذا تطلب القانون ذلك.</p>',

    'termsOfUse' => '<p dir="rtl" style="text-align: right;">
        من خلال دخولك أو استخدامك لموقعنا، فإنك توافق على الشروط التالية:
        </p>
        <ul dir="rtl" style="text-align: right;">
          <li><strong>استخدام المحتوى:</strong> جميع المحتويات على الموقع لأغراض إعلامية فقط، ولا يجوز نسخها أو استخدامها بدون إذن.</li>
          <li><strong>عروض العقارات:</strong> نحرص على أن تكون معلومات العقارات دقيقة ومحدثة، ولكننا لا نتحمل مسؤولية أي أخطاء أو تغييرات.</li>
          <li><strong>سلوك المستخدم:</strong> تتعهد بعدم استخدام الموقع بشكل مسيء، أو إدخال معلومات كاذبة، أو محاولة اختراق النظام.</li>
          <li><strong>تعديلات الشروط:</strong> قد نقوم بتحديث هذه الشروط من وقت لآخر. استخدامك المستمر للموقع يعني موافقتك على أي تعديلات.</li>
        </ul>'
];
