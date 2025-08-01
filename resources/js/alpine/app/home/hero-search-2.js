import Alpine from 'alpinejs'
import { getRoute } from '../../utils/helpers';
import mask from '@alpinejs/mask';

// Hero Title Text Animation (same as previous correct version)
function typeCharacterAnimation() {
    const wordsWrapper = document.querySelector('.hero-content .cd-words-wrapper');
    if (!wordsWrapper) return;
    const textElement = wordsWrapper.querySelector('b.item-text');
    const wordsDataSource = wordsWrapper.querySelector('span[data-words]');
    if (!textElement || !wordsDataSource) return;
    const words = wordsDataSource.dataset.words.split(',').map(w => w.trim()).filter(w => w.length > 0);
    if (words.length === 0) { if (textElement) textElement.textContent = ""; return; }
    if (textElement.classList.contains('is-visible') && words.length > 0) { /* Initial state */ }
    let wordIndex = 0, charIndex = 0, isDeleting = false;
    const typingSpeed = 100, deletingSpeed = 60, delayBetweenWords = 2000;
    function type() {
        if (!document.body.contains(textElement) || !textElement.offsetParent) return;
        const currentWord = words[wordIndex];
        if (isDeleting) {
            textElement.textContent = currentWord.substring(0, charIndex - 1); charIndex--;
        } else {
            textElement.textContent = currentWord.substring(0, charIndex + 1); charIndex++;
        }
        if (!isDeleting && charIndex === currentWord.length) {
            setTimeout(() => { isDeleting = true; }, delayBetweenWords);
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false; wordIndex = (wordIndex + 1) % words.length;
        }
        if (document.body.contains(textElement) && textElement.offsetParent) {
            setTimeout(type, isDeleting ? deletingSpeed : typingSpeed);
        }
    }
    if (document.body.contains(textElement) && textElement.offsetParent) {
        textElement.textContent = ''; setTimeout(type, typingSpeed);
    }
}
typeCharacterAnimation();

// --- START: Restored Vanilla JS Modal Logic ---
const openModalBtn = document.getElementById('openAdvancedSearchModal');
const closeModalBtn = document.getElementById('closeAdvancedSearchModal');
const advancedSearchModal = document.getElementById('advancedSearchModal');
const advancedSearchOverlay = document.getElementById('advancedSearchOverlay');
const bodyForModal = document.body; // Or document.documentElement for older browser compatibility with overflow

function openAdvancedSearch() {
    if (advancedSearchOverlay) advancedSearchOverlay.classList.add('active');
    if (advancedSearchModal) advancedSearchModal.classList.add('active');
    if (bodyForModal) bodyForModal.style.overflow = 'hidden';

    // Optional: Notify Alpine if it needs to know the modal is open
    // const alpineComponent = document.querySelector('[x-data^="searchFilters"]').__x;
    // if (alpineComponent) alpineComponent.data.setModalOpenState(true);
}

function closeAdvancedSearch() {
    if (advancedSearchOverlay) advancedSearchOverlay.classList.remove('active');
    if (advancedSearchModal) advancedSearchModal.classList.remove('active');
    if (bodyForModal) bodyForModal.style.overflow = '';

    // Optional: Notify Alpine
    // const alpineComponent = document.querySelector('[x-data^="searchFilters"]').__x;
    // if (alpineComponent) alpineComponent.data.setModalOpenState(false);
}

if (openModalBtn) {
    openModalBtn.addEventListener('click', e => {
        e.preventDefault();
        openAdvancedSearch();
    });
}
if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeAdvancedSearch);
}
if (advancedSearchOverlay) {
    advancedSearchOverlay.addEventListener('click', closeAdvancedSearch);
}

document.addEventListener('keydown', function (event) {
    if (event.key === "Escape" && advancedSearchModal && advancedSearchModal.classList.contains('active')) {
        closeAdvancedSearch();
    }
});


