import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

const api = http();

function propertyFilters() {
    return {
        isLoading: true,
        currentPage: 1,
        ITEMS_PER_PAGE: 10,
        allPropertiesData: [],
        allPropertiesPaginationData: null,

        async init() {
            this.isLoading = true;
            await this.fetchProperties(this.currentPage);
            this.isLoading = false;
        },

        async fetchProperties(page = this.currentPage) {
            this.isLoading = true;

            document.querySelector('#propertiesDataTable tbody').innerHTML = '';
            document.querySelector('#propertiesCardsContainer').innerHTML = '';
            document.getElementById('paginationInfo').innerHTML = '';
            document.getElementById('paginationControls').innerHTML = '';

            try {
                const params = new URLSearchParams({
                    page: page,
                    perPage: this.ITEMS_PER_PAGE,
                    filters: btoa(JSON.stringify([
                        { id: 'is_favorite', filterFns: 'equals', value: true }
                    ]))
                });

                const url = getRoute('api.dashboard-properties') + '?' + params.toString();
                const response = await api.get(url);

                this.allPropertiesData = response.data.data;
                this.allPropertiesPaginationData = response.data.pagination;
                this.currentPage = page;

                this.renderTableAndCards();
                this.setupPagination();
                this.updatePaginationInfo();

            } catch (error) {
                console.error("Failed to fetch properties:", error);
                document.querySelector('#propertiesDataTable tbody').innerHTML = `<tr><td colspan="7" class="text-center text-danger p-5">${translate('error_loading_properties')}</td></tr>`;
            } finally {
                this.isLoading = false;
            }
        },

        updatePaginationInfo() {
            const infoContainer = document.getElementById('paginationInfo');
            if (!infoContainer || !this.allPropertiesPaginationData || this.allPropertiesPaginationData.total === 0) {
                if (infoContainer) infoContainer.innerHTML = '';
                return;
            };

            const { from, to, total } = this.allPropertiesPaginationData;
            if (from && to && total) {
                infoContainer.innerHTML = translate('pagination_info', { from, to, total });
            }
        },

        renderTableAndCards() {
            const tableBody = document.querySelector('#propertiesDataTable tbody');
            const cardsContainer = document.querySelector('#propertiesCardsContainer');

            if (!tableBody || !cardsContainer) return;

            tableBody.innerHTML = '';
            cardsContainer.innerHTML = '';

            this.allPropertiesData.forEach(property => {
                const imageUrl = property.image_url || 'https://via.placeholder.com/150';
                const price = property.price_display ? property.price_display.toLocaleString() : 'N/A';

                // Desktop Table Row
                const row = tableBody.insertRow();
                row.className = 'hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="p-3"><img src="${imageUrl}" alt="${property.title}" class="w-16 h-16 object-cover rounded-md"></td>
                    <td class="p-3 font-medium text-text-primary whitespace-nowrap">${property.title || 'N/A'}</td>
                    <td class="p-3">${property.city || 'N/A'}</td>
                    <td class="p-3">${property.type || 'N/A'}</td>
                    <td class="p-3">${property.category || 'N/A'}</td>
                    <td class="p-3 font-medium text-text-primary">${price}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <a href="${getRoute('properties.details', { property: property.id })}" 
                               class="px-3 py-1 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90">
                                <i class="fas fa-eye mr-1"></i> ${translate('view_details')}
                            </a>
                            <button class="px-3 py-1 text-sm font-medium text-white bg-danger rounded-lg hover:bg-opacity-90 remove-favorite-action"
                                    data-id="${property.id}">
                                <i class="fas fa-heart-broken mr-1"></i> ${translate('remove_from_favorites')}
                            </button>
                        </div>
                    </td>
                `;

                // Mobile Card
                const card = document.createElement('div');
                card.className = "bg-card-bg rounded-xl shadow-custom p-4 border border-border-color";
                card.innerHTML = `
                    <div class="flex gap-4">
                        <img src="${imageUrl}" alt="${property.title}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-text-primary truncate">${property.title || 'N/A'}</h3>
                            <p class="text-sm text-text-secondary mt-1">
                                ${property.city || ''}, ${property.type || ''}, ${property.category || ''}
                            </p>
                            <p class="text-lg font-bold text-accent-primary mt-1">${price}</p>
                            <div class="flex gap-2 mt-3">
                                <a href="${getRoute('dashboard.properties.show', { property: property.id })}" 
                                   class="flex-1 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-accent-primary hover:bg-opacity-90">
                                    <i class="fas fa-eye mr-1"></i> ${translate('view_details')}
                                </a>
                                <button class="flex-1 px-3 py-2 text-sm font-medium text-white bg-danger rounded-lg hover:bg-opacity-90 remove-favorite-action"
                                        data-id="${property.id}">
                                    <i class="fas fa-heart-broken mr-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                cardsContainer.appendChild(card);
            });

            this.setupEventListeners();
        },

        setupPagination() {
            const container = document.getElementById('paginationControls');
            if (!container || !this.allPropertiesPaginationData || !this.allPropertiesPaginationData.links) return;

            container.innerHTML = '';

            this.allPropertiesPaginationData.links.forEach(link => {
                const button = document.createElement('button');
                button.innerHTML = link.label.replace('«', '<i class="fas fa-angle-left"></i>').replace('»', '<i class="fas fa-angle-right"></i>');
                button.disabled = !link.url || link.active;

                let baseClasses = 'px-3 py-1.5 text-sm rounded-md border transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
                let stateClasses = link.active
                    ? 'bg-accent-primary text-white border-accent-primary'
                    : 'border-border-color hover:bg-page-bg';

                button.className = `${baseClasses} ${stateClasses}`;

                button.addEventListener('click', () => {
                    if (link.url) {
                        const page = new URL(link.url).searchParams.get('page');
                        this.fetchProperties(parseInt(page));
                    }
                });
                container.appendChild(button);
            });
        },

        setupEventListeners() {
            const self = this;
            document.querySelectorAll('.remove-favorite-action').forEach(button => {
                button.addEventListener('click', function () {
                    const propertyId = this.getAttribute('data-id');
                    self.removeFromFavorites(propertyId);
                });
            });
        },

        removeFromFavorites(id) {
            Swal.fire({
                title: translate('confirm_remove_favorite_title'),
                text: translate('confirm_remove_favorite_text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: translate('confirm_button'),
                cancelButtonText: translate('cancel_button'),
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const url = getRoute('api.properties.toggle-favorite', { property: id });
                        await api.post(url);

                        Swal.fire(
                            translate('remove_favorite_success_title'),
                            translate('remove_favorite_success_text'),
                            'success'
                        );

                        this.fetchProperties(this.currentPage);
                    } catch (error) {
                        console.error("Failed to remove from favorites:", error);
                        Swal.fire('Error', 'Failed to remove property from favorites', 'error');
                    }
                }
            });
        }
    }
}

Alpine.data('propertyFilters', propertyFilters);