<?php

return [
    'messages' => [
        'first_name.required' => 'The first name field is required.',
        'first_name.string' => 'The first name must be a string.',
        'first_name.max' => 'The first name may not be greater than 255 characters.',
        'last_name.required' => 'The last name field is required.',
        'last_name.string' => 'The last name must be a string.',
        'last_name.max' => 'The last name may not be greater than 255 characters.',
        'email.required' => 'The email field is required.',
        'email.string' => 'The email must be a string.',
        'email.email' => 'The email must be a valid email address.',
        'email.max' => 'The email may not be greater than 255 characters.',
        'email.unique' => 'The email has already been taken.',
        'phone.required' => 'The phone field is required.',
        'phone.phone' => 'Please enter a valid phone number in international format.',
        'password.required' => 'The password field is required.',
        'password.string' => 'The password must be a string.',
        'password.min' => 'The password field must be at least 8 characters long and contain symbols, numbers, uppercase and lowercase letters.',
        'password_confirmation.required' => 'The password confirmation field is required.',
        'password_confirmation.same' => 'The password confirmation does not match.',
    ],
    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'email' => 'email',
        'phone' => 'phone',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
    ],
];