// --- END: Restored Vanilla JS Modal Logic ---

Alpine.data('searchFilters', (initialValues) => ({
    // `openModal` property is no longer used by Alpine to show/hide modal
    // We can keep it if other Alpine logic depends on knowing modal state,
    // or remove it and manage body overflow entirely in vanilla JS.
    // For simplicity, let's remove direct Alpine control over modal visibility.

    filters: { // Filter definitions remain the same
        type_id: initialValues.initialTypeId || '',
        category_id: initialValues.initialCategoryId || '',
        city_id: initialValues.initialCityId || '',
        rooms: initialValues.initialRooms,
        price_min: initialValues.initialMinPrice,
        price_max: initialValues.initialMaxPrice,
        min_floor: initialValues.initialMinFloor,
        max_floor: initialValues.initialMaxFloor,
        area_min: initialValues.initialMinArea,
        area_max: initialValues.initialMaxArea,
        condition_id: initialValues.initialConditionId || '',
        floor_id: initialValues.initialFloorId || '',
        orientation_id: initialValues.initialOrientationId || '',
        amenities: (typeof initialValues.initialAmenities === 'string' && initialValues.initialAmenities.trim() !== '')
            ? initialValues.initialAmenities.split(',').map(item => String(item.trim()))
            : [],
    },

    init() {
        // console.log('Alpine searchFilters initialized (Vanilla JS handles modal open/close):', JSON.parse(JSON.stringify(this.filters)));
    },

    // This toggleModal is no longer called by modal open/close buttons
    // It could be repurposed or removed if not needed for other Alpine state.
    // If you had `document.body.style.overflow` here, it's now in vanilla JS open/close.
    // setModalOpenState(isOpen) {
    //    // If you need Alpine to be aware of the modal state for other reasons
    //    // this.isModalActuallyOpen = isOpen;
    //    // document.body.style.overflow = isOpen ? 'hidden' : '';
    // },

    submitFilters(sourceFormId) { // submitFilters logic remains the same
        // console.log(`Submitting filters from: ${sourceFormId}`, JSON.parse(JSON.stringify(this.filters)));

        const formattedFilters = { ...this.filters };
        const numericFields = [
            'price_min', 'price_max',
            'area_min', 'area_max'
        ];

        numericFields.forEach(field => {
            if (formattedFilters[field] && typeof formattedFilters[field] === 'string') {
                formattedFilters[field] = formattedFilters[field].replace(/,/g, '');
                if (formattedFilters[field] !== '') {
                    formattedFilters[field] = parseFloat(formattedFilters[field]);
                } else {
                    formattedFilters[field] = null;
                }
            }
        });

        const params = new URLSearchParams();
        for (const key in formattedFilters) {
            let value = formattedFilters[key];
            const isNumericField = numericFields.includes(key);
            if (isNumericField && (value === '' || (typeof value === 'number' && isNaN(value)))) { value = null; }
            if (value !== null && value !== undefined && (typeof value !== 'string' || value.trim() !== '') && !(Array.isArray(value) && value.length === 0)) {
                if (Array.isArray(value)) {
                    value.forEach(item => params.append(`${key}[]`, String(item)));
                } else {
                    params.append(key, String(value));
                }
            }
        }
        const queryString = params.toString();
        window.location.href = getRoute('search_page') + (queryString ? '?' + queryString : '');

    },

    resetAdvancedFilters() { // resetAdvancedFilters logic remains the same
        this.filters.price_min = null;
        this.filters.price_max = null;
        this.filters.area_min = null;
        this.filters.area_max = null;
        this.filters.condition_id = '';
        this.filters.floor_id = '';
        this.filters.orientation_id = '';
        this.filters.amenities = [];
        // console.log('Advanced filters reset. Current filters:', JSON.parse(JSON.stringify(this.filters)));
    }
}));
window.Alpine = Alpine;
Alpine.plugin(mask);
Alpine.start()