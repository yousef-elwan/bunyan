<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('emails.new_property.new_property_added') }}</title>
</head>

<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right;">
    <h2>{{ __('emails.new_property.new_property_added') }}</h2>

    <p>{{ __('emails.new_property.property_details') }}</p>
    <ul>
        <li><strong>{{ __('emails.new_property.city') }}:</strong> {{ $property->city->name }}</li>
        <li><strong>{{ __('emails.new_property.price') }}:</strong> {{ $property->price_display }} </li>
    </ul>

    @if (count($images))
        <h4>📸 {{ __('emails.new_property.property_images') }}</h4>
        @foreach ($images as $img)
            <img src="{{ $img }}" alt="{{ __('emails.new_property.property_image') }}" width="200"
                style="margin: 5px; border: 1px solid #ccc;">
        @endforeach
    @endif

    @if ($property->latitude && $property->longitude)
        <h4>📍 {{ __('emails.new_property.location_map') }}</h4>
        <img src="https://staticmap.openstreetmap.de/staticmap.php?center={{ $property->latitude }},{{ $property->longitude }}&zoom=15&size=600x300&maptype=mapnik&markers={{ $property->latitude }},{{ $property->longitude }},red-pushpin"
            alt="{{ __('emails.new_property.map') }}" width="100%" style="max-width: 600px;">
    @endif

    <h3>📝 {{ __('emails.new_property.description') }}</h3>
    <p>{!! nl2br(e($property->content)) !!}</p>

    <p>📞 {{ __('emails.new_property.contact_us') }}</p>

    <p>{{ __('emails.new_property.regards') }}</p>


    <div style="text-align:center; margin-top: 12px;">
        <a href="{{ route('properties.details', [
            'locale' => app()->getLocale(),
            'property' => $property->id,
        ]) }}"
            style="background: #dc2626; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            {{ __('emails.new_property.view_property') }}
        </a>
    </div>
</body>

</html>
