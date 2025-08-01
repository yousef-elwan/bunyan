<?php

return [
    '403' => [
        'title' => 'Access Denied',
        'heading' => '403 Forbidden',
        'message' => 'You do not have permission to access this page.',
        'button' => 'Back to Homepage',
        'search_button' => 'Search Properties',
    ],
    '404' => [
        'title' => 'Page Not Found',
        'heading' => "Oops, we couldn't find this page.",
        'message' => 'It seems the link you followed is old or broken. The property you were looking for might have been sold or removed from our listings.',
        'button' => 'Back to Homepage',
        'search_button' => 'Search Properties',
    ],
    '500' => [
        'title' => 'Server Error',
        'heading' => 'Oops, something went wrong.',
        'message' => 'We are currently experiencing some technical issues. Our team has been notified and we are working to fix it as soon as possible. Please try again later.',
        'button' => 'Back to Homepage',
        'status_button' => 'Check System Status',
    ]
];
