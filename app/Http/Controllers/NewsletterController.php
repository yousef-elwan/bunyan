<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterConfirmation;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            ['token' => Str::random(64)]
        );

        if (!$subscriber->wasRecentlyCreated && $subscriber->is_active) {
            return response()->json([
                'message' => __('messages.newsletter.you_are_already_subscribed')
            ], 400);
        }

        Mail::to($request->email)->send(new NewsletterConfirmation($subscriber));

        return back()->with('success', __('messages.newsletter.pls_check_email'));
    }

    public function confirm(Request $request, string $token)
    {

        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'is_active' => true,
            'verified_at' => now(),
            'token' => null
        ]);

        return redirect()->route('home')->with('success', __('messages.newsletter.subscription_confirmed'));
    }


    public function unsubscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        NewsletterSubscriber::where('email', $request->email)
            ->update(['is_active' => false]);

        return back()->with('success', __('messages.newsletter.unsubscribed'));
    }
}
