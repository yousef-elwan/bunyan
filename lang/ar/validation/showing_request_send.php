<?php

return [
    'messages' => [
        'property_id.required' => 'حقل معرف العقار مطلوب.',
        'name.required' => 'حقل الاسم مطلوب.',
        'name.string' => 'حقل الاسم يجب أن يكون نصًا.',
        'name.max' => 'حقل الاسم يجب ألا يزيد عن 255 حرفًا.',
        'mobile.required' => 'حقل رقم الهاتف مطلوب.',
        'mobile.string' => 'حقل رقم الهاتف يجب أن يكون نصًا.',
        'mobile.max' => 'حقل رقم الهاتف يجب ألا يزيد عن 50 حرفًا.',
        'email.email' => 'حقل البريد الإلكتروني يجب أن يكون بريدًا إلكترونيًا صالحًا.',
        'email.max' => 'حقل البريد الإلكتروني يجب ألا يزيد عن 255 حرفًا.',
        'time_id.required' => 'حقل وقت العرض مطلوب.',
        'time_id.exists' => 'الوقت المحدد غير صالح.',
        'showing_request_type_id.required' => 'حقل نوع طلب العرض مطلوب.',
        'showing_request_type_id.exists' => 'نوع طلب العرض المحدد غير صالح.',
        'message.string' => 'حقل الرسالة يجب أن يكون نصًا.',
    ],
    'attributes' => [
        'property_id' => 'معرف العقار',
        'name' => 'الاسم',
        'mobile' => 'رقم الهاتف',
        'email' => 'البريد الإلكتروني',
        'time_id' => 'وقت العرض',
        'showing_request_type_id' => 'نوع طلب العرض',
        'message' => 'الرسالة',
    ],
];
