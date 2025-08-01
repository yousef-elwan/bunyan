<?php

return [
    'messages' => [
        'first_name.required' => 'The first name field is required.',
        'first_name.string' => 'The first name must be a string.',
        'first_name.max' => 'The first name may not be greater than 255 characters.',
        'last_name.required' => 'The last name field is required.',
        'last_name.string' => 'The last name must be a string.',
        'last_name.max' => 'The last name may not be greater than 255 characters.',
        'mobile.required' => 'The mobile field is required.',
        'mobile.phone' => 'The mobile number is invalid. Please enter a valid phone number with country code.',
        'image.image' => 'The file must be an image.',
        'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
        'image.max' => 'The image size may not be greater than 2048 kilobytes.',
        'email.unique' => 'This email is already taken.',
    ],
    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'mobile' => 'mobile',
        'image' => 'image',
        'email' => 'email',
    ],
];
