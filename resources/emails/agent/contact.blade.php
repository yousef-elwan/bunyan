@component('mail::message')
    # New Property Inquiry: {{ $details['propertyName'] }}

    Hello {{ $agentName }},

    You have received a new inquiry regarding the property: **{{ $details['propertyName'] }}**.
    Property Link: [View Property]({{ $details['propertyUrl'] }})

    **Sender Details:**
    - **Name:** {{ $details['name'] }}
    - **Email:** {{ $details['email'] }}
    - **Phone:** {{ $details['userPhoneFull'] }}

    **Message:**
    {{ $details['message'] }}

    @if ($details['loggedInUserId'])
        This message was sent by a logged-in user (ID: {{ $details['loggedInUserId'] }}).
    @else
        This message was sent by a guest.
    @endif

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
