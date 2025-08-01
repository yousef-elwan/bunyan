import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

const api = http();

function categoryList() {
    return {
        isLoading: true,
        currentPage: 1,
        ITEMS_PER_PAGE: 5,
        allData: [],
        paginationData: null,
        isActionSheetOpen: false,
        selectedCategoryId: null,
        categoryActions: [],

        init() {
            this.loadUrlParams();
            this.fetchCategories();
        },

        openActionSheet(categoryId) {
            this.selectedCategoryId = categoryId;
            this.categoryActions = [
                {
                    label: translate('edit_tooltip'),
                    icon: 'fas fa-edit',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.editItem(this.selectedCategoryId)
                },
                {
                    label: translate('delete_tooltip'),
                    icon: 'fas fa-trash',
                    classes: 'text-danger hover:bg-red-100',
                    handler: () => this.deleteItemWithConfirmation(this.selectedCategoryId)
                }
            ];
            this.isActionSheetOpen = true;
        },

        async fetchCategories(page = this.currentPage) {
            this.isLoading = true;
            document.querySelector('#categoriesDataTable tbody').innerHTML = '';
            document.querySelector('#categoryCardsContainer').innerHTML = '';
            document.querySelector('#paginationInfo').innerHTML = '';

            try {
                const params = new URLSearchParams({ page, perPage: this.ITEMS_PER_PAGE });
                const url = `${getRoute('api.category')}?${params.toString()}`;
                const response = await api.get(url);
                this.allData = response.data.data;
                this.paginationData = response.data.pagination;
                this.currentPage = page;
                this.updateUrlParams();
                this.renderTableAndCards();
                this.setupPagination();
                this.updatePaginationInfo();
            } catch (error) {
                console.error("Failed to fetch categories:", error);
                document.querySelector('#categoriesDataTable tbody').innerHTML = `<tr><td colspan="4" class="text-center p-5 text-danger">${translate('error_loading')}</td></tr>`;
            } finally {
                this.isLoading = false;
            }
        },

        updatePaginationInfo() {
            const infoContainer = document.getElementById('paginationInfo');
            if (!infoContainer || !this.paginationData || this.paginationData.total === 0) return;
            const { from, to, total } = this.paginationData;
            infoContainer.innerHTML = translate('pagination_info', { from, to, total });
        },

        renderTableAndCards() {
            const tableBody = document.querySelector('#categoriesDataTable tbody');
            const cardsContainer = document.querySelector('#categoryCardsContainer');
            if (!tableBody || !cardsContainer) return;

            tableBody.innerHTML = '';
            cardsContainer.innerHTML = '';

            this.allData.forEach(item => {
                const imageUrl = item.image_url || 'https://via.placeholder.com/100';

                const row = tableBody.insertRow();
                row.className = "hover:bg-gray-50";
                row.innerHTML = `
                    <td class="p-4"><img src="${imageUrl}" alt="${item.name}" class="w-12 h-12 object-cover rounded-md" /></td>
                    <td class="p-4 font-medium text-text-primary">${item.name || 'N/A'}</td>
                    <td class="p-4">${item.created_at || 'N/A'}</td>
                    <td class="p-4"><div class="flex items-center justify-center gap-4 text-lg">
                        <button @click.stop="editItem(${item.id})" class="text-accent-primary hover:text-opacity-80" title="${translate('edit_tooltip')}"><i class="fas fa-edit"></i></button>
                        <button @click.stop="deleteItemWithConfirmation(${item.id})" class="text-danger hover:text-opacity-80" title="${translate('delete_tooltip')}"><i class="fas fa-trash"></i></button>
                    </div></td>`;

                const card = document.createElement('div');
                card.className = 'flex items-center gap-4 p-4 bg-white border rounded-xl shadow-sm border-border-color';
                card.innerHTML = `
                    <img src="${imageUrl}" alt="${item.name}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-text-primary truncate">${item.name || 'N/A'}</h3>
                        <p class="text-sm text-text-secondary"><i class="fas fa-calendar-alt fa-fw me-1"></i>${item.created_at || 'N/A'}</p>
                    </div>
                    <button @click="openActionSheet(${item.id})" class="p-2 text-text-secondary hover:text-text-primary -m-2"><i class="fas fa-ellipsis-v"></i></button>`;
                cardsContainer.appendChild(card);
            });
        },

        setupPagination() {
            const container = document.getElementById('paginationControls');
            if (!container || !this.paginationData || !this.paginationData.links) return;
            container.innerHTML = '';
            this.paginationData.links.forEach(link => {
                const button = document.createElement('button');
                button.innerHTML = link.label.replace('«', '<i class="fas fa-angle-left"></i>').replace('»', '<i class="fas fa-angle-right"></i>');
                button.disabled = !link.url || link.active;
                const baseClasses = 'px-3 py-1.5 text-sm rounded-md border transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
                const stateClasses = link.active ? 'bg-accent-primary text-white border-accent-primary' : 'border-border-color hover:bg-page-bg';
                button.className = `${baseClasses} ${stateClasses}`;
                button.addEventListener('click', () => {
                    if (link.url) this.fetchCategories(new URL(link.url).searchParams.get('page'));
                });
                container.appendChild(button);
            });
        },

        updateUrlParams() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage > 1 ? params.set('page', this.currentPage) : params.delete('page');
            history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);
        },

        loadUrlParams() {
            this.currentPage = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
        },

        editItem(id) {
            window.location.href = getRoute('dashboard.category.edit', { category: id });
        },

        deleteItemWithConfirmation(id) {
            Swal.fire({
                title: translate('confirm_delete_title'),
                text: translate('confirm_delete_text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: translate('confirm_delete_button'),
                cancelButtonText: translate('cancel_button'),
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                reverseButtons: true
            }).then(result => { if (result.isConfirmed) this.deleteItem(id) });
        },

        async deleteItem(id) {
            const url = getRoute('api.category.destroy', { category: id });
            try {
                await api.delete(url);
                Swal.fire(translate('delete_success_title'), translate('delete_success_text'), 'success');
                if (this.allData.length === 1 && this.currentPage > 1) {
                    this.fetchCategories(this.currentPage - 1);
                } else {
                    this.fetchCategories(this.currentPage);
                }
            } catch (error) {
                Swal.fire(translate('error_title'), error.response?.data?.message || translate('delete_error_text', { error: '' }), 'error');
            }
        }
    };
}

// تسجيل المكون في Alpine
Alpine.data('categoryList', categoryList);