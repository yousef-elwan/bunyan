<section class="categories-section-carousel">
    <div class="container">
        <h2 class="section-title">
            {{ __('app/home.lists.cities_title') }}
        </h2>
        <div class="swiper category-swiper">
            <div class="swiper-wrapper">
                @foreach ($cities as $value)
                    <div class="swiper-slide">
                        <a href="{{ route('search', ['city_id' => $value['id']]) }}" class="old-design-category-card">
                            <div class="odcc-icon-wrapper">
                                <img src="<?= $value['image_url'] ?>" alt=" <?= $value['name'] ?>" loading="lazy">
                            </div>
                            <h3 class="odcc-name">
                                <?= $value['name'] ?>
                            </h3>
                            <p class="odcc-count">
                                (<?= $value['properties_count'] ?? 0 ?>)
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
