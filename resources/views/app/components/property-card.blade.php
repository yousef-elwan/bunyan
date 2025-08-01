<a href="{{ route('properties.details', [
    'property' => $property['id'],
]) }}" style="display: contents">
    <div class="old-design-property-card">
        <div class="odpc-image-wrapper">
            <img src="{{ $property['image_url'] }}" alt="عقار رقم 1" loading="lazy">
            <span class="odpc-status-tag odpc-tag-rent">
                <?= $property['type']['name'] ?>
            </span>
        </div>
        <div class="odpc-content">
            <h3 class="odpc-title" title="<?= $property['location'] ?>">
                <?= formatDescription($property['location'], 70) ?>
            </h3>
            <p class="odpc-description">
                <?= formatDescription($property['content'], 120) ?>
            </p>
            <div class="odpc-meta">
                <span class="odpc-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= $property['city']['name'] ?>
                </span>
                <span class="odpc-area">
                    <i class="fas fa-ruler-combined"></i>
                    {{ __('app/properties.area') }}:
                    <?= $property['size'] ?>
                </span>
            </div>
        </div>
        <div class="odpc-footer">
            <span class="odpc-price">
                <?= $property['price_display'] ?>
            </span>
            <div class="odpc-details-link">
                {{ __('app/properties.details') }}
            </div>
        </div>
    </div>
</a>
