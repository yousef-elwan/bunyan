import Swal from 'sweetalert2';
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import Alpine from 'alpinejs';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

// ===================================================================
// 2. إعداد نسخة Axios مخصصة لهذه الصفحة
// ===================================================================
const api = http(); // نقوم بإنشاء نسخة واحدة لاستخدامها في كل مكان

// ===================================================================

// دالة مساعدة خاصة بهذه الصفحة
function encodeFiltersToBase64(filters) {
    return btoa(JSON.stringify(filters));
}

// 3. تعريف مكون Alpine
function propertyFilters() {
    return {
        showAdvancedFilters: false,

        isActionSheetOpen: false,
        selectedPropertyId: null,
        propertyActions: [],
        propertyStatuses: [],

        isLoading: true,
        filters: {
            user_id: '',
            city_id: '',
            category_id: '',
            type_id: '',
            status_id: '',
            min_price: '',
            max_price: '',
            floor_id: '',
            orientation_id: '',
            contract_type_id: '',
            amenities: []
        },
        currentPage: 1,
        ITEMS_PER_PAGE: 5,
        allPropertiesData: [],
        allPropertiesPaginationData: null,
        previousStatus: '',

        isOwnerModalOpen: false,
        isOwnerLoading: false,
        selectedOwner: null,

        async openOwnerModal(ownerId) {
            if (!ownerId) return;
            this.isOwnerModalOpen = true;
            this.isOwnerLoading = true;
            this.selectedOwner = null; // إفراغ البيانات القديمة

            try {
                const url = getRoute('api.users.show', { user: ownerId });
                const response = await api.get(url);
                this.selectedOwner = response.data.data;
            } catch (error) {
                console.error("Failed to fetch owner details:", error);
                // يمكنك إغلاق النافذة وإظهار رسالة خطأ
                this.isOwnerModalOpen = false;
                Swal.fire('Error', 'Could not load owner details.', 'error');
            } finally {
                this.isOwnerLoading = false;
            }
        },

        blacklistOwner() {
            if (!this.selectedOwner) return;
            const ownerName = this.selectedOwner.name;

            Swal.fire({
                title: translate('confirm_blacklist_title'),
                html: translate('confirm_blacklist_text', { name: ownerName }),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: translate('confirm_blacklist_button'),
                cancelButtonText: translate('cancel_button'),
                confirmButtonColor: '#d33',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const url = getRoute('api.users.blacklist', { user: this.selectedOwner.id });
                    try {
                        await api.post(url);
                        Swal.fire(
                            translate('blacklist_success_title'),
                            translate('blacklist_success_text', { name: ownerName }),
                            'success'
                        );
                        this.isOwnerModalOpen = false; // أغلق النافذة بعد النجاح
                        // قد ترغب في تحديث الواجهة لتعكس حالة الحظر
                    } catch (error) {
                        console.error("Failed to blacklist owner:", error);
                        // الخطأ سيعالج بواسطة المعترض العام في api.js
                    }
                }
            });
        },

        openActionSheet(propertyId) {
            this.selectedPropertyId = propertyId;
            const property = this.allPropertiesData.find(p => p.id === propertyId);
            if (!property) return;

            this.propertyActions = [
                {
                    label: translate('edit_tooltip'),
                    icon: 'fas fa-edit',
                    classes: 'hover:bg-gray-200',
                    handler: () => this.editProperty(this.selectedPropertyId)
                },
                {
                    label: translate('delete_tooltip'),
                    icon: 'fas fa-trash',
                    classes: 'text-danger hover:bg-red-100',
                    handler: () => this.deletePropertyWithConfirmation(this.selectedPropertyId)
                }
                // يمكنك إضافة المزيد من الإجراءات هنا في المستقبل
            ];

            this.isActionSheetOpen = true;
        },

        // --- الدوال ---
        toggleAdvancedFilterDrawer(show) {
            const body = document.body;
            body.classList.toggle('filter-drawer-open', show);
            body.classList.toggle('drawer-scroll-lock', show);
        },

        // async init() {

        //     const fetchTasks = [];

        //     this.loadFiltersFromUrl();
        //     if (window.AppConfig.isAdmin) {
        //         fetchTasks.push(this.fetchPropertyStatuses());
        //         // await this.fetchPropertyStatuses();
        //     }
        //     // this.fetchProperties();
        //     fetchTasks.push(this.fetchProperties(this.currentPage));

        //     await Promise.all(fetchTasks);

        //     this.setupTableEventListeners();
        // },
        async init() {
            this.isLoading = true; // ابدأ التحميل مرة واحدة في البداية

            this.loadFiltersFromUrl();

            try {
                const fetchTasks = []; // مصفوفة لتخزين وعود (Promises) الجلب

                // المهمة الأولى: جلب حالات العقار (فقط للمدير)
                // if (window.AppConfig.isAdmin) {
                fetchTasks.push(this.fetchPropertyStatuses());
                // }

                // المهمة الثانية: جلب العقارات نفسها
                // لاحظ أننا لا نستخدم await هنا، بل نضيف الـ Promise إلى المصفوفة
                fetchTasks.push(this.fetchProperties(this.currentPage));

                // انتظر حتى تكتمل جميع مهام الجلب في نفس الوقت
                await Promise.all(fetchTasks);

                // الآن بعد أن اكتمل كل شيء، قم بإعداد الواجهة
                this.renderTableAndCards();
                this.setupPagination();
                this.updatePaginationInfo();
                this.setupTableEventListeners();

            } catch (error) {
                console.error("Failed to initialize component:", error);
                // يمكنك عرض رسالة خطأ للمستخدم هنا
                document.querySelector('#propertiesDataTable tbody').innerHTML = `<tr><td colspan="11" class="text-center text-danger p-5">${translate('error_loading_properties')}</td></tr>`;
            } finally {
                this.isLoading = false; // أوقف التحميل بعد انتهاء كل شيء
            }
        },
        async fetchPropertyStatuses() {
            try {
                const response = await api.get(getRoute('api.properties-statuses'));
                this.propertyStatuses = response.data.data;
            } catch (error) {
                console.error("Failed to fetch property statuses:", error);
            }
        },
        getAppliedFilters() {
            let filters = [];

            if (this.filters.user_id) filters.push({ id: 'user_id', filterFns: 'equals', value: this.filters.user_id });
            if (this.filters.city_id) filters.push({ id: 'city_id', filterFns: 'equals', value: this.filters.city_id });
            if (this.filters.category_id) filters.push({ id: 'category_id', filterFns: 'equals', value: this.filters.category_id });
            if (this.filters.type_id) filters.push({ id: 'type_id', filterFns: 'equals', value: this.filters.type_id });
            if (this.filters.status_id) filters.push({ id: 'status_id', filterFns: 'equals', value: this.filters.status_id });
            if (this.filters.min_price) filters.push({ id: 'price', filterFns: 'greaterThanOrEqualTo', value: this.filters.min_price });
            if (this.filters.max_price) filters.push({ id: 'price', filterFns: 'lessThanOrEqualTo', value: this.filters.max_price });

            // if (this.filters.area_min) filters.push({ id: 'size', filterFns: 'greaterThanOrEqualTo', value: this.filters.area_min });
            // if (this.filters.area_max) filters.push({ id: 'size', filterFns: 'lessThanOrEqualTo', value: this.filters.area_max });
            // if (this.filters.rooms_count) filters.push({ id: 'rooms_count', filterFns: 'equals', value: this.filters.rooms_count });

            if (this.filters.floor_id) filters.push({ id: 'floor_id', filterFns: 'equals', value: this.filters.floor_id });
            if (this.filters.orientation_id) filters.push({ id: 'orientation_id', filterFns: 'equals', value: this.filters.orientation_id });
            if (this.filters.contract_type_id) filters.push({ id: 'contract_type_id', filterFns: 'equals', value: this.filters.contract_type_id });

            if (this.filters.amenities.length > 0) filters.push({ id: 'amenities', filterFns: 'arrIncludesAll', value: JSON.stringify(this.filters.amenities) });

            return filters;
        },
        async fetchProperties(page = this.currentPage) {
            this.isLoading = true; // <<< تحديث حالة التحميل

            // مسح المحتوى القديم قبل التحميل
            document.querySelector('#propertiesDataTable tbody').innerHTML = '';
            document.querySelector('#propertiesCardsContainer').innerHTML = '';
            document.getElementById('paginationInfo').innerHTML = '';
            document.getElementById('paginationControls').innerHTML = '';

            const filters = this.getAppliedFilters();

            try {
                // const params = new URLSearchParams({
                //     page: page,
                //     perPage: this.ITEMS_PER_PAGE
                // });


                console.log(this.filters);
                console.log(filters);

                const params = new URLSearchParams({
                    page: page,
                    perPage: this.ITEMS_PER_PAGE,
                    filters: btoa(JSON.stringify(filters)),
                    sorting: btoa(JSON.stringify(this.sort))
                });

                // const activeFilters = [];
                // Object.entries(this.filters).forEach(([key, value]) => {
                //     if (value && (Array.isArray(value) ? value.length > 0 : true)) {
                //         if (key === 'min_price' || key === 'max_price') return;
                //         activeFilters.push({ id: key, value: value, filterFns: 'equals' });
                //     }
                // });

                // if (this.filters.min_price || this.filters.max_price) {
                //     activeFilters.push({ id: 'price', value: [this.filters.min_price || '', this.filters.max_price || ''], filterFns: 'between' });
                // }

                // if (activeFilters.length > 0) {
                //     params.append('filters', encodeFiltersToBase64(activeFilters));
                // }


                const url = getRoute('api.dashboard-properties') + '?' + params.toString();
                const response = await api.get(url);

                this.allPropertiesData = response.data.data;
                this.allPropertiesPaginationData = response.data.pagination;
                this.currentPage = page;

                this.updateUrlWithFilters();
                this.renderTableAndCards(); // <<< تم تغيير اسم الدالة
                this.setupPagination();
                this.updatePaginationInfo(); // <<< دالة جديدة

            } catch (error) {
                console.error("Failed to fetch properties:", error);
                document.querySelector('#propertiesDataTable tbody').innerHTML = `<tr><td colspan="11" class="text-center text-danger p-5">${translate('error_loading_properties')}</td></tr>`;
            } finally {
                this.isLoading = false; // <<< تحديث حالة التحميل
            }
        },

        // <<< دالة جديدة لعرض معلومات التصفح
        updatePaginationInfo() {
            const infoContainer = document.getElementById('paginationInfo');
            // التأكد من وجود البيانات قبل عرضها
            if (!infoContainer || !this.allPropertiesPaginationData || this.allPropertiesPaginationData.total === 0) {
                if (infoContainer) infoContainer.innerHTML = '';
                return;
            };

            // << هذا الكود سيعمل بشكل صحيح مع الـ response لديك >>
            const { from, to, total } = this.allPropertiesPaginationData;
            if (from && to && total) {
                infoContainer.innerHTML = translate('pagination_info', { from, to, total });
            }
        },

        renderTableAndCards() {
            const tableBody = document.querySelector('#propertiesDataTable tbody');
            const cardsContainer = document.querySelector('#propertiesCardsContainer');

            if (!tableBody || !cardsContainer) return;

            tableBody.innerHTML = ''; // Clear previous content
            cardsContainer.innerHTML = ''; // Clear previous content

            this.allPropertiesData.forEach(property => {
                const formattedDate = property.created_at || 'N/A';
                const imageUrl = property.image_url || 'https://via.placeholder.com/150';
                const owner = property.owner;

                const propertyStatuses = window.AppConfig.isAdmin ? this.propertyStatuses : this.propertyStatuses.filter((s) => s.id != 'rejected')
                const ownerIsMe = property.owner_is_me;

                const blacklistedIcon = `<i class="fas fa-user-slash text-danger text-xs ms-2" title="${translate('user_is_blacklisted')}"></i>`;

                // Helper to get status classes
                const getStatusInfo = (status) => {
                    switch (status) {
                        case 'active': return { text: 'Active', classes: 'bg-success-light text-success' };
                        case 'rejected': return { text: 'Rejected', classes: 'bg-danger-light text-danger' };
                        default: return { text: 'Pending', classes: 'bg-warning-light text-warning' };
                    }
                };
                const statusInfo = getStatusInfo(property.status_id);

                const row = tableBody.insertRow();
                row.className = 'hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="p-3"><input type="checkbox" class="property-checkbox rounded border-gray-300 text-accent-primary focus:ring-accent-primary" data-id="${property.id}"></td>
                    <td class="p-3"><img src="${imageUrl}" alt="${property.title}" class="w-16 h-16 object-cover rounded-md"></td>
                    <td class="p-3 font-medium text-text-primary whitespace-nowrap">${property.title || 'N/A'}</td>
                  ${window.AppConfig.isAdmin ? `
                    <td class="p-3">
                        
                    ${ownerIsMe
                            ? translate('owner_is_me')
                            :
                            `<button @click.prevent="openOwnerModal(${owner.id})" class="text-accent-primary hover:underline">
                                ${owner.name}
                                ${owner.is_blacklisted ? blacklistedIcon : ''}
                            </button>`
                        }
                    </td>`: ``
                    }
                    <td class="p-3">${property.city || 'N/A'}</td>
                    <td class="p-3">${property.type || 'N/A'}</td>
                    <td class="p-3">${property.category || 'N/A'}</td>
                    <td class="p-3">
                        ${property.status_id != 'rejected' || window.AppConfig.isAdmin
                        ?
                        `<select class="status-select w-full p-1 border border-gray-300 rounded focus:ring-accent-primary focus:border-accent-primary"
                                      data-id="${property.id}"
                                      data-old-status="${property.status_id}">
                                 ${propertyStatuses.map(status => `
                                     <option value="${status.id}" ${property.status_id === status.id ? 'selected' : ''}>
                                         ${status.name}
                                     </option>
                                 `).join('')}
                               </select>`
                        : `<span class="px-2 py-1 text-xs font-medium rounded-full ${statusInfo.classes}">
                                   ${statusInfo.text}
                               </span>`
                    }
                    </td>
                    <td class="p-3 text-center">${property.views_count || 0}</td>
                    <td class="p-3 font-medium text-text-primary">${property.price_display ? property.price_display.toLocaleString() : 'N/A'}</td>
                    <td class="p-3">${formattedDate}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <button class="text-accent-primary hover:text-opacity-80 edit-action" title="${translate('edit_tooltip')}" data-id="${property.id}"><i class="fas fa-edit"></i></button>
                            <button class="text-danger hover:text-opacity-80 delete-action" title="${translate('delete_tooltip')}" data-id="${property.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;

                const card = document.createElement('div');
                card.className = "bg-card-bg rounded-xl shadow-custom p-4 border border-border-color";

                card.innerHTML = `
            <div class="flex gap-4">
                <img src="${imageUrl}" alt="${property.title}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        ${property.status_id != 'rejected' || window.AppConfig.isAdmin
                        ? `<select class="status-select w-full max-w-[120px] p-1 text-xs border border-gray-300 rounded focus:ring-accent-primary focus:border-accent-primary"
                                      data-id="${property.id}"
                                      data-old-status="${property.status_id}">
                                 ${propertyStatuses.map(status => `
                                     <option value="${status.id}" ${property.status_id === status.id ? 'selected' : ''}>
                                         ${status.name}
                                     </option>
                                 `).join('')}
                               </select>`
                        : `<span class="text-xs font-semibold px-2 py-1 rounded-full ${statusInfo.classes}">
                                   ${statusInfo.text}
                               </span>`
                    }
                        ${window.AppConfig.isAdmin ? '' :
                        `   <button @click="openActionSheet(${property.id})" class="text-lg text-text-secondary hover:text-text-primary p-1 rounded-full -m-1">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>`
                    }
                     
                    </div>
                    <h3 class="font-bold text-text-primary truncate mt-1">${property.title || 'N/A'}</h3>
                    <p class="text-sm text-text-secondary truncate">${property.city || ''}, ${property.category || ''}</p>
                    <p class="text-lg font-bold text-accent-primary mt-1">${property.price_display ? property.price_display.toLocaleString() : 'N/A'}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-border-color text-xs text-text-secondary">
                <span><i class="fas fa-calendar-alt me-1"></i> ${formattedDate}</span>
                 ${owner
                        ? `<span>
                        ${owner.is_blacklisted ? blacklistedIcon : `<i i class="fas fa-user me-1" ></i >`}
                        <button @click.prevent = "openOwnerModal(${owner.id})" class= "hover:underline" >
                        ${ownerIsMe ? translate('owner_is_me') : owner.name}
                        </button >
                    </span > `
                        : ''
                    }
                <span><i class="fas fa-eye me-1"></i> ${property.views_count || 0}</span>
                </div>
                `;
                // <span><i class="fas fa-eye me-1"></i> ${translate('th_views')}: ${property.views_count || 0}</span>
                cardsContainer.appendChild(card);
            });
        },


        setupPagination() {
            const container = document.getElementById('paginationControls');
            // التأكد من وجود البيانات
            if (!container || !this.allPropertiesPaginationData || !this.allPropertiesPaginationData.links) return;

            container.innerHTML = '';

            // << هذا الكود سيعمل بشكل صحيح مع مصفوفة links >>
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
        unblacklistOwner() {
            if (!this.selectedOwner) return;
            const ownerName = this.selectedOwner.name;

            Swal.fire({
                title: translate('confirm_reactivate_title'),
                html: translate('confirm_reactivate_text', { name: ownerName }),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: translate('confirm_reactivate_button'),
                cancelButtonText: translate('cancel_button'),
                confirmButtonColor: '#28a745', // أخضر
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const url = getRoute('api.users.unblacklist', { user: this.selectedOwner.id });
                    try {
                        // نستخدم api.delete لأن هذا هو المسار الذي حددناه
                        await api.delete(url);
                        Swal.fire(
                            translate('reactivate_success_title'),
                            translate('reactivate_success_text', { name: ownerName }),
                            'success'
                        );
                        this.isOwnerModalOpen = false;
                        // --- <<< تحديث الواجهة فورًا >>> ---
                        this.updateLocalOwnerStatus(this.selectedOwner.id, false);
                    } catch (error) {
                        console.error("Failed to reactivate owner:", error);
                    }
                }
            });
        },

        // --- <<< دالة مساعدة لتحديث حالة المالك في القائمة بدون إعادة تحميل >>> ---
        updateLocalOwnerStatus(ownerId, isBlacklisted) {
            // تحديث البيانات في القائمة الرئيسية
            this.allPropertiesData.forEach(property => {
                if (property.owner && property.owner.id === ownerId) {
                    property.owner.is_blacklisted = isBlacklisted;
                }
            });
            // إعادة عرض الجدول والبطاقات بالبيانات المحدثة
            this.renderTableAndCards();
        },

        applyFilters() { this.currentPage = 1; this.fetchProperties(); },
        resetFilters() {
            Object.keys(this.filters).forEach(key => this.filters[key] = Array.isArray(this.filters[key]) ? [] : '');
            this.applyFilters();
        },
        updateUrlWithFilters() {
            const params = new URLSearchParams(window.location.search);
            params.set('page', this.currentPage);
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value && (Array.isArray(value) ? value.length > 0 : true)) {
                    params.set(key, Array.isArray(value) ? value.join(',') : value);
                } else {
                    params.delete(key);
                }
            });
            // Don't add page=1 to the URL
            if (params.get('page') == '1') {
                params.delete('page');
            }
            history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);
        },
        loadFiltersFromUrl() {
            const params = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(params.get('page')) || 1;
            Object.keys(this.filters).forEach(key => {
                if (params.has(key)) {
                    this.filters[key] = key === 'amenities' ? params.get(key).split(',') : params.get(key);
                }
            });
        },

        setupTableEventListeners() {
            document.addEventListener('click', e => {
                const editButton = e.target.closest('.edit-action');
                if (editButton) this.editProperty(editButton.dataset.id);

                const deleteButton = e.target.closest('.delete-action');
                if (deleteButton) this.deletePropertyWithConfirmation(deleteButton.dataset.id);
            });
            document.addEventListener('change', e => {
                const statusSelect = e.target.closest('.status-select');
                if (statusSelect) {
                    const propertyId = statusSelect.dataset.id;
                    const newStatus = statusSelect.value;
                    const oldStatus = statusSelect.getAttribute('data-old-status');
                    this.confirmAndUpdateStatus(propertyId, oldStatus, newStatus, statusSelect);
                }
            });
        },

        editProperty(id) {
            window.location.href = getRoute('dashboard.properties.edit', { property: id });
        },

        confirmAndUpdateStatus(id, oldStatus, newStatus, selectElement) {
            const oldStatusText = selectElement.querySelector(`option[value="${oldStatus}"]`).textContent;
            const newStatusText = selectElement.querySelector(`option[value="${newStatus}"]`).textContent;

            Swal.fire({
                title: translate('confirm_status_change_title'),
                html: translate('confirm_status_change_text', { oldStatus: oldStatusText, newStatus: newStatusText }),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: translate('confirm_button'),
                cancelButtonText: translate('cancel_button'),
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.updatePropertyStatus(id, newStatus, oldStatus, selectElement);
                } else {
                    selectElement.value = oldStatus;
                }
            });
        },

        // async updatePropertyStatus(id, newStatus, oldStatus, selectElement) {
        //     const url = getRoute('api.dashboard-properties.update', { property: id });
        //     selectElement.disabled = true;
        //     try {
        //         await api.put(url, { status: newStatus, _source: 'status_update_from_list' }); // استخدام النسخة api
        //         Swal.fire(translate('update_success_title'), translate('update_success_text'), 'success');
        //         this.fetchProperties(this.currentPage);
        //     } catch (error) {
        //         // الخطأ سيعالج بواسطة المعترض، ولكننا نعيد الحالة القديمة هنا
        //         selectElement.value = oldStatus;
        //         selectElement.disabled = false;
        //     }
        // },
        async updatePropertyStatus(id, newStatus, oldStatus, selectElement) {
            const url = getRoute('api.dashboard-properties.update', { property: id });
            selectElement.disabled = true;
            try {
                await api.put(url, {
                    status_id: newStatus,
                    _source: 'status_update_from_list'
                });

                // تحديث حالة العقار محلياً دون إعادة جلب كل البيانات
                const propertyIndex = this.allPropertiesData.findIndex(p => p.id == id);
                if (propertyIndex !== -1) {
                    this.allPropertiesData[propertyIndex].status_id = newStatus;
                    selectElement.setAttribute('data-old-status', newStatus);
                }

                // إظهار رسالة نجاح
                Swal.fire({
                    title: translate('update_success_title'),
                    text: translate('update_success_text'),
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                selectElement.value = oldStatus;
                console.error("Update status failed:", error);
            } finally {
                selectElement.disabled = false;
            }
        },

        async deletePropertyWithConfirmation(id) {
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
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await this.deleteProperty(id);
                }
            });
        },

        async deleteProperty(id) {
            const url = getRoute('api.dashboard-properties.destroy', { property: id });
            try {
                await api.delete(url);
                Swal.fire(translate('delete_success_title'), translate('delete_success_text'), 'success');
                // Refetch data, stay on same page unless it's the last item on that page
                if (this.allPropertiesData.length === 1 && this.currentPage > 1) {
                    this.currentPage--;
                }
                this.fetchProperties(this.currentPage);
            } catch (error) {
                console.error("Delete failed:", error);
            }
        }
    }
}

Alpine.data('propertyFilters', propertyFilters);

document.addEventListener('DOMContentLoaded', () => {
    Fancybox.bind("[data-fancybox]", {});
});