<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-column about-us">
            <a href="{{ route('home') }}">
                <img src="{{ $web_config['company_logo'] }}" alt="{{ $web_config['company_name'] }}" class="footer-logo"
                    loading="lazy">
            </a>
            <p>
                {{ __('app/layouts.footer.small_text', ['region' => $web_config['company_name']]) }}
            </p>
            <div class="social-media-icons">

                @isset($web_config['youtube_link'])
                    <a href="{{ $web_config['youtube_link'] }}" aria-label="Youtube">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endisset

                @isset($web_config['instagram_link'])
                    <a href="{{ $web_config['instagram_link'] }}" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endisset

                @isset($web_config['facebook_link'])
                    <a href="{{ $web_config['facebook_link'] }}" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                @endisset

            </div>
        </div>
        <div class="footer-column quick-links">
            <h4>
                {{ __('app/layouts.footer.quick_links') }}
            </h4>
            <ul>
                <li>
                    <a href="{{ route('contact-us') }}">
                        {{ __('app/layouts.footer.contact') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('terms-of-use') }}">
                        {{ __('app/layouts.footer.terms_of_use') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('privacy-policy') }}">
                        {{ __('app/layouts.footer.privacy_policy') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('terms-of-service') }}">
                        {{ __('app/layouts.footer.termofservice') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="footer-column newsletter">
            <h4>
                {{ __('app/layouts.footer.newsletter_title') }}
            </h4>
            <p>
                {{ __('app/layouts.footer.newsletter_text') }}
            </p>
            {{-- <form class="newsletter-form">
                <input type="email" placeholder="Your email address">
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form> --}}
            <form class="newsletter-form" id="newsletterForm">
                @csrf
                <input type="email" name="email" id="newsletterEmail" placeholder="Your email address" required>
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <p>©{{ date('Y') }} {{ $web_config['company_name'] ?? '' }}. {{ __('app/layouts.footer.copyright') }}.
        </p>
    </div>
</footer>
