<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Newsletter Confirmation') }}</title>
</head>

<body>
    <h2>{{ __('Confirm Your Subscription') }}</h2>
    <p>{{ __('Click the button below to confirm your newsletter subscription:') }}</p>

    <a href="{{ route('newsletter.confirm', $subscriber->token) }}"
        style="background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        {{ __('Confirm Subscription') }}
    </a>

    <p>{{ __('If you did not request this, no further action is required.') }}</p>
</body>

</html>
