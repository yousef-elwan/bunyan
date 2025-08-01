    <script>
        window.AppConfig = {
            // 1. البيانات الأساسية
            locale: '{{ app()->getLocale() }}',
            baseUrl: '{{ url('/') }}',
            // csrfToken: '{{ csrf_token() }}',
            apiToken: "{{ session('api_token') }}",
            sound_message: "{{ asset('sound/happy-message-ping-351298.mp3') }}",
            sound_pop: "{{ asset('sound/bubble-pop-2-293341.mp3') }}",
            isRTL: {{ app()->getLocale() === 'ar' ? 'true' : 'false' }}, // مثال أبسط لـ isRTL
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
            isAdmin: {{ Auth::check() ? (auth()->user()->is_admin ? 'true' : 'false') : 'false' }},
            locale: '{{ app()->getLocale() }}',
            // 2. بيانات المستخدم (إذا مسجل دخوله)
            @auth
            user: {
                id: {{ auth()->user()->id }},
                name: '{{ Str::limit(auth()->user()->name, 50) }}',
                first_name: '{{ auth()->user()->first_name }}',
                last_name: '{{ auth()->user()->last_name }}',
                email_notifications: '{{ auth()->user()->email_notifications }}',
                newsletter_notifications: '{{ auth()->user()->newsletter_notifications }}',
                email: '{{ auth()->user()->email }}',
                mobile: '{{ auth()->user()->mobile }}',
            },
        @endauth

        i18n: {!! json_encode(__('js/app')) !!},
            routes: {
                'home': "{{ route('home') }}",
                'contact-us': "{{ route('contact-us') }}",
                'auth.logout': "{{ route('api.auth.web.logout') }}",
                'newsletter.subscribe': "{{ route('newsletter.subscribe') }}",
                'login': "{{ route('auth.login') }}",
            },
            settings: {},
            pageData: {}
        };
    </script>
