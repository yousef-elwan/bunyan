import Alpine from 'alpinejs';
import { http } from '../../utils/api';
import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { format, formatDistanceToNow, isPast, isWithinInterval, addDays } from 'date-fns';
import { ar } from 'date-fns/locale'; // Import arabic locale if needed

const api = http();

function userManagement() {
    return {
        isLoading: true,
        allData: [],
        paginationData: null,
        currentPage: 1,
        ITEMS_PER_PAGE: 10,
        showAdvancedFilters: false,
        isUserModalOpen: false,
        selectedUser: null,
        isActionSheetOpen: false,
        isActionModalOpen: false,
        selectedUserForAction: null,
        userActions: [],
        filters: {
            search: '',
            is_active: '',
            is_blacklisted: '',
            email: '',
            mobile: '',
            subscription_start: '',
            subscription_end: '',
        },

        init() {
            this.loadUrlParams();
            this.fetchUsers(this.currentPage);
        },

        async fetchUsers(page = 1) {
            this.isLoading = true;
            this.currentPage = page;
            try {
                const params = new URLSearchParams({ page: this.currentPage, perPage: this.ITEMS_PER_PAGE });


                const filterArray = [];
                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value === undefined || value === '') return;

                    let filterFns = 'equals';

                    switch (key) {
                        case 'search':
                            filterFns = 'includesString';
                            break;
                        case 'email':
                        case 'mobile':
                            filterFns = 'contains';
                            break;
                        case 'is_active':
                        case 'is_blacklisted':
                            filterFns = 'equals';
                            break;
                        case 'subscription_start':
                            filterFns = 'greaterThanOrEqualTo';
                            key = 'subscription_end';
                            break;
                        case 'subscription_end':
                            filterFns = 'lessThanOrEqualTo';
                            key = 'subscription_end';
                            break;
                        default:
                            filterFns = 'equals';
                    }

                    filterArray.push({
                        id: key,
                        value,
                        filterFns
                    });
                });

                if (filterArray.length > 0) {
                    console.log(filterArray);
                    const encodedFilters = btoa(JSON.stringify(filterArray));
                    params.append('filters', encodedFilters);
                }

                const response = await api.get(`${getRoute('api.users')}?${params.toString()}`);
                this.allData = response.data.data;
                this.paginationData = response.data.pagination;
                this.updateUrlParams();
                this.renderAll();
                this.setupPagination();
                this.updatePaginationInfo();
            } catch (error) {
                console.error("Failed to fetch users:", error);
                const tableBody = document.querySelector('tbody');
                if (tableBody) tableBody.innerHTML = `<tr><td colspan="6" class="p-5 text-center text-danger">${translate('error_loading')}</td></tr>`;
                const cardsContainer = document.getElementById('userCardsContainer');
                if (cardsContainer) cardsContainer.innerHTML = `<div class="p-5 text-center text-danger">${translate('error_loading')}</div>`;
            } finally {
                this.isLoading = false;
            }
        },

        renderAll() {
            this.renderTable();
            this.renderCards();
        },

        renderTable() {
            // The table structure in the blade file is already updated,
            // so we just need to ensure the data is passed correctly.
            // No major changes here, the getSubscriptionBadge function will do the work.
            const tableBody = document.querySelector('tbody');
            if (!tableBody) return;
            tableBody.innerHTML = '';
            if (this.allData.length === 0 && !this.isLoading) return;

            this.allData.forEach(user => {
                const row = tableBody.insertRow();
                row.className = "hover:bg-gray-50";
                row.innerHTML = `
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="${user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`}" class="user-avatar" alt="${user.name}">
                            <div>
                                <div class="font-medium text-text-primary">${user.name}</div>
                                <div class="text-sm text-text-secondary">${user.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">${this.getSubscriptionBadge(user)}</td>
                    <td class="p-4"><span class="status-badge ${user.is_active ? 'status-active' : 'status-inactive'}">${user.is_active_text}</span></td>
                    <td class="p-4">${this.getBlacklistBadge(user, true)}</td>
                    <td class="p-4 text-text-secondary">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="p-4 text-center">
                        <button @click="$dispatch('open-action-modal', ${user.id})" class="p-2 text-text-secondary hover:text-text-primary rounded-full">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </td>
                `;
            });
        },


        renderCards() {
            const cardsContainer = document.getElementById('userCardsContainer');
            if (!cardsContainer) return;
            cardsContainer.innerHTML = '';

            this.allData.forEach(user => {
                const card = document.createElement('div');
                card.className = "flex items-center gap-4 p-4 bg-white border rounded-xl shadow-sm border-border-color";
                card.innerHTML = `
                    <img src="${user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`}" class="w-12 h-12 rounded-full object-cover flex-shrink-0" alt="${user.name}">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-text-primary truncate">${user.name}</h3>
                        <p class="text-sm text-text-secondary">${user.email}</p>
                        <div class="flex items-center flex-wrap gap-2 mt-2">
                            <span class="status-badge ${user.is_active ? 'status-active' : 'status-inactive'}">${user.is_active_text}</span>
                            ${this.getSubscriptionBadge(user)}
                            ${this.getBlacklistBadge(user, false)}
                        </div>
                    </div>
                    <button @click="$dispatch('open-action-sheet', ${user.id})" class="p-2 text-text-secondary hover:text-text-primary -m-2"><i class="fas fa-ellipsis-v"></i></button>
                `;
                cardsContainer.appendChild(card);
            });
        },

        getSubscriptionBadge(user) {
            if (!user.subscription_end) {
                return `<span class="subscription-badge subscription-none"><i class="fas fa-ban"></i> ${translate('subscription_none')}</span>`;
            }

            const endDate = new Date(user.subscription_end);
            const now = new Date();
            const locale = document.documentElement.lang === 'ar' ? { locale: ar } : {};

            let statusClass = 'subscription-active';
            let iconClass = 'fas fa-check-circle';
            let timeText = formatDistanceToNow(endDate, { ...locale, addSuffix: true });

            if (isPast(endDate)) {
                statusClass = 'subscription-expired';
                iconClass = 'fas fa-exclamation-triangle';
            } else if (isWithinInterval(endDate, { start: now, end: addDays(now, 7) })) {
                // If expires within 7 days, show a warning color
                statusClass = 'subscription-expired'; // Use 'expired' style for warning
                iconClass = 'fas fa-exclamation-circle';
            }

            const fullDate = format(endDate, 'PPP', locale);

            return `
                <div class="flex flex-col" title="${fullDate}">
                    <span class="subscription-badge ${statusClass}">
                        <i class="${iconClass}"></i>
                        <span>${timeText}</span>
                    </span>
                </div>
            `;
        },
        getBlacklistBadge(user, showFullText = true) {
            if (user.is_blacklisted) {
                return `<span class="blacklisted-badge"><i class="fas fa-user-slash"></i>${showFullText ? ` ${translate('blacklisted')}` : ''}</span>`;
            }
            return showFullText ? `<span class="not-blacklisted">${translate('not_blacklisted')}</span>` : '';
        },

        openActionModal(userId) {
            const user = this.allData.find(u => u.id === userId);
            if (!user) return;
            this.selectedUserForAction = user;
            this.isActionModalOpen = true;
        },

        openActionSheet(userId) {
            const user = this.allData.find(u => u.id === userId);
            if (!user) return;

            this.userActions = [
                {
                    label: translate('view_tooltip'),
                    icon: 'fas fa-eye',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.openUserModal(user.id)
                },
                {
                    label: user.is_active ? translate('deactivate_user') : translate('activate_user'),
                    icon: 'fas fa-power-off',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.promptForStatusChange(user)
                },
                {
                    label: user.is_blacklisted ? translate('unblacklist_button') : translate('blacklist_button'),
                    icon: 'fas fa-user-slash',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.promptForBlacklist(user)
                },
                {
                    label: translate('delete_tooltip'), icon: 'fas fa-trash',
                    classes: 'text-danger hover:bg-red-100',
                    handler: () => this.deleteUserWithConfirmation(user.id)
                }
            ];
            this.isActionSheetOpen = true;
        },

        async promptForStatusChange(user) {
            const newStatus = user.is_active ? 'inactive' : 'active';
            const { value: reason } = await Swal.fire({
                title: translate('confirm_status_change_title'),
                input: 'textarea',
                inputLabel: translate('reason_label'),
                inputPlaceholder: translate('reason_placeholder'),
                showCancelButton: true,
                confirmButtonText: translate('confirm_button'),
                cancelButtonText: translate('cancel_button'),
                inputValidator: (value) => { if (!value) return translate('reason_required'); }
            });

            if (reason) {
                try {
                    await api.put(getRoute('api.users.update_status', { user: user.id }), { is_active: newStatus, is_active_reason: reason });
                    await this.fetchUsers(this.currentPage);
                    Swal.fire({ title: translate('update_success_title'), text: translate('update_success_text'), icon: 'success', timer: 2000, showConfirmButton: false });
                } catch (error) {
                    Swal.fire({ title: translate('error_title'), text: error.response?.data?.message || translate('error_occurred'), icon: 'error' });
                }
            }
        },

        async promptForBlacklist(user) {
            const action = user.is_blacklisted ? 'unblacklist' : 'blacklist';
            const { value: reason } = await Swal.fire({
                title: translate(`confirm_${action}_title`),
                input: 'textarea',
                inputLabel: translate('reason_label'),
                inputPlaceholder: translate('reason_placeholder'),
                showCancelButton: true,
                confirmButtonText: translate('confirm_button'),
                cancelButtonText: translate('cancel_button'),
                inputValidator: (value) => { if (!value && action === 'blacklist') return translate('reason_required'); }
            });

            if (reason !== undefined) {
                try {
                    const route = getRoute(`api.users.${action}`, { user: user.id });
                    const payload = {
                        is_blacklist_reason: reason
                    };
                    action === 'blacklist' ? await api.post(route, payload) : await api.delete(route, { data: payload });
                    await this.fetchUsers(this.currentPage);
                    Swal.fire({ title: translate('update_success_title'), text: translate('update_success_text'), icon: 'success', timer: 2000, showConfirmButton: false });
                } catch (error) {
                    Swal.fire({ title: translate('error_title'), text: error.response?.data?.message || translate('error_occurred'), icon: 'error' });
                }
            }
        },

        deleteUserWithConfirmation(id) {
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
            }).then(result => { if (result.isConfirmed) this.deleteUser(id) });
        },

        async deleteUser(id) {
            try {
                await api.delete(getRoute('api.users.destroy', { user: id }));
                const pageToFetch = (this.allData.length === 1 && this.currentPage > 1) ? this.currentPage - 1 : this.currentPage;
                await this.fetchUsers(pageToFetch);
                Swal.fire({ title: translate('delete_success_title'), text: translate('delete_success_text'), icon: 'success', timer: 2000, showConfirmButton: false });
            } catch (error) {
                Swal.fire({ title: translate('error_title'), text: error.response?.data?.message || translate('error_occurred'), icon: 'error' });
            }
        },

        async openUserModal(userId) {
            this.isUserModalOpen = true;
            this.selectedUser = null;
            try {
                const url = getRoute('api.users.show', { 'user': userId });
                const response = await api.get(url);
                this.selectedUser = response.data.data;
            } catch (error) {
                this.isUserModalOpen = false;
                Swal.fire({ icon: 'error', title: translate('error_title'), text: translate('error_loading_user_details') });
            }
        },

        applyFilters() { this.fetchUsers(1); },
        resetFilters() {
            this.filters = { search: '', is_active: '', is_blacklisted: '', email: '', mobile: '', subscription_start: '', subscription_end: '' };
            this.applyFilters();
        },
        updateUrlParams() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage > 1 ? params.set('page', this.currentPage) : params.delete('page');
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) {
                    params.set(key, value);
                } else {
                    params.delete(key);
                }
            });
            history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);
        },

        loadUrlParams() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(params.get('page')) || 1;
            Object.keys(this.filters).forEach(key => {
                if (params.has(key)) {
                    this.filters[key] = params.get(key);
                }
            });
        },

        setupPagination() {
            const container = document.getElementById('paginationControls');
            if (!container || !this.paginationData || !this.paginationData.links) {
                if (container) container.innerHTML = '';
                return;
            };
            container.innerHTML = '';
            this.paginationData.links.forEach(link => {
                const button = document.createElement('button');
                button.innerHTML = link.label.replace('«', '<i class="fas fa-angle-left"></i>').replace('»', '<i class="fas fa-angle-right"></i>');
                button.disabled = !link.url || link.active;
                const baseClasses = 'px-3 py-1.5 text-sm rounded-md border transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
                const stateClasses = link.active ? 'bg-accent-primary text-white border-accent-primary' : 'border-border-color hover:bg-page-bg';
                button.className = `${baseClasses} ${stateClasses}`;
                button.addEventListener('click', () => {
                    if (link.url) this.fetchUsers(new URL(link.url).searchParams.get('page'));
                });
                container.appendChild(button);
            });
        },

        updatePaginationInfo() {
            const infoContainer = document.getElementById('paginationInfo');
            if (!infoContainer || !this.paginationData || this.paginationData.total === 0) {
                if (infoContainer) infoContainer.innerHTML = '';
                return;
            };
            const { from, to, total } = this.paginationData;
            infoContainer.innerHTML = translate('pagination_info', { from, to, total });
        },
    };
}

Alpine.data('userManagement', userManagement);