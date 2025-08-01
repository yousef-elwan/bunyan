<section class="featured-properties-section">
    <div class="container">
        <div class="section-header" data-aos="fade-right">
            <h2 class="section-title">
                {{ $title }}
            </h2>

            @isset($allText)
                @if (isset($allUrl))
                    <a href="{{ $allUrl }}" class="text-subtitle text-primary">
                        {{ $allText }}
                    </a>
                @else
                    <div class="text-subtitle text-primary">
                        {{ $allText }}
                    </div>
                @endif
            @endisset

        </div>
        <div class="swiper properties-swiper">
            <div class="swiper-wrapper">
                @foreach ($properties as $property)
                    <div class="swiper-slide" data-aos="fade-up" data-aos-duration="800">
                        @include('app.components.property-card', ['property' => $property])
                    </div>
                @endforeach
            </div>

            <div class="swiper-pagination properties-swiper-pagination">

            </div>
            <div class="swiper-button-prev properties-swiper-button-prev">

            </div>
            <div class="swiper-button-next properties-swiper-button-next">

            </div>
        </div>
    </div>
</section>
