import Alpine from 'alpinejs';
import { http } from '../../utils/api';
import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { format } from 'date-fns';
import { ar } from 'date-fns/locale';

const api = http();

function reportsManagement() {
    return {
        // State Properties
        isLoading: true,
        allData: [],
        paginationData: null,
        currentPage: 1,
        ITEMS_PER_PAGE: 5,
        showAdvancedFilters: false,
        isActionSheetOpen: false,

        // Modal state
        isModalOpen: false,
        selectedReport: null,
        isStatusChanged: false,
        report_statuses: [],

        isModalOpen: false,
        selectedReport: null,
        selectedStatus: null,
        isSaveDisabled: true,

        reportActions: [],

        // Filters
        filters: {
            search: '',
            report_status_id: '',
            type_id: '',
            date_from: '',
            date_to: '',
            owner_id: '',
        },

        // Initialization
        init() {
            this.loadUrlParams();
            this.fetchReports(this.currentPage);
        },

        // Data Fetching
        async fetchReports(page = 1) {
            this.isLoading = true;
            this.currentPage = page;

            try {
                const params = new URLSearchParams({ page: this.currentPage, perPage: this.ITEMS_PER_PAGE });

                const filterArray = [];
                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value === undefined || value === '') return;
                    let filterKey = key;
                    let filterFns = 'equals';

                    switch (key) {
                        case 'search':
                            filterFns = 'includesString';
                            break;
                        case 'date_from':
                            filterFns = 'greaterThanOrEqualTo';
                            filterKey = 'created_at';
                            break;
                        case 'date_to':
                            filterFns = 'lessThanOrEqualTo';
                            filterKey = 'created_at';
                            break;
                        default:
                            filterFns = 'equals';
                    }

                    filterArray.push({
                        id: filterKey,
                        value,
                        filterFns
                    });
                });

                if (filterArray.length > 0) {
                    const encodedFilters = btoa(JSON.stringify(filterArray));
                    params.append('filters', encodedFilters);
                }

                const response = await api.get(`${getRoute('api.reports')}?${params.toString()}`);

                this.allData = response.data.data;
                this.paginationData = response.data.pagination;

                this.updateUrlParams();
                this.setupPagination();
                this.updatePaginationInfo();
            } catch (error) {
                console.error("Failed to fetch reports:", error);
                Swal.fire({
                    icon: 'error',
                    title: translate('error_title'),
                    text: translate('error_loading_reports'),
                });
            } finally {
                this.isLoading = false;
            }
        },

        // UI Actions
        openActionModal(report) {
            this.selectedReport = report;
            this.selectedStatus = report.report_status_id;
            this.isSaveDisabled = true;
            this.isModalOpen = true;
        },

        openActionSheet(report) {
            this.selectedReport = report;
            this.reportActions = [
                {
                    label: translate('view_tooltip'),
                    icon: 'fas fa-eye',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.openActionModal(report)
                },
                {
                    label: translate('mark_as_resolved'),
                    icon: 'fas fa-check-circle',
                    classes: 'text-success hover:bg-green-100',
                    handler: () => this.updateStatus(report.id, 'resolved')
                },
                {
                    label: translate('mark_as_rejected'),
                    icon: 'fas fa-ban',
                    classes: 'text-gray-700 hover:bg-gray-100',
                    handler: () => this.updateStatus(report.id, 'rejected')
                }
            ];
            this.isActionSheetOpen = true;
        },

        openModal(report) {
            this.openActionModal(report);
        },

        closeModal() {
            this.isModalOpen = false;
        },

        updateSaveButtonState() {
            // تفعيل الزر فقط إذا تغيرت الحالة
            if (this.selectedReport) {
                this.isSaveDisabled = (this.selectedStatus === this.selectedReport.report_status_id);
            }
        },

        async saveReportChanges() {
            if (!this.selectedReport || this.isSaveDisabled) return;

            try {
                await this.updateStatus(this.selectedReport.id, this.selectedStatus);
                this.closeModal();
            } catch (error) {
                console.error("Failed to save changes:", error);
            }
        },

        async saveReportStatus() {
            if (!this.selectedReport || !this.isStatusChanged) return;

            try {
                const reportId = this.selectedReport.id;
                await this.updateStatus(reportId, this.selectedStatus);
                this.closeModal();
            } catch (error) {
                console.error("Failed to save report status:", error);
            }
        },

        async updateStatus(reportId, newStatusId) {
            const currentReport = this.allData.find(r => r.id === reportId);
            if (currentReport && currentReport.status.id === newStatusId) return;

            try {
                const route = getRoute('api.reports.update_status', { reportId });
                const response = await api.patch(route, { report_status_id: newStatusId });

                // Update local data
                const reportIndex = this.allData.findIndex(r => r.id === reportId);
                if (reportIndex !== -1) {

                    this.allData[reportIndex].status.id = newStatusId;

                    this.allData[reportIndex].status = {
                        id: newStatusId,
                        name: this.report_statuses.find(s => s.id == newStatusId)?.name || newStatusId
                    };
                }

                Swal.fire({
                    icon: 'success',
                    title: response.data.message || translate('update_success_title'),
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (error) {
                console.error("Failed to update report status:", error);
                Swal.fire({
                    icon: 'error',
                    title: translate('error_title'),
                    text: error.response?.data?.message || translate('error_generic'),
                });
            } finally {
                this.isActionSheetOpen = false;
            }
        },

        // Utility Helpers
        applyFilters() { this.fetchReports(1); },

        resetFilters() {
            this.filters = { search: '', report_status_id: '', type_id: '', date_from: '', date_to: '', owner_id: '' };
            this.fetchReports(1);
        },

        updateUrlParams() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage > 1 ? params.set('page', this.currentPage) : params.delete('page');
            Object.entries(this.filters).forEach(([key, value]) => {
                value ? params.set(key, value) : params.delete(key);
            });
            history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);
        },

        loadUrlParams() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(params.get('page')) || 1;
            Object.keys(this.filters).forEach(key => {
                if (params.has(key)) this.filters[key] = params.get(key);
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
                    if (link.url) this.fetchReports(new URL(link.url).searchParams.get('page'));
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

        getReportStatusText(statusKey) {
            return translate(`status_${statusKey}`) || statusKey;
        },

        formatDate(dateString) {
            const locale = document.documentElement.lang === 'ar' ? { locale: ar } : {};
            return format(new Date(dateString), 'd MMM yyyy', locale);
        },

        propertyEditUrl(propertyId) {
            return getRoute('dashboard.properties.edit', { propertyId });
        },
        getReportActionsHtml(type = 'modal') {
            if (!this.selectedReport) return '';
            const reportId = this.selectedReport.id;
            const buttons = [
                { status: 'resolved', label: translate('mark_as_resolved'), icon: 'fa-check-circle', color: 'success' },
                { status: 'rejected', label: translate('mark_as_rejected'), icon: 'fa-ban', color: 'gray' }
            ];

            if (type === 'modal') {
                return buttons.map(btn =>
                    `<button @click="updateStatus(${reportId}, '${btn.status}')" class="btn-${btn.color}">
                        <i class="fas ${btn.icon} mr-2"></i> ${btn.label}
                    </button>`
                ).join('');
            } else { // 'sheet'
                return buttons.map(btn =>
                    `<button @click="updateStatus(${reportId}, '${btn.status}')" class="w-full flex items-center gap-4 p-3 text-lg rounded-lg text-left bg-card-bg text-${btn.color}-dark">
                        <i class="fas ${btn.icon} w-6 text-center"></i>
                        <span>${btn.label}</span>
                    </button>`
                ).join('');
            }
        },
    };
}

Alpine.data('reportsManagement', reportsManagement);


// ===============================================================
// START: New component for the Dashboard reports widget
// ===============================================================

function dashboardReports() {
    return {
        // State Properties
        isLoading: true,
        allData: [], // To store the latest reports
        report_statuses: [],

        // Modal state
        isModalOpen: false,
        selectedReport: null,
        selectedStatus: null,
        isSaveDisabled: true,

        // Initialization
        init() {
            // `report_statuses` will be injected from the Blade file via x-init.
            this.fetchLatestReports();
        },

        // Data Fetching for dashboard (fetches a fixed number of recent reports)
        async fetchLatestReports() {
            this.isLoading = true;
            try {
                const response = await api.get(`${getRoute('api.reports')}?perPage=5&sortBy=created_at&sortDirection=desc`);
                this.allData = response.data.data;
            } catch (error) {
                console.error("Failed to fetch latest reports for dashboard:", error);
            } finally {
                this.isLoading = false;
            }
        },

        // UI Actions for the modal
        openActionModal(report) {
            this.selectedReport = report;
            this.selectedStatus = report.report_status_id;
            this.isSaveDisabled = true;
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
            this.selectedReport = null; // Clear selection on close
        },

        updateSaveButtonState() {
            if (this.selectedReport) {
                this.isSaveDisabled = (this.selectedStatus == this.selectedReport.report_status_id);
            }
        },

        async saveReportChanges() {
            if (!this.selectedReport || this.isSaveDisabled) return;
            try {
                await this.updateStatus(this.selectedReport.id, this.selectedStatus);
                this.closeModal();
            } catch (error) {
                console.error("Failed to save report changes from dashboard:", error);
            }
        },

        // Re-using the same status update logic
        async updateStatus(reportId, newStatusId) {
            const currentReport = this.allData.find(r => r.id === reportId);
            if (currentReport && currentReport.report_status_id == newStatusId) return;

            try {
                const route = getRoute('api.reports.update_status', { reportId });
                const response = await api.patch(route, { report_status_id: newStatusId });

                // Update local data in the dashboard widget
                const reportIndex = this.allData.findIndex(r => r.id === reportId);
                if (reportIndex !== -1) {
                    const newStatusObject = this.report_statuses.find(s => s.id == newStatusId);
                    this.allData[reportIndex].status = newStatusObject || { id: newStatusId, name: newStatusId };
                    this.allData[reportIndex].report_status_id = newStatusId; // Update the ID as well
                }

                Swal.fire({
                    icon: 'success',
                    title: response.data.message || translate('update_success_title'),
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (error) {
                console.error("Failed to update report status:", error);
                Swal.fire({
                    icon: 'error',
                    title: translate('error_title'),
                    text: error.response?.data?.message || translate('error_generic'),
                });
            }
        },

        // Utility Helpers
        formatDate(dateString) {
            if (!dateString) return '';
            const locale = document.documentElement.lang === 'ar' ? { locale: ar } : {};
            return format(new Date(dateString), 'd MMM yyyy', locale);
        },

        propertyEditUrl(propertyId) {
            return getRoute('dashboard.properties.edit', { propertyId });
        },
    };
}

// Register the new component for the dashboard
Alpine.data('dashboardReports', dashboardReports);
// ===============================================================
// END: New component for the Dashboard reports widget
// ===========================
