import Alpine from 'alpinejs';
import { getRoute } from '../../utils/helpers';
import { http } from '../../utils/api';
import mask from '@alpinejs/mask';


// --- Helper Functions ---
function updateCssVariables() {
    const mainHeader = document.querySelector('.app-header'); // Use a class that your main header has
    const headerHeight = mainHeader ? mainHeader.offsetHeight : 0;
    const topBar = document.querySelector('#property-search-page .page-title-bar');
    const topBarHeight = topBar ? topBar.offsetHeight : 110;

    const root = document.documentElement;
    root.style.setProperty('--header-height', `${headerHeight}px`);
    root.style.setProperty('--top-bar-total-height', `${topBarHeight}px`);
}

function isMobileView() {
    return window.innerWidth <= 992;
}

// --- Event Listeners ---
window.addEventListener('load', updateCssVariables);
window.addEventListener('resize', () => {
    clearTimeout(window.resizeTimer);
    window.resizeTimer = setTimeout(updateCssVariables, 250);
});


// --- Leaflet Map Initialization ---
const map = L.map('map', { scrollWheelZoom: false }).setView([35.0, 38.0], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
const markers = L.markerClusterGroup();
map.addLayer(markers);

function updateMapMarkers(properties = []) {
    markers.clearLayers();
    properties.forEach(item => {
        if (item.latitude && item.longitude) {
            const url = getRoute('properties.details', {
                'property': item.id
            })
            const popupContent = `
                <a href="${url}" style="text-decoration:none; color:inherit;">
                    <div style="width:180px;">
                        <img src="${item.image_url}" style="width:100%; height:100px; object-fit:cover; border-radius:4px;" alt="${item.title}">
                        <div style="font-weight:bold; margin-top:5px;">
                            ${item.price_display}
                        </div>
                        <div style="font-size:12px; color:#555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${item.location}
                        </div>
                    </div>
                </a>`;
            markers.addLayer(L.marker([item.latitude, item.longitude]).bindPopup(popupContent));
        }
    });
}

function fitToMarkers() {
    if (markers.getLayers().length > 0) {
        const bounds = markers.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// --- Alpine.js Component ---
function propertySearch() {
    return {
        // --- State ---
        properties: [],
        pagination: null,
        loading: true,
        loadingMore: false,
        page: 1,
        perPage: 12,
        limitOnMap: 200,
        showMapWarning: false,
        resultCount: null,
        isCounting: false,

        // --- Filters ---
        category: null,
        type: null,
        governorate: null,
        price_min: null,
        price_max: null,
        floor_min: null,
        floor_max: null,
        area_min: null,
        area_max: null,
        rooms_count: null,
        amenities: [],
        latitudeRange: null,
        longitudeRange: null,

        // --- UI ---
        sortKey: '',
        sort: [],
        layout: 'row',
        reachedEnd: false,

        init() {
            // Read initial state from URL parameters
            const params = new URLSearchParams(window.location.search);
            this.category = params.get('category_id') || null;
            this.type = params.get('type_id') || null;
            this.governorate = params.get('city_id') || null;
            this.price_min = params.get('price_min') || null;
            this.price_max = params.get('price_max') || null;
            this.floor_min = params.get('floor_min') || null;
            this.floor_max = params.get('floor_max') || null;
            this.area_min = params.get('area_min') || null;
            this.area_max = params.get('area_max') || null;
            this.rooms_count = params.get('rooms_count') || null;
            this.amenities = params.get('amenities') ? params.get('amenities').split(',') : [];
            this.sortKey = params.get('sort') || '';
            this.layout = params.get('layout') || 'row';

            this.checkLayoutOnResize(); // Check on initial load
            this.handleLayoutChange()
            this.updateSort(false);
            this.fetchProperties({ firstLoad: true, resetPage: true });

            const debouncedFetchCount = debounce(() => this.fetchResultCount(), 500);

            [
                'category',
                'type',
                'governorate',
                'price_min',
                'price_max',
                'floor_min',
                'floor_max',
                'area_min',
                'area_max',
                'rooms_count',
                'amenities'
            ].forEach(prop => {
                this.$watch(prop, debouncedFetchCount);
            });

            // --- Event Listeners ---
            this.$watch('sortKey', () => { this.updateSort(); });

            window.addEventListener('resize', debounce(() => {

                this.checkLayoutOnResize();
            }, 250));

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && document.body.classList.contains('no-scroll')) {
                    this.closeSidebar();
                }
            });

            map.on('moveend', debounce(() => {
                if (this.layout !== 'grid') {
                    const bounds = map.getBounds();
                    this.latitudeRange = [bounds.getSouth(), bounds.getNorth()];
                    this.longitudeRange = [bounds.getWest(), bounds.getEast()];
                    this.fetchProperties({ resetPage: true });
                }
            }, 500));


            const handleScroll = () => {
                if (this.layout !== 'grid' || this.loadingMore) return;
                const cardsContainer = document.getElementById('propertyCardsContainer');
                if (!cardsContainer) return;

                const bottomOffset = 50;
                const rect = cardsContainer.getBoundingClientRect();
                const isNearBottom = rect.bottom <= (window.innerHeight + bottomOffset);

                if (isNearBottom) {
                    if (!this.reachedEnd) {
                        this.loadMore();
                    } else {
                    }
                }
            };

            window.addEventListener('scroll', debounce(handleScroll, 100));

        },

        getAppliedFilters(includeMapBounds = true) {

            const stateCopy = { ...this };
            const numericFields = [
                'price_min', 'price_max',
                'area_min', 'area_max'
            ];
            numericFields.forEach(field => {
                if (stateCopy[field] && typeof stateCopy[field] === 'string') {
                    // إزالة الفواصل
                    stateCopy[field] = stateCopy[field].replace(/,/g, '');
                    // تحويل إلى عدد إذا كان يحتوي على أرقام
                    if (stateCopy[field] !== '') {
                        stateCopy[field] = parseFloat(stateCopy[field]);
                    } else {
                        stateCopy[field] = null;amenities
                    }
                }
            });

            console.log(stateCopy)
            let filters = [];
            filters.push({ id: 'is_blacklist', filterFns: 'equals', value: false });
            if (stateCopy.category) filters.push({ id: 'category_id', filterFns: 'equals', value: stateCopy.category });
            if (stateCopy.type) filters.push({ id: 'type_id', filterFns: 'equals', value: stateCopy.type });
            if (stateCopy.governorate) filters.push({ id: 'city_id', filterFns: 'equals', value: stateCopy.governorate });
            if (stateCopy.rooms_count) filters.push({ id: 'rooms_count', filterFns: 'equals', value: stateCopy.rooms_count });
            if (stateCopy.price_min) filters.push({ id: 'price', filterFns: 'greaterThanOrEqualTo', value: stateCopy.price_min });
            if (stateCopy.price_max) filters.push({ id: 'price', filterFns: 'lessThanOrEqualTo', value: stateCopy.price_max });
            if (stateCopy.floor_min) filters.push({ id: 'cached_floor_value', filterFns: 'greaterThanOrEqualTo', value: stateCopy.floor_min });
            if (stateCopy.floor_max) filters.push({ id: 'cached_floor_value', filterFns: 'lessThanOrEqualTo', value: stateCopy.floor_max });
            if (stateCopy.area_min) filters.push({ id: 'size', filterFns: 'greaterThanOrEqualTo', value: stateCopy.area_min });
            if (stateCopy.area_max) filters.push({ id: 'size', filterFns: 'lessThanOrEqualTo', value: stateCopy.area_max });
            if (stateCopy.amenities?.length > 0) filters.push({ id: 'amenities', filterFns: 'arrIncludesAll', value: JSON.stringify(stateCopy.amenities) });
            if (includeMapBounds && stateCopy.layout !== 'grid' && stateCopy.latitudeRange && stateCopy.longitudeRange) {
                filters.push({ id: 'latitude', filterFns: 'inNumberRange', value: stateCopy.latitudeRange });
                filters.push({ id: 'longitude', filterFns: 'inNumberRange', value: stateCopy.longitudeRange });
            }

            return filters;
        },

        async fetchProperties({ firstLoad = false, loadMore = false, resetPage = false, checkForUpdate = false } = {}) {
            // if (resetPage) this.page = 1;
            if (resetPage) {
                this.page = 1;
                this.reachedEnd = false;
            }
            if (loadMore || checkForUpdate) {
                this.loadingMore = true
            } else {
                this.loading = true
            }
            this.setParams();

            const filters = this.getAppliedFilters(!firstLoad);
            const currentPerPage = (this.layout === 'grid') ? this.perPage : this.limitOnMap;

            try {
                const params = new URLSearchParams({
                    page: this.page,
                    perPage: currentPerPage,
                    filters: btoa(JSON.stringify(filters)),
                    sorting: btoa(JSON.stringify(this.sort))
                });
                const response = await http().get(`${getRoute('properties.search')}?${params}`);
                const data = response.data;

                if (data.data.length > 0) {
                    if (loadMore || checkForUpdate) {
                        this.properties.push(...data.data);
                    } else {
                        this.properties = data.data;
                    }
                }

                this.pagination = data.pagination;

                if (this.layout === 'grid' && this.pagination && this.page >= this.pagination.last_page) {
                    this.reachedEnd = true;
                }

                const hasMore = data.pagination.next_page_url != null;
                this.showMapWarning = (this.layout !== 'grid' && hasMore);
                updateMapMarkers(this.properties);

                if (firstLoad && this.properties.length > 0 && this.layout !== 'grid') fitToMarkers();
            } catch (error) { console.error('[PropertySearch] API error:', error); }
            finally {
                this.loading = false;
                this.loadingMore = false;
            }
        },

        async fetchResultCount() {
            this.isCounting = true;
            this.resultCount = null;
            const filters = this.getAppliedFilters(false);
            try {
                const params = new URLSearchParams({ filters: btoa(JSON.stringify(filters)) });
                const response = await http().get(`${getRoute('properties.search')}?${params}`);
                this.resultCount = response.data.data.length;
            } catch (error) { console.error('[FetchCount] API error:', error); }
            finally { this.isCounting = false; }
        },

        setParams() {
            const url = new URL(window.location.href);

            url.searchParams.delete('category_id');
            url.searchParams.delete('type_id');
            url.searchParams.delete('city_id');
            url.searchParams.delete('price_min');
            url.searchParams.delete('price_max');
            url.searchParams.delete('area_min');
            url.searchParams.delete('area_max');
            url.searchParams.delete('floor_min');
            url.searchParams.delete('floor_max');
            url.searchParams.delete('rooms_count');
            url.searchParams.delete('amenities');
            url.searchParams.delete('sort');
            url.searchParams.delete('layout');

            if (this.category) url.searchParams.set('category_id', this.category);
            if (this.type) url.searchParams.set('type_id', this.type);
            if (this.governorate) url.searchParams.set('city_id', this.governorate);
            if (this.area_min) url.searchParams.set('area_min', this.area_min);
            if (this.area_max) url.searchParams.set('area_max', this.area_max);
            if (this.price_min) url.searchParams.set('price_min', this.price_min);
            if (this.price_max) url.searchParams.set('price_max', this.price_max);
            if (this.floor_min) url.searchParams.set('floor_min', this.floor_min);
            if (this.floor_max) url.searchParams.set('floor_max', this.floor_max);
            if (this.rooms_count) url.searchParams.set('rooms_count', this.rooms_count);
            if (this.amenities.length) url.searchParams.set('amenities', this.amenities.join(','));
            if (this.sortKey) url.searchParams.set('sort', this.sortKey);
            if (this.layout) url.searchParams.set('layout', this.layout);

            window.history.replaceState({}, '', url.href);
        },

        loadMore() {
            if (this.reachedEnd || (this.pagination && this.page >= this.pagination.last_page)) {
                this.reachedEnd = true;
                return;
            }
            this.page++;
            this.fetchProperties({ loadMore: true });
        },
        checkForUpdates() {
            if (this.pagination) {
                this.page = this.pagination.last_page + 1;
                this.fetchProperties({ checkForUpdate: true });
            }
        },
        updateSort() {

            if (this.sortKey) {

                const sortKeyLength = this.sortKey.length;
                var sortKey = this.sortKey.substring(0, sortKeyLength - 4);
                const sortType = this.sortKey.substring(sortKeyLength - 3, sortKeyLength);

                switch (sortKey) {
                    case 'newest':
                        this.sort = [
                            {
                                'id': 'created_at',
                                'desc': sortType == 'des',
                            }
                        ];
                        break;
                    case 'featured':
                        this.sort = [
                            {
                                'id': 'is_featured',
                                'desc': sortType == 'des',
                            }
                        ];
                        break;
                    case 'price':
                        this.sort = [
                            {
                                'id': 'price_on_request',
                                'desc': false,
                            },
                            {
                                'id': sortKey,
                                'desc': sortType == 'des',
                            }
                        ];
                        break;
                    default:
                        this.sort = [
                            {
                                'id': sortKey,
                                'desc': sortType == 'des',
                            }
                        ];
                        break;
                }

                this.fetchProperties({ resetPage: true });
            }
        },
        openSidebar() { document.getElementById('property-search-page').classList.add('sidebar-open'); document.body.classList.add('no-scroll'); this.fetchResultCount(); },
        closeSidebar() { document.getElementById('property-search-page').classList.remove('sidebar-open'); document.body.classList.remove('no-scroll'); },
        applyFiltersAndClose() { this.fetchProperties({ resetPage: true }); this.closeSidebar(); this.resultCount = null; },

        checkLayoutOnResize() {
            if (isMobileView() && this.layout === 'row') {
                this.layout = 'grid';
                this.handleLayoutChange();
            }
        },
        handleLayoutChange() {
            const searchPage = document.getElementById('property-search-page');
            const listingsContainer = document.getElementById('listingsContainer');
            const mapContainer = document.getElementById('mapContainer');
            const cardsContainer = document.getElementById('propertyCardsContainer');

            searchPage.classList.remove(
                'layout-row',
                'layout-grid',
                'layout-map'
            );

            searchPage.classList.add(`layout-${this.layout}`);

            listingsContainer.style.display = 'block';
            mapContainer.style.display = 'block';
            cardsContainer.classList.remove('grid-view');

            switch (this.layout) {
                case 'grid':
                    mapContainer.style.display = 'none';
                    cardsContainer.classList.add('grid-view');
                    this.latitudeRange = null;
                    this.longitudeRange = null;
                    break;

                case 'map':
                    listingsContainer.style.display = 'none';

                    if (window.innerWidth <= 992) {
                        mapContainer.style.top = `${document.querySelector('.app-header').offsetHeight}px`;
                        mapContainer.style.height = `calc(100vh - ${document.querySelector('.app-header').offsetHeight}px`;
                    }
                    break;

                case 'row':
                default:
                    if (window.innerWidth > 992) {
                        mapContainer.style.position = 'sticky';
                        mapContainer.style.top = `${document.querySelector('.page-title-bar').offsetHeight + document.querySelector('.app-header').offsetHeight}px`;
                        mapContainer.style.height = `calc(100vh - ${document.querySelector('.page-title-bar').offsetHeight + document.querySelector('.app-header').offsetHeight}px)`;
                    }
                    break;
            }

            this.fetchProperties({ resetPage: true });

            setTimeout(() => {
                map.invalidateSize();

                if (this.layout !== 'grid' && this.properties.length > 0) {
                    fitToMarkers();
                }

                if (window.innerWidth <= 992) {
                    updateCssVariables();
                }
            }, 300);

            this.setParams();
        }
    }
}

Alpine.data('propertySearch', propertySearch);
window.Alpine = Alpine;
Alpine.plugin(mask);
Alpine.start();