import L from 'leaflet';
import Swal from 'sweetalert2';
import 'leaflet/dist/leaflet.css';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

const api = http();

function displayValidationErrors(errors) {
    document.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

    for (const [field, messages] of Object.entries(errors)) {
        let input, errorElement;
        const fieldName = field.split('.')[0];

        if (fieldName === 'attributes') {
            const attrId = field.match(/attributes\.(\d+)\.value/)?.[1];
            if (attrId) {
                input = document.getElementById(`custom_attr_${attrId}`);
                errorElement = document.getElementById(`attributes_${attrId}_error`);
            }
        } else if (fieldName === 'locales') {
            const [_, locale, key] = field.split('.');
            input = document.querySelector(`[name="${key}_${locale}"]`);
            errorElement = document.getElementById(`${key}_${locale}_error`);
        } else {
            input = document.querySelector(`[name="${field}"]`);
            errorElement = document.getElementById(`${field}_error`);
        }

        if (input) input.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
        if (errorElement) errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
    }
    const firstError = document.querySelector('.border-danger, .invalid-feedback:not(:empty)');
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

document.addEventListener('DOMContentLoaded', function () {
    const pageDataEl = document.getElementById('page-data');
    if (!pageDataEl) { console.error('Critical: Page data element not found.'); return; }
    const pageData = JSON.parse(pageDataEl.textContent);

    const editPropertyForm = document.getElementById('editPropertyForm');
    if (!editPropertyForm) return;

    const propCategorySelect = document.getElementById('propCategory');
    const customAttributesContainer = document.getElementById('customAttributesContainer');
    const customAttributesSection = document.getElementById('customAttributesSection');
    const propLatitudeInput = document.getElementById('propLatitude');
    const propLongitudeInput = document.getElementById('propLongitude');
    const propertyLocationMapDiv = document.getElementById('propertyLocationMap');
    const propImagesUploadInput = document.getElementById('propImagesUpload');
    const imageGalleryPreviewContainer = document.getElementById('imageGalleryPreviewContainer');
    const savePropertyBtn = document.getElementById('savePropertyBtn');
    const uploadProgressContainer = document.getElementById('uploadProgressContainer');
    const uploadProgressBarFill = document.querySelector('#uploadProgressContainer .progress-bar-fill');
    const uploadStatusText = document.getElementById('uploadStatusText');
    const existingImagesGallery = document.getElementById('existingImagesGallery');
    const deletedImagesInput = document.getElementById('deleted_images_input');
    const priceInput = document.getElementById('propPrice');
    const hiddenPriceInput = document.getElementById('price_clean');
    const priceOnRequestCheckbox = document.getElementById('propPriceOnRequest');

    // NEW: Select the cancel button by its new ID
    const cancelBtn = document.getElementById('cancelEditPropertyBtn');

    let leafletMap, mapMarker;
    let selectedImageFilesForUpload = [], imagesToDelete = [];

    function formatPriceInput() {
        if (!priceInput || !hiddenPriceInput) return;
        let value = priceInput.value.replace(/[^\d]/g, '');
        hiddenPriceInput.value = value;
        priceInput.value = value ? parseInt(value).toLocaleString('en-US') : '';
    }

    // NEW: Add cancel confirmation logic
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function (event) {
            event.preventDefault(); // Stop immediate navigation
            const destinationUrl = this.href;
            Swal.fire({
                title: translate('swal_cancel_title'),
                text: translate('swal_cancel_text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: translate('swal_cancel_confirm_button'),
                cancelButtonText: translate('swal_cancel_abort_button')
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = destinationUrl;
                }
            });
        });
    }

    // UPDATED: Price on Request logic with visual feedback
    if (priceInput && hiddenPriceInput && priceOnRequestCheckbox) {
        priceInput.addEventListener('input', formatPriceInput);
        if (priceInput.value) formatPriceInput();

        priceOnRequestCheckbox.addEventListener('change', function () {
            priceInput.disabled = this.checked;
            priceInput.required = !this.checked;

            if (this.checked) {
                priceInput.value = '';
                hiddenPriceInput.value = '';
                priceInput.classList.add('bg-gray-100'); // Add gray background
                const priceErrorEl = document.getElementById('price_error');
                if (priceErrorEl) priceErrorEl.textContent = '';
                priceInput.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger');
            } else {
                priceInput.classList.remove('bg-gray-100'); // Remove gray background
            }
        });
    }

    function initPropertyMap() {
        if (!propertyLocationMapDiv) return;
        const initialLat = pageData.property.latitude || 36.199480;
        const initialLng = pageData.property.longitude || 37.162667;

        // NEW: Define the custom marker icon
        const customMarkerIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        leafletMap = L.map(propertyLocationMapDiv).setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(leafletMap);

        // Use the custom icon when creating the marker
        mapMarker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: customMarkerIcon // Apply the custom icon here
        }).addTo(leafletMap);

        const updateInputs = (lat, lng) => {
            if (propLatitudeInput) propLatitudeInput.value = lat.toFixed(6);
            if (propLongitudeInput) propLongitudeInput.value = lng.toFixed(6);
        };
        mapMarker.on('dragend', () => updateInputs(mapMarker.getLatLng().lat, mapMarker.getLatLng().lng));
        leafletMap.on('click', e => {
            mapMarker.setLatLng(e.latlng);
            leafletMap.panTo(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    }

    // async function fetchAndDisplayCustomAttributes(categoryId, existingValues = {}) {
    //     if (!customAttributesContainer || !customAttributesSection) return;
    //     customAttributesContainer.innerHTML = `<p class="text-text-secondary">${translate('js_loading_attributes')}</p>`;
    //     customAttributesSection.style.display = 'block';

    //     try {
    //         const filter = btoa(JSON.stringify([{ 'id': 'category_id', 'value': categoryId, 'filterFns': 'equals' }]));
    //         const url = `${getRoute('api.customAttribute')}?filters=${filter}`;
    //         const response = await api.get(url);
    //         const attributesData = response.data.data;
    //         customAttributesContainer.innerHTML = '';
    //         if (!attributesData || attributesData.length === 0) {
    //             customAttributesContainer.innerHTML = `<p class="text-text-secondary col-span-full">${translate('js_no_attributes')}</p>`;
    //             return;
    //         }

    //         attributesData.forEach(attr => {
    //             const formGroup = document.createElement('div');
    //             const label = document.createElement('label');
    //             label.htmlFor = `custom_attr_${attr.id}`;
    //             label.className = 'block mb-2 text-sm font-medium text-text-primary';
    //             label.textContent = attr.name;
    //             if (attr.is_required) {
    //                 const requiredSpan = document.createElement('span');
    //                 requiredSpan.className = 'text-danger';
    //                 requiredSpan.textContent = ' *';
    //                 label.appendChild(requiredSpan);
    //             }
    //             formGroup.appendChild(label);

    //             const values = attr.custom_attribute_values;
    //             let inputElement;
    //             if (attr.type === "select" && values && values.length > 0) {
    //                 inputElement = document.createElement('select');
    //                 if (!attr.is_required) {
    //                     inputElement.add(new Option(translate('select_attribute_placeholder', { name: attr.name }), ""));
    //                 }
    //                 values.forEach(val => inputElement.add(new Option(val.value, val.id)));
    //             } else {
    //                 inputElement = document.createElement('input');
    //                 inputElement.type = attr.type === "number" ? "number" : "text";
    //                 if (attr.type === "number") {
    //                     if (attr.min_value !== null) inputElement.min = attr.min_value;
    //                     if (attr.max_value !== null) inputElement.max = attr.max_value;
    //                 }
    //             }

    //             inputElement.id = `custom_attr_${attr.id}`;
    //             inputElement.name = `attributes[${attr.id}][value]`;
    //             inputElement.className = 'block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary';
    //             if (attr.is_required) inputElement.required = true;

    //             const existingAttr = existingValues[attr.id.toString()];
    //             if (existingAttr && typeof existingAttr.value !== 'undefined') {
    //                 inputElement.value = existingAttr.value;
    //             }

    //             formGroup.appendChild(inputElement);
    //             const hiddenInputId = document.createElement('input');
    //             hiddenInputId.type = "hidden";
    //             hiddenInputId.name = `attributes[${attr.id}][custom_attribute_id]`;
    //             hiddenInputId.value = attr.id;
    //             formGroup.appendChild(hiddenInputId);
    //             const errorDiv = document.createElement('div');
    //             errorDiv.className = 'mt-1 text-sm text-danger invalid-feedback';
    //             errorDiv.id = `attributes_${attr.id}_error`;
    //             formGroup.appendChild(errorDiv);
    //             customAttributesContainer.appendChild(formGroup);
    //         });
    //     } catch (error) {
    //         console.error("Error fetching custom attributes:", error);
    //         customAttributesContainer.innerHTML = `<p class="text-danger">${translate('js_error_loading_attributes')}</p>`;
    //     }
    // }

    // This change applies to BOTH create.js and edit.js

    async function fetchAndDisplayCustomAttributes(categoryId, existingValues = {}) {

        if (!customAttributesContainer || !customAttributesSection) return;

        // --- START OF CHANGES ---

        // 1. Hide the section completely and clear it before making the API call.
        //    This prevents any "loading" message from showing.
        customAttributesContainer.innerHTML = '';
        customAttributesSection.style.display = 'none';

        try {
            const filter = btoa(JSON.stringify([{ 'id': 'category_id', 'value': categoryId, 'filterFns': 'equals' }]));
            const url = `${getRoute('api.customAttribute')}?filters=${filter}`;
            const response = await api.get(url);
            const attributesData = response.data.data;

            // 2. IMPORTANT: Check if the response actually has attributes.
            //    If not, do nothing and leave the section hidden.
            if (!attributesData || attributesData.length === 0) {
                return; // Exit the function silently
            }

            // 3. If and ONLY IF there are attributes, show the section and build the fields.
            customAttributesSection.style.display = 'block';

            attributesData.forEach(attr => {
                // ... The rest of the code that creates the form fields remains exactly the same ...
                const formGroup = document.createElement('div');
                const label = document.createElement('label');
                label.htmlFor = `custom_attr_${attr.id}`;
                label.className = 'block mb-2 text-sm font-medium text-text-primary';
                label.textContent = attr.name;
                if (attr.is_required) {
                    const requiredSpan = document.createElement('span');
                    requiredSpan.className = 'text-danger';
                    requiredSpan.textContent = ' *';
                    label.appendChild(requiredSpan);
                }
                formGroup.appendChild(label);

                const values = attr.custom_attribute_values;
                let inputElement;
                if (attr.type === "select" && values && values.length > 0) {
                    inputElement = document.createElement('select');
                    if (!attr.is_required) {
                        inputElement.add(new Option(translate('select_attribute_placeholder', { name: attr.name }), ""));
                    }
                    values.forEach(val => inputElement.add(new Option(val.value, val.id)));
                } else {
                    inputElement = document.createElement('input');
                    inputElement.type = attr.type === "number" ? "number" : "text";
                    if (attr.type === "number") {
                        if (attr.min_value !== null) inputElement.min = attr.min_value;
                        if (attr.max_value !== null) inputElement.max = attr.max_value;
                    }
                }

                inputElement.id = `custom_attr_${attr.id}`;
                inputElement.name = `attributes[${attr.id}][value]`;
                inputElement.className = 'block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary';
                if (attr.is_required) inputElement.required = true;

                const existingAttr = existingValues[attr.id.toString()];
                if (existingAttr && typeof existingAttr.value !== 'undefined') {
                    inputElement.value = existingAttr.value;
                }

                formGroup.appendChild(inputElement);
                const hiddenInputId = document.createElement('input');
                hiddenInputId.type = "hidden";
                hiddenInputId.name = `attributes[${attr.id}][custom_attribute_id]`;
                hiddenInputId.value = attr.id;
                formGroup.appendChild(hiddenInputId);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mt-1 text-sm text-danger invalid-feedback';
                errorDiv.id = `attributes_${attr.id}_error`;
                formGroup.appendChild(errorDiv);
                customAttributesContainer.appendChild(formGroup);
            });

            // --- END OF CHANGES ---

        } catch (error) {
            // In case of an error, we will also keep the section hidden.
            // We can log the error for debugging purposes.
            console.error("Error fetching custom attributes:", error);
        }
    }

    if (propCategorySelect) {
        const initialCategoryId = propCategorySelect.value;

        propCategorySelect.addEventListener('change', () => {
            const selectedCategoryId = propCategorySelect.value;

            if (selectedCategoryId === initialCategoryId) {
                fetchAndDisplayCustomAttributes(selectedCategoryId, pageData.property.custom_attributes);
            } else {
                fetchAndDisplayCustomAttributes(selectedCategoryId, {});
            }
        });

        if (initialCategoryId) {
            fetchAndDisplayCustomAttributes(initialCategoryId, pageData.property.custom_attributes);
        }
    }

    function displayImagePreviewsForUpload() {
        if (!imageGalleryPreviewContainer) return;
        imageGalleryPreviewContainer.innerHTML = '';
        if (selectedImageFilesForUpload.length === 0) {
            imageGalleryPreviewContainer.innerHTML = `<p class="w-full text-sm text-center text-text-secondary">${translate('js_no_new_images')}</p>`;
            return;
        }
        selectedImageFilesForUpload.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'relative w-28 h-28 group';
            const img = document.createElement('img');
            img.className = 'object-cover w-full h-full rounded-md border border-border-color';
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
            previewItem.appendChild(img);
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.title = translate('button_remove_image');
            removeBtn.className = 'absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = () => {
                selectedImageFilesForUpload.splice(index, 1);
                displayImagePreviewsForUpload();
            };
            previewItem.appendChild(removeBtn);
            imageGalleryPreviewContainer.appendChild(previewItem);
        });
    }

    function handleFileSelection(files) {
        const currentTotalNew = selectedImageFilesForUpload.length;
        const existingImagesCount = existingImagesGallery ? existingImagesGallery.querySelectorAll('.existing-image:not(.marked-for-deletion)').length : 0;
        const maxImages = window.AppConfig.settings.max_images;
        const maxFileSize = (window.AppConfig.settings.max_file_size_mb || 2) * 1024 * 1024;

        if (existingImagesCount + currentTotalNew + files.length > maxImages) {
            Swal.fire(translate('swal_limit_exceeded_title'), translate('swal_limit_exceeded_text', { max: maxImages }), 'warning');
            return;
        }
        let oversizedFiles = [], invalidTypeFiles = [];
        Array.from(files).forEach(file => {
            if (file.size > maxFileSize) oversizedFiles.push(file.name);
            else if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) invalidTypeFiles.push(file.name);
            else selectedImageFilesForUpload.push(file);
        });
        if (invalidTypeFiles.length > 0) Swal.fire(translate('swal_invalid_type_title'), translate('swal_invalid_type_text', { name: `"${invalidTypeFiles.join(', ')}"` }), 'warning');
        if (oversizedFiles.length > 0) Swal.fire(translate('swal_file_too_large_title'), translate('swal_file_too_large_text', { files: `"${oversizedFiles.join(', ')}"` }), 'warning');
        displayImagePreviewsForUpload();
    }

    if (propImagesUploadInput) {
        propImagesUploadInput.addEventListener('change', (e) => {
            handleFileSelection(e.target.files);
            e.target.value = "";
        });
        const dropArea = document.getElementById('image-drop-area');
        if (dropArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => dropArea.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
            ['dragenter', 'dragover'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.add('drag-over')));
            ['dragleave', 'drop'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.remove('drag-over')));
            dropArea.addEventListener('drop', e => handleFileSelection(e.dataTransfer.files));
        }
    }

    if (existingImagesGallery && deletedImagesInput) {
        existingImagesGallery.addEventListener('click', function (e) {
            const button = e.target.closest('.remove-existing-image-btn');
            if (button) {
                const imageItem = button.closest('.existing-image');
                const imageName = imageItem.dataset.imageName;
                if (!imageName) return;

                const index = imagesToDelete.indexOf(imageName);
                if (index > -1) {
                    imagesToDelete.splice(index, 1);
                    imageItem.classList.remove('marked-for-deletion');
                    button.classList.remove('undo-delete-btn');
                    button.innerHTML = '×';
                    button.title = translate('button_remove_image');
                } else {
                    imagesToDelete.push(imageName);
                    imageItem.classList.add('marked-for-deletion');
                    button.classList.add('undo-delete-btn');
                    button.innerHTML = '↺'; // Undo symbol
                    button.title = translate('button_undo_delete');
                }
                deletedImagesInput.value = imagesToDelete.join(',');
            }
        });
    }

    // async function uploadPropertyImages(propertyId) {
    //     if (selectedImageFilesForUpload.length === 0) return { success: true, message: "" };
    //     return new Promise((resolve, reject) => {
    //         if (!uploadProgressContainer) return reject(new Error("UI progress element missing."));
    //         uploadProgressContainer.style.display = 'block';
    //         uploadStatusText.textContent = translate('js_uploading_status', { count: selectedImageFilesForUpload.length });
    //         const imageFormData = new FormData();
    //         selectedImageFilesForUpload.forEach(file => imageFormData.append('images[]', file));
    //         const uploadUrl = getRoute('api.dashboard-properties.images.store', { property: propertyId });
    //         const xhr = new XMLHttpRequest();
    //         xhr.open('POST', uploadUrl, true);
    //         xhr.setRequestHeader('X-CSRF-TOKEN', window.AppConfig.csrfToken || pageData.csrf_token);
    //         xhr.setRequestHeader('Authorization', `Bearer ${window.AppConfig.apiToken}`);
    //         xhr.setRequestHeader('Accept-Language', window.AppConfig.locale);
    //         xhr.setRequestHeader('Accept', 'application/json');
    //         xhr.upload.onprogress = e => {
    //             if (e.lengthComputable) {
    //                 const percent = Math.round((e.loaded * 100) / e.total);
    //                 uploadProgressBarFill.style.width = percent + '%';
    //             }
    //         };
    //         xhr.onload = function () {
    //             try {
    //                 const result = JSON.parse(xhr.responseText);
    //                 if (xhr.status >= 200 && xhr.status < 300) {
    //                     resolve({ success: true, message: result.message || translate('js_upload_success_message') });
    //                 } else {
    //                     reject(new Error(result.message || 'Image upload failed.'));
    //                 }
    //             } catch (e) {
    //                 reject(new Error(translate('js_upload_error_invalid_response')));
    //             }
    //         };
    //         xhr.onerror = () => reject(new Error(translate('js_upload_error_network')));
    //         xhr.send(imageFormData);
    //     });
    // }

    async function uploadPropertyImages(propertyId) {
        // 1. التحقق من وجود ملفات (يبقى كما هو)
        if (selectedImageFilesForUpload.length === 0) {
            return { success: true, message: "" };
        }

        // 2. التحقق من وجود عناصر الواجهة الرسومية
        if (!uploadProgressContainer) {
            // من الأفضل إطلاق خطأ هنا لإيقاف التنفيذ
            throw new Error("UI progress element missing.");
        }

        // 3. إعداد الواجهة الرسومية (يبقى كما هو)
        uploadProgressContainer.style.display = 'block';
        uploadStatusText.textContent = translate('js_uploading_status', { count: selectedImageFilesForUpload.length });
        uploadProgressBarFill.style.width = '0%'; // ابدأ من الصفر

        // 4. تهيئة بيانات النموذج (يبقى كما هو)
        const imageFormData = new FormData();
        selectedImageFilesForUpload.forEach(file => imageFormData.append('images[]', file));

        // 5. الحصول على رابط الرفع (يبقى كما هو)
        const uploadUrl = getRoute('api.dashboard-properties.images.store', { property: propertyId });

        // 6. استخدام try/catch لإدارة النجاح والفشل بطريقة حديثة
        try {
            // استدعاء دالة http التي تستخدم axios في الخلفية
            const response = await http({ multipart: true }).post(uploadUrl, imageFormData, {

                // هذا الخيار في axios هو المكافئ لـ xhr.upload.onprogress
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.lengthComputable) {
                        const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        uploadProgressBarFill.style.width = percent + '%';
                    }
                },
            });

            // إذا نجح الطلب، قم بإرجاع رسالة النجاح
            return { success: true, message: response.data.message || translate('js_upload_success_message') };

        } catch (error) {
            // إذا فشل الطلب، التقط الخطأ هنا
            // دالة http ستكون قد عالجت الأخطاء العامة (مثل 419)
            // يمكنك هنا استخراج رسالة الخطأ المحددة من الخادم
            const errorMessage = error.response?.data?.message || translate('js_upload_error_network');

            // أطلق استثناءً جديدًا مع الرسالة الواضحة
            throw new Error(errorMessage);
        }
    }

    editPropertyForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        // NEW: Trigger TinyMCE to save content to the original textareas before processing the form
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        savePropertyBtn.disabled = true;
        savePropertyBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('button_updating')}`;
        displayValidationErrors({});

        try {
            const formData = new FormData(this);
            const payload = {
                _method: 'PUT',
                size: formData.get('size'),
                year_built: formData.get('year_built') || null,
                type_id: formData.get('type_id'),
                available_from: formData.get('available_from'),
                price: formData.get('price'),
                price_on_request: formData.get('price_on_request') === 'true',
                category_id: formData.get('category_id'),
                floor_id: formData.get('floor_id') || null,
                rooms_count: formData.get('rooms_count') || null,
                orientation_id: formData.get('orientation_id') || null,
                city_id: formData.get('city_id'),
                latitude: formData.get('latitude'),
                longitude: formData.get('longitude'),
                video_url: formData.get('video_url'),
                deleted_images: imagesToDelete,
                amenities: Array.from(formData.getAll('amenities[]')),
                attributes: [],
                locales: []
            };

            if (payload.price_on_request) payload.price = null;
            document.querySelectorAll('#customAttributesContainer > div').forEach(group => {
                const valueInput = group.querySelector('[name^="attributes["][name$="[value]"]');
                const idInput = group.querySelector('[name^="attributes["][name$="[custom_attribute_id]"]');
                if (idInput && valueInput && valueInput.value.trim() !== '') {
                    payload.attributes.push({ custom_attribute_id: idInput.value, value: valueInput.value });
                }
            });
            pageData.locales.forEach(locale => {
                payload.locales.push({
                    locale: locale,
                    content: formData.get(`content_${locale}`),
                    location: formData.get(`location_${locale}`)
                });
            });

            const updateUrl = getRoute('api.dashboard-properties.update', { property: pageData.property.id });
            const result = await api.post(updateUrl, payload); // Use post with _method spoofing

            if (selectedImageFilesForUpload.length > 0) {
                savePropertyBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('button_uploading_images')}`;
                const imageUploadResult = await uploadPropertyImages(pageData.property.id);
                Swal.fire({ icon: 'success', title: translate('swal_success_title'), text: `${result.data.message || ''} ${imageUploadResult.message || ''}`, timer: 4000 })
                    .then(() => window.location.href = getRoute('dashboard.properties.index'));
            } else {
                Swal.fire({ icon: 'success', title: translate('swal_success_title'), text: result.data.message, timer: 3000 })
                    .then(() => window.location.href = getRoute('dashboard.properties.index'));
            }

        } catch (error) {
            console.error("Error during form submission:", error);
            if (error.response && error.response.status === 422) {
                displayValidationErrors(error.response.data.errors);
            }
            Swal.fire({ icon: 'error', title: translate('swal_error_title'), text: error.response?.data?.message || translate('js_form_error_default') });
            savePropertyBtn.disabled = false;
            savePropertyBtn.innerHTML = translate('button_update');
        }
    });

    initPropertyMap();
    displayImagePreviewsForUpload();
});
