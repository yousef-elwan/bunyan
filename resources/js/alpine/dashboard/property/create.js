import Swal from 'sweetalert2';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';
import "flatpickr/dist/flatpickr.min.css";

const api = http();


function displayValidationErrors(errors) {
    document.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger'));
    document.querySelectorAll('.invalid-feedback, [id$="_error"]').forEach(el => el.textContent = '');

    for (const [field, messages] of Object.entries(errors)) {
        let input;
        let errorElement;

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

        if (input) {
            input.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
        }
        if (errorElement) {
            errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    }
    const firstError = document.querySelector('.border-danger, .invalid-feedback:not(:empty), [id$="_error"]:not(:empty)');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

export function initAddPropertyForm() {
    const addPropertyForm = document.getElementById('addPropertyForm');
    if (!addPropertyForm) return;

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
    const priceInput = document.getElementById('propPrice');
    const hiddenPriceInput = document.getElementById('price_clean');
    const priceOnRequestCheckbox = document.getElementById('propPriceOnRequest');
    const cancelBtn = document.getElementById('cancelAddPropertyBtn'); // NEW: Select cancel button

    let leafletMap = null;
    let mapMarker = null;
    let selectedImageFilesForUpload = [];
    let userInteractedWithMap = false;

    // NEW: Cancel button confirmation logic
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


    if (priceInput && hiddenPriceInput && priceOnRequestCheckbox) {
        priceInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            hiddenPriceInput.value = value;
            e.target.value = value ? parseInt(value).toLocaleString('en-US') : '';
        });

        priceOnRequestCheckbox.addEventListener('change', function () {
            priceInput.disabled = this.checked;
            priceInput.required = !this.checked;
            // UPDATED: Toggle background color
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
        const defaultLat = 36.199480,
            defaultLng = 37.162667;

        // NEW: Custom marker icon
        const customMarkerIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // A nice red marker icon
            iconSize: [32, 32], // size of the icon
            iconAnchor: [16, 32], // point of the icon which will correspond to marker's location
            popupAnchor: [0, -32] // point from which the popup should open relative to the iconAnchor
        });

        leafletMap = L.map(propertyLocationMapDiv).setView([defaultLat, defaultLng], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(leafletMap);

        // UPDATED: Use the custom icon
        mapMarker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            icon: customMarkerIcon
        }).addTo(leafletMap);

        const updateInputs = (lat, lng) => {
            if (propLatitudeInput) propLatitudeInput.value = lat.toFixed(6);
            if (propLongitudeInput) propLongitudeInput.value = lng.toFixed(6);
        };

        mapMarker.on('dragend', () => {
            const { lat, lng } = mapMarker.getLatLng();
            updateInputs(lat, lng);
            userInteractedWithMap = true; // NEW: Set flag on drag
        });

        leafletMap.on('click', e => {
            mapMarker.setLatLng(e.latlng);
            leafletMap.panTo(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            userInteractedWithMap = true; // NEW: Set flag on click
        });

        updateInputs(defaultLat, defaultLng);
    }

    // async function fetchAndDisplayCustomAttributes(categoryId) {
    //     if (!customAttributesContainer || !customAttributesSection) return;
    //     customAttributesContainer.innerHTML = `<p class="text-text-secondary">${translate('js_loading_attributes')}</p>`;
    //     customAttributesSection.style.display = 'block';

    //     try {
    //         const filter = btoa(JSON.stringify([{ 'id': 'category_id', 'value': categoryId, 'filterFns': 'equals' }]));
    //         const url = `${getRoute('api.customAttribute')}?filters=${filter}`;
    //         const response = await api.get(url);
    //         const attributesData = response.data.data;
    //         customAttributesContainer.innerHTML = '';

    //         if (attributesData.length === 0) {
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
    //                     const defaultOption = document.createElement('option');
    //                     defaultOption.value = "";
    //                     defaultOption.textContent = translate('select_attribute_placeholder', { name: attr.name });
    //                     inputElement.appendChild(defaultOption);
    //                 }
    //                 values.forEach(val => {
    //                     const option = document.createElement('option');
    //                     option.value = val.id;
    //                     option.textContent = val.value;
    //                     inputElement.appendChild(option);
    //                 });
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
    //             formGroup.appendChild(inputElement);

    //             const hiddenInput = document.createElement('input');
    //             hiddenInput.type = "hidden";
    //             hiddenInput.name = `attributes[${attr.id}][custom_attribute_id]`;
    //             hiddenInput.value = attr.id;
    //             formGroup.appendChild(hiddenInput);

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

    function displayImagePreviewsForUpload() {
        if (!imageGalleryPreviewContainer) return;
        imageGalleryPreviewContainer.innerHTML = '';
        if (selectedImageFilesForUpload.length === 0) {
            imageGalleryPreviewContainer.innerHTML = `<p class="w-full text-sm text-center text-text-secondary">${translate('js_no_images_selected')}</p>`;
            return;
        }
        selectedImageFilesForUpload.forEach((file, index) => {
            const reader = new FileReader();
            const previewItem = document.createElement('div');
            previewItem.className = 'relative w-28 h-28 group';
            const img = document.createElement('img');
            img.className = 'object-cover w-full h-full rounded-md border border-border-color';
            reader.onload = (e) => img.src = e.target.result;
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
        const currentTotal = selectedImageFilesForUpload.length;
        const maxImages = window.AppConfig.settings.max_images || 10;
        const maxFileSize = (window.AppConfig.settings.max_file_size_mb || 2) * 1024 * 1024;
        if (currentTotal + files.length > maxImages) {
            Swal.fire(translate('swal_limit_exceeded_title'), translate('swal_limit_exceeded_text', { max: maxImages, current: currentTotal }), 'warning');
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

    async function uploadPropertyImages(propertyId) {
        // 1. التحقق المبدئي
        if (selectedImageFilesForUpload.length === 0) {
            return { success: true, message: "No images to upload." };
        }
        if (!uploadProgressContainer) {
            // إذا لم تكن عناصر الواجهة موجودة، أوقف التنفيذ مع إطلاق خطأ واضح
            throw new Error("UI progress elements are not found in the DOM.");
        }

        // 2. إعداد واجهة المستخدم لشريط التقدم
        uploadProgressContainer.style.display = 'block';
        uploadStatusText.textContent = translate('js_uploading_status', { count: selectedImageFilesForUpload.length });
        uploadProgressBarFill.style.width = '0%';

        // 3. تهيئة بيانات النموذج
        const imageFormData = new FormData();
        selectedImageFilesForUpload.forEach(file => imageFormData.append('images[]', file, file.name));

        // 4. تحديد رابط الرفع
        const uploadUrl = getRoute('api.dashboard-properties.images.store', { property: propertyId });

        // 5. استخدام دالة http مع try/catch للتعامل مع النجاح والفشل
        try {
            // استدعاء دالة http مع تمرير خيارات للتعامل مع رفع الملفات (multipart) وشريط التقدم
            const response = await http({ multipart: true }).post(uploadUrl, imageFormData, {

                // هذا هو الخيار الذي يتيح لـ axios تتبع تقدم الرفع
                onUploadProgress: (progressEvent) => {
                    // تحقق من أن حجم الملف معروف لتجنب القسمة على صفر
                    if (progressEvent.lengthComputable) {
                        const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        uploadProgressBarFill.style.width = percent + '%';
                        uploadStatusText.textContent = translate('js_uploading_progress', { percent });
                    }
                },
            });

            // في حالة النجاح
            uploadStatusText.textContent = translate('js_upload_complete');
            // إرجاع استجابة ناجحة
            return { success: true, message: response.data.message || 'Upload successful' };

        } catch (error) {
            // في حالة الفشل، سيقوم معترض الأخطاء في http بمعالجة الأخطاء العامة (مثل 419)
            // هنا يمكنك التعامل مع الأخطاء الخاصة بالرفع
            const errorMsg = error.response?.data?.message || 'An unknown error occurred during upload.';
            // إظهار الخطأ في واجهة المستخدم
            uploadStatusText.textContent = errorMsg;
            // إطلاق استثناء يمكن التقاطه في الكود الذي استدعى هذه الدالة
            throw new Error(errorMsg);
        }
    }


    if (propCategorySelect) {
        propCategorySelect.addEventListener('change', function () {
            if (this.value) fetchAndDisplayCustomAttributes(this.value);
            else {
                if (customAttributesSection) customAttributesSection.style.display = 'none';
                if (customAttributesContainer) customAttributesContainer.innerHTML = '';
            }
        });
    }

    if (propImagesUploadInput) {
        propImagesUploadInput.addEventListener('change', (e) => {
            handleFileSelection(e.target.files);
            e.target.value = "";
        });

        const dropArea = document.getElementById('image-drop-area');
        if (dropArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
                document.body.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
            });
            ['dragenter', 'dragover'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-over')));
            ['dragleave', 'drop'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-over')));
            dropArea.addEventListener('drop', e => handleFileSelection(e.dataTransfer.files));
        }
    }

    addPropertyForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        // NEW: Validate map interaction before anything else
        if (!userInteractedWithMap) {
            Swal.fire({
                icon: 'warning',
                title: translate('swal_map_location_title'),
                text: translate('swal_map_location_text'),
            });
            return; // Stop the submission
        }

        savePropertyBtn.disabled = true;
        savePropertyBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('button_saving')}`;
        displayValidationErrors({});

        try {
            const formData = new FormData(this);

            // بناء الكائن الأساسي للبيانات
            const payload = {
                price: formData.get('price'),
                size: formData.get('size'),
                year_built: formData.get('year_built') || null,
                category_id: formData.get('category_id'),
                type_id: formData.get('type_id'),
                floor_id: formData.get('floor_id') || null,
                rooms_count: formData.get('rooms_count') || null,
                price_on_request: formData.get('price_on_request') === 'true',
                orientation_id: formData.get('orientation_id') || null,
                city_id: formData.get('city_id'),
                latitude: formData.get('latitude'),
                longitude: formData.get('longitude'),
                available_from: formData.get('available_from'),
                video_url: formData.get('video_url'),
                amenities: Array.from(formData.getAll('amenities[]')),
                attributes: [],
                // *** تم التعديل هنا: تهيئة كمصفوفة فارغة ***
                locales: []
            };

            // التعامل مع السعر عند الطلب
            if (payload.price_on_request) {
                payload.price = null;
            }

            // جمع بيانات السمات المخصصة
            document.querySelectorAll('#customAttributesContainer > div').forEach(group => {
                const valueInput = group.querySelector('[name^="attributes["][name$="[value]"]');
                const idInput = group.querySelector('[name^="attributes["][name$="[custom_attribute_id]"]');
                if (idInput && valueInput && valueInput.value.trim() !== '') {
                    payload.attributes.push({
                        custom_attribute_id: idInput.value,
                        value: valueInput.value
                    });
                }
            });

            // *** تم التعديل هنا: بناء مصفوفة اللغات ***
            window.AppConfig.pageData.locales.forEach(locale => {
                payload.locales.push({
                    locale: locale,
                    content: formData.get(`content_${locale}`),
                    location: formData.get(`location_${locale}`)
                });
            });

            // إرسال البيانات
            const response = await api.post(getRoute('api.dashboard-properties.store'), payload);
            const result = response.data;
            const propertyId = result.data.id;

            // منطق رفع الصور بعد النجاح (يبقى كما هو)
            if (selectedImageFilesForUpload.length > 0 && propertyId) {
                savePropertyBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('button_uploading_images')}`;
                try {
                    const imageUploadResult = await uploadPropertyImages(propertyId);
                    Swal.fire({
                        icon: 'success', title: translate('swal_success_title'),
                        text: `${result.message || translate('js_create_success_default')} ${imageUploadResult.message || translate('js_images_processed')}`,
                        timer: 4000, showConfirmButton: false
                    }).then(() => window.location.href = getRoute('dashboard.properties.index'));
                } catch (imageError) {
                    Swal.fire({
                        icon: 'warning', title: translate('js_create_success_img_error'),
                        text: translate('js_create_success_img_error_desc', { error: imageError.message }),
                        showConfirmButton: true
                    }).then(() => window.location.href = getRoute('dashboard.properties.index'));
                }
            } else {
                Swal.fire({
                    icon: 'success', title: translate('swal_success_title'),
                    text: result.message || translate('js_create_success_default'),
                    timer: 3000, showConfirmButton: false
                }).then(() => {
                    window.location.href = getRoute('dashboard.properties.index')
                });
            }
        } catch (error) {
            console.error("Error during form submission:", error);
            if (error.response && error.response.status === 422) {
                displayValidationErrors(error.response.data.errors);
            }
            Swal.fire({
                icon: 'error', title: translate('swal_error_title'),
                text: error.response?.data?.message || translate('js_form_error_default'),
            });
            savePropertyBtn.disabled = false;
            savePropertyBtn.innerHTML = translate('button_save');
        }
    });

    initPropertyMap();
    displayImagePreviewsForUpload();
}


document.addEventListener('DOMContentLoaded', initAddPropertyForm);
