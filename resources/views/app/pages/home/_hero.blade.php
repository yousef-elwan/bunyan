<div x-data="searchFilters({
    initialTypeId: '{{ addslashes(request()->input('type_id', '')) }}',
    initialCategoryId: '{{ addslashes(request()->input('category_id', '')) }}',
    initialCityId: '{{ addslashes(request()->input('city_id', '')) }}',
    initialRooms: {{ request()->filled('rooms') && is_numeric(request()->input('rooms')) ? request()->input('rooms') : 'null' }},
    initialMinPrice: {{ request()->filled('price_min') && is_numeric(request()->input('price_min')) ? request()->input('price_min') : 'null' }},
    initialMaxPrice: {{ request()->filled('price_max') && is_numeric(request()->input('price_max')) ? request()->input('price_max') : 'null' }},
    initialMinFloor: {{ request()->filled('floor_min') && is_numeric(request()->input('floor_min')) ? request()->input('floor_min') : 'null' }},
    initialMaxFloor: {{ request()->filled('floor_max') && is_numeric(request()->input('floor_max')) ? request()->input('floor_max') : 'null' }},
    initialMinArea: {{ request()->filled('area_min') && is_numeric(request()->input('area_min')) ? request()->input('area_min') : 'null' }},
    initialMaxArea: {{ request()->filled('area_max') && is_numeric(request()->input('area_max')) ? request()->input('area_max') : 'null' }},
    initialConditionId: '{{ addslashes(request()->input('condition_id', '')) }}',
    initialFloorId: '{{ addslashes(request()->input('floor_id', '')) }}',
    initialOrientationId: '{{ addslashes(request()->input('orientation_id', '')) }}',
    initialAmenities: '{{ addslashes(implode(',', array_map('strval', request()->input('amenities', [])))) }}'
})">

    <!-- Hero Section -->
    <section class="hero-section" style="background-image: url('{{ asset('images/slider/2.png') }}');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            {{-- ... (Hero title and subtitle - unchanged) ... --}}
            <h1 class="animated-hero-title" data-aos="fade-up" data-aos-once="true" data-aos-delay="100">
                <span class="static-hero-text">
                    {{ trans('app/home.hero.animate_text.static_text') }}
                </span>
                <br>
                <span class="tf-text s1 cd-words-wrapper">
                    <b class="item-text is-visible"></b>
                    <span
                        data-words="{{ implode(',', [
                            trans('app/home.hero.animate_text.text-1'),
                            trans('app/home.hero.animate_text.text-2'),
                            trans('app/home.hero.animate_text.text-3'),
                            trans('app/home.hero.animate_text.text-4'),
                        ]) }}"
                        style="display:none;"></span>
                </span>
            </h1>
            <p class="subtitle" data-aos="fade-up" data-aos-delay="300">
                {!! nl2br(__('app/home.hero.small_text')) !!}
            </p>

            <form class="hero-search-form" @submit.prevent="submitFilters('hero')" data-aos="fade-up"
                data-aos-delay="500">
                {{-- ... (Hero search tabs and fields - unchanged, Alpine x-model bindings remain) ... --}}
                <div class="search-tabs">
                    <button type="button" class="tab-btn"
                        :class="{ 'active': filters.type_id === '' || filters.type_id === null }"
                        @click="filters.type_id = ''">
                        {{ __('app/home.hero.search.all') }}
                    </button>
                    @foreach ($types as $value)
                        <button type="button" class="tab-btn"
                            :class="{ 'active': filters.type_id == '{{ $value['id'] }}' }"
                            @click="filters.type_id = '{{ $value['id'] }}'">
                            {{ $value['name'] }}
                        </button>
                    @endforeach
                </div>
                <div class="search-fields">
                    <div class="form-group">
                        <select x-model="filters.category_id">
                            <option value="">{{ __('app/home.hero.search.all_category') }}</option>
                            @foreach ($categories as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select x-model="filters.city_id">
                            <option value="">{{ __('app/home.hero.search.all_city') }}</option>
                            @foreach ($cities as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="number" x-model.number="filters.rooms"
                            placeholder="{{ __('app/home.hero.search.rooms_count') }}">
                    </div>
                    <div class="form-group form-group-actions">
                        {{-- This link will now be targeted by Vanilla JS --}}
                        <a href="#" class="advanced-search-link" id="openAdvancedSearchModal">
                            <i class="fas fa-sliders-h"></i>
                            {{ trans('app/home.hero.search.advance_filter') }}
                        </a>
                        <button type="submit" class="search-submit-btn">
                            {{ trans('app/home.hero.search.search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Advanced Search Modal (Vanilla JS will control .active class) -->
    <div class="modal-overlay" id="advancedSearchOverlay"></div>
    <div class="modal advanced-search-modal" id="advancedSearchModal" role="dialog" aria-modal="true"
        aria-labelledby="advancedSearchModalTitle">
        <div class="modal-header">
            <h2 class="modal-title" id="advancedSearchModalTitle">
                {{ trans('app/home.hero.search.advance_filter') }}
            </h2>
            {{-- This button will now be targeted by Vanilla JS --}}
            <button class="modal-close-btn" id="closeAdvancedSearchModal" aria-label="{{ trans('app/home.close') }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="advancedSearchFormInternal" @submit.prevent="submitFilters('modal')">
                {{-- ... (Modal form content - unchanged, Alpine x-model bindings remain) ... --}}
                <div class="adv-search-row top-controls">
                    <div class="adv-form-group status-tabs">
                        <label> {{ __('app/home.hero.search.type') }}:</label>
                        <button type="button" class="tab-btn adv-status-btn"
                            :class="{ 'active': filters.type_id === '' || filters.type_id === null }"
                            @click="filters.type_id = ''">
                            {{ __('app/home.hero.search.all') }}
                        </button>
                        @foreach ($types as $value)
                            <button type="button" class="tab-btn adv-status-btn"
                                :class="{ 'active': filters.type_id == @json($value['id']) }"
                                @click="filters.type_id = @json($value['id'])">
                                {{ $value['name'] }}
                            </button>
                        @endforeach
                    </div>
                    <div class="adv-form-group">
                        <label for="adv-category">{{ __('app/home.hero.search.category') }}</label>
                        <select id="adv-category" x-model="filters.category_id">
                            <option value="">{{ __('app/home.hero.search.all_category') }}</option>
                            @foreach ($categories as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adv-form-group">
                        <label for="adv-city">{{ __('app/home.hero.search.city') }}</label>
                        <select id="adv-city" x-model="filters.city_id">
                            <option value="">{{ __('app/home.hero.search.all_city') }}</option>
                            @foreach ($cities as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adv-form-group">
                        <label for="adv-rooms">{{ __('app/home.hero.search.rooms_count') }}</label>
                        <input type="number" id="adv-rooms" x-model.number="filters.rooms"
                            placeholder="{{ __('app/home.hero.search.rooms_count') }}">
                    </div>
                </div>
                <hr class="adv-search-divider">
                <div class="adv-search-row">
                    <div class="adv-form-group price-range">
                        <label>{{ __('app/home.hero.search.price') }}:</label>
                        <div class="input-pair">
                            <input type="text" x-model="filters.price_min" x-mask:dynamic="$money($input, ',')"
                                placeholder="{{ __('app/home.hero.search.price_min') }}">
                            <span>-</span>
                            <input type="text" x-model="filters.price_max" x-mask:dynamic="$money($input, ',')"
                                placeholder="{{ __('app/home.hero.search.price_max') }}">
                        </div>
                    </div>
                    <div class="adv-form-group area-range">
                        <label>{{ __('app/home.hero.search.area') }}:</label>
                        <div class="input-pair">
                            <input type="text" x-model="filters.area_min" x-mask:dynamic="$money($input, ',')"
                                placeholder="{{ __('app/home.hero.search.area_min') }}">
                            <span>-</span>
                            <input type="text" x-model="filters.area_max" x-mask:dynamic="$money($input, ',')"
                                placeholder="{{ __('app/home.hero.search.area_max') }}">
                        </div>
                    </div>
                </div>
                <div class="adv-search-row">
                    <div class="adv-form-group">
                        <div class="adv-form-group">
                            <label>{{ __('app/home.hero.search.floor') }}:</label>
                            <div class="input-pair">
                                <select id="adv-floor" x-model="filters.floor_min">
                                    <option value="">{{ __('app/home.hero.search.floor_min') }}</option>
                                    @foreach ($floors as $floor)
                                        <option value="{{ $floor['id'] }}">{{ $floor['name'] }}</option>
                                    @endforeach
                                </select>
                                <select id="adv-floor" x-model="filters.floor_max">
                                    <option value="">{{ __('app/home.hero.search.floor_max') }}</option>
                                    @foreach ($floors as $floor)
                                        <option value="{{ $floor['id'] }}">{{ $floor['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="adv-form-group">
                        <div class="input-pair">
                            <div class="adv-form-group" style="width: 100%;">
                                <label
                                    for="adv-property-condition">{{ __('app/home.hero.search.condition') }}:</label>
                                <select id="adv-property-condition" x-model="filters.condition_id">
                                    <option value="">{{ __('app/home.hero.search.all') }}</option>
                                    @foreach ($conditions as $condition)
                                        <option value="{{ $condition['id'] }}">{{ $condition['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="adv-form-group" style="width: 100%;">
                                <label for="adv-orientation">{{ __('app/home.hero.search.orientation') }}:</label>
                                <select id="adv-orientation" x-model="filters.orientation_id">
                                    <option value="">{{ __('app/home.hero.search.all') }}</option>
                                    @foreach ($orientations as $orientation)
                                        <option value="{{ $orientation['id'] }}">{{ $orientation['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="adv-search-row">
                    {{-- <div class="adv-form-group">
                        <label for="adv-floor">{{ __('app/home.hero.search.floor') }}:</label>
                        <select id="adv-floor" x-model="filters.floor_id">
                            <option value="">{{ __('app/home.hero.search.all') }}</option>
                            @foreach ($floors as $floor)
                                <option value="{{ $floor['id'] }}">{{ $floor['name'] }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                </div>
                <hr class="adv-search-divider">
                <div class="adv-search-row features-amenities">
                    <label class="section-label">{{ __('app/home.hero.search.amenities') }}:</label>
                    <div class="checkbox-grid">
                        @foreach ($amenities as $amenity)
                            <div class="checkbox-group">
                                <input type="checkbox" :value="{{ $amenity['id'] }}" value="{{ $amenity['id'] }}"
                                    id="amenity_{{ $amenity['id'] }}" x-model="filters.amenities">
                                <label :for="'amenity_{{ $amenity['id'] }}'">{{ $amenity['name'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="adv-search-btn-reset" @click="resetAdvancedFilters()">
                {{ trans('app/home.hero.search.reset') }}
            </button>
            <button type="submit" form="advancedSearchFormInternal" class="adv-search-btn-submit">
                <i class="fas fa-search"></i> {{ trans('app/home.hero.search.search') }}
            </button>
        </div>
    </div>

</div> <!-- End of x-data wrapper -->
