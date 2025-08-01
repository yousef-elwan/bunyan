<?php

return [
    'messages' => [
        'property_id.required' => 'The property ID field is required.',
        'name.required' => 'The name field is required.',
        'name.string' => 'The name must be a string.',
        'name.max' => 'The name may not be greater than 255 characters.',
        'mobile.required' => 'The mobile field is required.',
        'mobile.string' => 'The mobile must be a string.',
        'mobile.max' => 'The mobile may not be greater than 50 characters.',
        'email.email' => 'The email must be a valid email address.',
        'email.max' => 'The email may not be greater than 255 characters.',
        'time_id.required' => 'The showing time field is required.',
        'time_id.exists' => 'The selected showing time is invalid.',
        'showing_request_type_id.required' => 'The showing request type field is required.',
        'showing_request_type_id.exists' => 'The selected showing request type is invalid.',
        'message.string' => 'The message must be a string.',
    ],
    'attributes' => [
        'property_id' => 'property ID',
        'name' => 'name',
        'mobile' => 'mobile',
        'email' => 'email',
        'time_id' => 'showing time',
        'showing_request_type_id' => 'showing request type',
        'message' => 'message',
    ],
];
