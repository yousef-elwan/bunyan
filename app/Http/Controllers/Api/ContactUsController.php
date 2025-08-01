<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Contact;

class ContactUsController extends Controller
{

    public function __construct() {}

    public function store(SendMessageRequest $request)
    {
        try {
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
            return successResponse(
                message: __('messages.contact.message_sent_success'),
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: __('messages.contact.message_sent_failed')
            );
        }
    }
}
