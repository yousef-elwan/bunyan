import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import { http } from '../../utils/api';
import { getRoute, translate } from '../../utils/helpers';
import Swal from 'sweetalert2';
import { format, formatDistanceToNow, isPast } from 'date-fns';
import { ar } from 'date-fns/locale';

const api = http();

function subscriptionManager() {
    return {
        // --- State ---
        selectedUser: null,
        history: [],
        isLoadingHistory: false,
        isAddModalOpen: false,
        isSubmitting: false,
        newSubscription: {
            package: '',
            package_name: '',
            duration_in_days: 0,
            start_date: new Date().toISOString().split('T')[0], // Default to today
            price: 0,
            notes: ''
        },
        translate: translate,
        tomSelectInstance: null,

        // --- Initialization ---
        init() {
            this.initializeUserSearch();
        },

        initializeUserSearch() {
            const self = this;

            this.tomSelectInstance = new TomSelect(this.$refs.userSelect, {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'email', 'mobile'],
                placeholder: this.translate('search_placeholder'),
                load: (query, callback) => {
                    if (!query.length) return callback();
                    api.get(getRoute('users.search'), { params: { q: query } })
                        .then(response => {
                            // The response from the API should be wrapped in a 'data' key
                            callback(response.data.data);
                        })
                        .catch(() => {
                            console.error('Failed to load users for TomSelect');
                            callback();
                        });
                },

                render: {
                    option: (data, escape) => {
                        return `<div class="flex items-center gap-3 p-2">
                                    <img src="${data.avatar_url || `https://ui-avatars.com/api/?name=${escape(data.name)}`}" class="w-8 h-8 rounded-full">
                                    <div>
                                        <div class="font-medium">${escape(data.name)}</div>
                                        <div class="text-sm text-gray-500">${escape(data.email)}</div>
                                    </div>
                                </div>`;
                    },
                    item: (data, escape) => {
                        return `<div class="flex items-center gap-2">
                                    <img src="${data.avatar_url || `https://ui-avatars.com/api/?name=${escape(data.name)}`}" class="w-6 h-6 rounded-full">
                                    <span>${escape(data.name)}</span>
                                </div>`;
                    }
                },

                onChange: (userId) => {
                    if (userId) {
                        const selectedOptionData = self.tomSelectInstance.options[userId];
                        self.selectUser(selectedOptionData);
                    } else {
                        self.selectedUser = null;
                        self.history = [];
                    }
                },
            });
        },

        selectUser(userData) {
            this.selectedUser = userData;
            this.fetchHistory(userData.id);
        },

        fetchHistory(userId) {
            this.isLoadingHistory = true; // Show spinner
            this.history = []; // Clear old data

            // Clear the view immediately before fetching
            document.getElementById('history-table-body').innerHTML = '';
            document.getElementById('history-cards-container').innerHTML = '';

            api.get(getRoute('subscriptions.history', { user: userId }))
                .then(response => {
                    const historyData = response.data.data || response.data;
                    this.history = historyData;

                    // Update user's main subscription date
                    const latestSubscription = historyData.length > 0 ? historyData[0] : null;
                    if (this.selectedUser) {
                        this.selectedUser.subscription_end = latestSubscription ? latestSubscription.end_date : null;
                    }
                })
                .catch(error => console.error("Failed to fetch subscription history:", error))
                .finally(() => {
                    this.isLoadingHistory = false; // Hide spinner
                    this.renderAll(); // Render data now that loading is complete
                });
        },

        renderAll() {
            this.renderHistoryTable();
            this.renderHistoryCards();
        },
        renderHistoryTable() {
            const tableBody = document.getElementById('history-table-body');
            if (!tableBody) return;
            tableBody.innerHTML = '';

            // Just render the data. The empty state is handled by the template.
            this.history.forEach(item => {
                const row = tableBody.insertRow();
                row.className = "border-t border-border-color";
                row.innerHTML = `
                    <td class="p-3">${item.package_name}</td>
                    <td class="p-3">${this.formatDate(item.start_date)}</td>
                    <td class="p-3">${this.formatDate(item.end_date)}</td>
                    <td class="p-3">${item.admin ? item.admin.name : 'N/A'}</td>
                `;
            });
        },

        renderHistoryCards() {
            const cardsContainer = document.getElementById('history-cards-container');
            if (!cardsContainer) return;
            cardsContainer.innerHTML = '';

            // Just render the data.
            this.history.forEach(item => {
                const card = document.createElement('div');
                card.className = "bg-page-bg p-3 rounded-lg border border-border-color";
                card.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-text-primary">${item.package_name}</div>
                        <div class="text-xs text-text-secondary">${translate('added_by')} ${item.admin ? item.admin.name : 'N/A'}</div>
                    </div>
                    <div class="text-sm text-text-secondary space-y-1">
                        <div><i class="fas fa-play-circle fa-fw text-green-500"></i> ${this.formatDate(item.start_date)}</div>
                        <div><i class="fas fa-stop-circle fa-fw text-red-500"></i> ${this.formatDate(item.end_date)}</div>
                    </div>
                `;
                cardsContainer.appendChild(card);
            });
        },

        addSubscription() {
            if (!this.newSubscription.package_name) {
                Swal.fire(this.translate('error'), this.translate('please_select_package'), 'error');
                return;
            }
            this.isSubmitting = true;
            const url = getRoute('subscriptions.store', { user: this.selectedUser.id });
            // const payload = {
            //     package_name: this.newSubscription.package_name,
            //     duration_in_days: this.newSubscription.duration_in_days,
            //     price: this.newSubscription.price,
            //     notes: this.newSubscription.notes
            // };
            const payload = {
                package_name: this.newSubscription.package_name,
                duration_in_days: this.newSubscription.duration_in_days,
                start_date: this.newSubscription.start_date, // Send start date
                price: this.newSubscription.price,
                notes: this.newSubscription.notes
            };

            api.post(url, payload)
                .then(() => {
                    Swal.fire(this.translate('success'), this.translate('subscription_added_successfully'), 'success');
                    this.isAddModalOpen = false;
                    this.resetForm();
                    this.fetchHistory(this.selectedUser.id);
                })
                .catch(error => {
                    Swal.fire(this.translate('error'), error.response?.data?.message || this.translate('unknown_error'), 'error');
                })
                .finally(() => this.isSubmitting = false);
        },

        updateFormFromPackage() {
            if (!this.newSubscription.package) {
                this.resetForm(false);
                return;
            }
            const pkg = JSON.parse(this.newSubscription.package);
            this.newSubscription.package_name = pkg.name;
            this.newSubscription.duration_in_days = pkg.duration;
        },

        resetForm(clearPackage = true) {
            if (clearPackage) this.newSubscription.package = '';
            this.newSubscription.package_name = '';
            this.newSubscription.duration_in_days = 0;
            this.newSubscription.start_date = new Date().toISOString().split('T')[0]; // Reset to today
            this.newSubscription.price = 0;
            this.newSubscription.notes = '';
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const locale = document.documentElement.lang === 'ar' ? { locale: ar } : {};
            return format(new Date(dateString), 'PPP', locale);
        },

        getRemainingTime(dateString) {
            if (!dateString) return { text: this.translate('no_active_subscription'), classes: 'text-text-secondary' };
            const endDate = new Date(dateString);
            const locale = document.documentElement.lang === 'ar' ? { locale: ar } : {};

            if (isPast(endDate)) {
                return { text: this.translate('expired_since') + ' ' + formatDistanceToNow(endDate, { ...locale, addSuffix: true }), classes: 'text-danger font-medium' };
            }
            return { text: this.translate('expires') + ' ' + formatDistanceToNow(endDate, { ...locale, addSuffix: true }), classes: 'text-success font-medium' };
        }
    };
}

Alpine.data('subscriptionManager', subscriptionManager);