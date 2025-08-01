@php
    $user = auth()->user();
@endphp
<aside class="sidebar-v3">
    <div class="agent-contact-card-v3 card-v3 sticky-sidebar-v3">
        <h4>{{ __('app/properties.contact_agent_title') }}</h4>

        <div class="agent-info-v3">
            <img src="{{ $agent['image_url'] }}"
                alt="{{ __('app/properties.agent_avatar_alt', ['name' => $agent['name']]) }}" class="agent-avatar-v3">
            <div>
                <h5>{{ $agent['name'] }}</h5>
                {{-- Display agent email if available --}}
                @if ($agent['email'] ?? false)
                    <div class="agent-details-v3 mt-1">
                        <div class="agent-email-v3">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $agent['email'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- NEW: Unified status notice for blacklisted agents --}}
        @if ($agent['is_blacklisted'] ?? false)
            <div class="agent-status-notice-v3">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>{{ __('app/properties.agent_under_review_title', ['name' => $agent['name']]) }}</strong>
                    <p>{{ __('app/properties.agent_under_review_message') }}</p>

                    {{-- Display the reason if it exists --}}
                    @if ($agent['blacklist_reason'] ?? false)
                        <p class="reason-text">
                            <strong>{{ __('app/properties.reason_label') }}:</strong> {{ $agent['blacklist_reason'] }}
                        </p>
                    @endif

                    <p class="support-text">
                        {!! __('app/properties.contact_support_for_help', ['url' => route('contact-us')]) !!}
                    </p>
                </div>
            </div>
        @endif

        {{-- Contact form is now always visible, with a separator line --}}
        <form id="contactAgentFormV3">
            @auth
                <input type="hidden" name="property_id" value="{{ $propertyId }}">
                <div class="form-group-v3">
                    <textarea name="message" rows="3" placeholder="{{ __('app/properties.whatsapp_default_message') }}">
                        {{ __('app/properties.whatsapp_default_message', [
                            'property_title' => $property['location'],
                        ]) }}
                    </textarea>
                </div>
                <button type="submit" class="btn-v3 btn-v3-primary btn-v3-block">
                    {{ __('app/properties.send_button') }}
                </button>
            @endauth

            @isset($agent['mobile'])
                <div class="agent-action-buttons-v3">
                    <button type="button" class="btn-v3 btn-v3-outline show-mobile-btn">
                        <i class="fas fa-phone-alt"></i>
                        {{ __('app/properties.show_number_button') }}
                    </button>
                    <a href="#" id="whatsappLinkV3" class="btn-v3 btn-v3-success whatsapp-btn-v3" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i>
                        {{ __('app/properties.whatsapp_button') }}
                    </a>
                </div>
            @endisset
        </form>

    </div>
</aside>
