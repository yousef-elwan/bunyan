<div x-data="searchPageManager('{{ $layout }}')" x-init="init" id="wrapper">
    <div class="page-title-bar">
        <header class="top-filter-bar">
            <!-- These controls are now wired directly to Livewire properties -->
            <div class="filter-controls">
                <select wire:model.live="category">
                    <option value="">{{ trans('app/search.all_categories') }}</option>
                    @foreach ($categories as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="type">
                    <option value="">{{ trans('app/search.all_types') }}</option>
                    @foreach ($types as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="governorate">
                    <option value="">{{ trans('app/search.all_city') }}</option>
                    @foreach ($governorates as $value)
                        <option value="{{ $value->name }}">{{ $value->name }}</option>
                    @endforeach
                </select>
                <button @click="sidebarOpen = true" class="filter-button" id="toggleFilterSidebar">
                    <i class="fas fa-filter"></i> {{ trans('app/search.filters') }}
                </button>
            </div>

            <!-- This custom dropdown is now managed by Alpine for the UI interaction -->
            <div class="action-buttons">
                <div id="customLayoutDropdown" class="custom-dropdown" @click.away="layoutDropdownOpen = false"
                    tabindex="0">
                    <div class="custom-dropdown-selected" @click="layoutDropdownOpen = !layoutDropdownOpen"
                        id="customLayoutSelected">
                        <i :class="currentLayoutIcon"></i> <span x-text="currentLayoutText"></span>
                    </div>
                    <ul class="custom-dropdown-list" x-show="layoutDropdownOpen" x-transition>
                        <li @click="$wire.set('layout', 'row'); layoutDropdownOpen = false;">
                            <i class="fas fa-grip-lines"></i> {{ trans('app/search.layout_row') }}
                        </li>
                        <li @click="$wire.set('layout', 'grid'); layoutDropdownOpen = false;">
                            <i class="fas fa-th-large"></i> {{ trans('app/search.layout_grid') }}
                        </li>
                        <li @click="$wire.set('layout', 'map'); layoutDropdownOpen = false;">
                            <i class="fas fa-map-marked-alt"></i> {{ trans('app/search.layout_map') }}
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="listings-info">
            <div wire:loading.delay>
                <span class="text-gray-500">{{ trans('app/search.loading') }}</span>
            </div>
            <div wire:loading.remove>
                @if ($properties->total() === 0)
                    <span class="text-red-500">{{ trans('app/search.empty') }}</span>
                @else
                    <span class="text-green-600">
                        {{ trans('app/search.showing_results', ['shown' => $properties->count(), 'total' => $properties->total()]) }}
                    </span>
                @endif
            </div>

            <select wire:model.live="sortKey">
                <option value="">{{ trans('app/search.sort_default') }}</option>
                <option value="created_at_des">{{ trans('app/search.sort_newest') }}</option>
                <option value="price_asc">{{ trans('app/search.sort_price_asc') }}</option>
                <option value="price_des">{{ trans('app/search.sort_price_desc') }}</option>
                <option value="size_asc">{{ trans('app/search.sort_size_asc') }}</option>
                <option value="size_des">{{ trans('app/search.sort_size_desc') }}</option>
            </select>
        </div>
    </div>

    <main class="content-area" id="contentArea" :style="mainContentStyle">
        <!-- Listings Section -->
        <section class="listings-container" id="listingsContainer" :style="listingsContainerStyle">
            <div id="propertyCardsContainer" class="property-cards-grid" :class="cardContainerClass">
                <!-- Data is now looped directly from Livewire's paginated collection -->
                @forelse ($properties as $property)
                    <a href="{{ route('properties.details', $property->id) }}">
                        <article class="property-card cursor-pointer hover:shadow-lg transition-shadow duration-200">
                            <div class="property-image">
                                <img src="{{ $property->image_url }}" alt="{{ $property->title }}">
                            </div>
                            <div class="property-details">
                                <div class="property-price-actions">
                                    <p class="price">{{ number_format($property->price, 0) }} $</p>
                                    <div class="actions">
                                        <button class="icon-button"><i class="far fa-heart"></i></button>
                                    </div>
                                </div>
                                <p class="features">{{ $property->rooms_count }} {{ trans('app/search.rooms') }} •
                                    {{ $property->size }} {{ trans('app/search.area') }}</p>
                                <p class="address">{{ $property->location }}</p>
                            </div>
                        </article>
                    </a>
                @empty
                    <!-- No results message is handled in the listings-info bar now -->
                @endforelse
            </div>
            <div class="pagination-container" wire:loading.remove>
                {{ $properties->links() }}
            </div>
        </section>

        <!-- Map Section (wire:ignore is critical) -->
        <section class="map-container" id="mapContainer" :style="mapContainerStyle" wire:ignore>
            <div id="map" x-ref="map" style="height: 100%; width: 100%;"></div>
        </section>
    </main>

    <!-- Filter Sidebar (Alpine controls its visibility) -->
    <aside class="filter-sidebar" id="filterSidebar" :class="{ 'open': sidebarOpen }"
        @keydown.escape.window="sidebarOpen = false">
        <div class="sidebar-header">
            <h2>{{ trans('app/search.filters') }}</h2>
            <button @click="sidebarOpen = false" id="closeFilterSidebar" class="close-button">×</button>
        </div>
        <div class="sidebar-content">
            <!-- All form inputs are now wired to Livewire -->
            <div class="form-style mt-4"><label>{{ trans('app/search.floor') }}</label>
                <select wire:model.live="floor">
                    <option value="">Select</option>
                    @foreach ($floors as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-style mt-4"><label>{{ trans('app/search.condition') }}</label>
                <select wire:model.live="condition">
                    <option value="">Select</option>
                    @foreach ($conditions as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-style mt-4"><label>{{ trans('app/search.orientation') }}</label>
                <select wire:model.live="orientation">
                    <option value="">Select</option>
                    @foreach ($orientations as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mt-4"><label>{{ trans('app/search.rooms') }}:</label>
                <input type="number" wire:model.live.debounce.500ms="rooms_count" min="0" class="form-input">
            </div>
            <div class="form-group mt-4"><label>{{ trans('app/search.price') }}:</label>
                <div class="range-inputs">
                    <input type="number" wire:model.live.debounce.500ms="price_min" placeholder="Min">
                    <span>-</span>
                    <input type="number" wire:model.live.debounce.500ms="price_max" placeholder="Max">
                </div>
            </div>
            <div class="form-group mt-4"><label>{{ trans('app/search.area') }}:</label>
                <div class="range-inputs">
                    <input type="number" wire:model.live.debounce.500ms="area_min" placeholder="Min">
                    <span>-</span>
                    <input type="number" wire:model.live.debounce.500ms="area_max" placeholder="Max">
                </div>
            </div>
            @if (count($propertyAttributes) > 0)
                <div class="form-group mt-4">
                    @foreach ($propertyAttributes as $attribute)
                        <div class="form-style mt-2">
                            <label>{{ $attribute->name }}</label>
                            <select wire:model.live="attributeValues.{{ $attribute->id }}">
                                <option value="">Select</option>
                                @foreach ($attribute->customAttributeValues as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="form-style wd-amenities mt-4">
                <h6 class="title">{{ trans('app/search.amenities') }}:</h6>
                <div class="group-amenities">
                    @foreach ($allAmenities as $amenity)
                        <fieldset class="amenities-item">
                            <label for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
                            <input type="checkbox" wire:model.live="amenities" value="{{ $amenity->id }}"
                                id="amenity-{{ $amenity->id }}">
                        </fieldset>
                    @endforeach
                </div>
            </div>
            <div class="form-style mt-4">
                <button @click="sidebarOpen = false" class="tf-btn btn-view primary w-100">
                    {{ trans('app/search.search') }}
                </button>
            </div>
        </div>
    </aside>
</div>
