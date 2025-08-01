<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('emails.property_status_changed.subject') }}</title>
</head>

<body>
    <h2>{{ __('emails.property_status_changed.greeting', ['name' => $property->owner->name]) }}</h2>

    <p>{{ __('emails.property_status_changed.message', ['title' => $property->title]) }}</p>

    <p style="font-weight: bold;">
        {{ __('emails.property_status_changed.from_to', ['old' => $oldStatus, 'new' => $newStatus]) }}
    </p>

    <p>{{ __('emails.property_status_changed.check_panel') }}</p>
</body>

</html>
