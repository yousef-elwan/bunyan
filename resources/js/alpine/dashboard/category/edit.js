import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

// تهيئة نسخة axios لإرسال بيانات FormData (للصور)
const multipartApi = http({ multipart: true });

/**
 * دالة لإعداد منطق فورم تعديل فئة
 */
function setupEditForm() {
    // --- 1. تحديد العناصر ---
    const editForm = document.getElementById('editCategoryForm');
    if (!editForm) return;

    const saveBtn = document.getElementById('updateCategoryBtn');
    const imageInput = document.getElementById('imageUpload');
    const newImagePreviewContainer = document.getElementById('newImagePreviewContainer');
    const existingImageContainer = document.getElementById('existingImageContainer');
    const dropArea = document.getElementById('image-drop-area');

    // --- 2. الحالة (State) ---
    let isImageMarkedForDeletion = false;

    // --- 3. الدوال المساعدة ---

    /**
     * عرض أخطاء التحقق من الصحة التي تأتي من السيرفر
     * @param {object} errors - كائن الأخطاء
     */
    const displayValidationErrors = (errors) => {
        document.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        for (const [field, messages] of Object.entries(errors)) {
            const fieldParts = field.split('.');
            let input, errorElement;

            if (fieldParts[0] === 'locales' && fieldParts.length === 3) {
                const locale = window.AppConfig.pageData.locales[fieldParts[1]];
                input = document.getElementById(`name_${locale}`);
                errorElement = document.getElementById(`name_${locale}_error`);
            } else {
                input = document.querySelector(`[name="${field}"]`);
                errorElement = document.getElementById(`${field}_error`);
            }

            if (input) input.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
            if (errorElement) errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
        const firstError = document.querySelector('.border-danger, .invalid-feedback:not(:empty)');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    /**
     * معالجة عرض الصورة الجديدة المختارة
     * @param {File|null} file - الملف الجديد
     */
    const handleFileSelection = (file) => {
        // إذا اختار المستخدم صورة جديدة، قم بإلغاء تحديد الصورة القديمة للحذف
        if (isImageMarkedForDeletion) {
            isImageMarkedForDeletion = false;
            const img = existingImageContainer?.querySelector('img');
            const btn = existingImageContainer?.querySelector('button');
            if (img) img.classList.remove('marked-for-deletion');
            if (btn) {
                btn.innerHTML = '×';
                btn.title = translate('delete_image_tooltip');
            }
        }

        if (!file) {
            newImagePreviewContainer.innerHTML = `<p class="self-center w-full text-sm text-center text-text-secondary">${translate('no_new_image_preview')}</p>`;
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            Swal.fire(translate('swal_file_too_large_title'), translate('swal_file_too_large_text'), 'warning');
            imageInput.value = '';
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
            Swal.fire(translate('swal_invalid_type_title'), translate('swal_invalid_type_text'), 'warning');
            imageInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            newImagePreviewContainer.innerHTML = `
                <div class="relative w-28 h-28 group">
                    <img src="${event.target.result}" alt="New Preview" class="object-cover w-full h-full rounded-md border border-border-color">
                    <button type="button" class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity" title="${translate('button_remove_image')}">×</button>
                </div>`;
            newImagePreviewContainer.querySelector('button').addEventListener('click', () => {
                imageInput.value = '';
                handleFileSelection(null);
            });
        };
        reader.readAsDataURL(file);
    };

    // --- 4. ربط الأحداث ---

    // عند الضغط على زر حذف الصورة الحالية
    if (existingImageContainer) {
        existingImageContainer.addEventListener('click', (e) => {
            if (e.target.closest('button')) {
                isImageMarkedForDeletion = !isImageMarkedForDeletion;
                const img = existingImageContainer.querySelector('img');
                img.classList.toggle('marked-for-deletion', isImageMarkedForDeletion);
                e.target.closest('button').innerHTML = isImageMarkedForDeletion ? '↺' : '×'; // رمز التراجع
                e.target.closest('button').title = isImageMarkedForDeletion ? translate('undo_delete_tooltip') : translate('delete_image_tooltip');
                // إذا تم تحديد الحذف، قم بإلغاء أي صورة جديدة مختارة
                if (isImageMarkedForDeletion) {
                    imageInput.value = '';
                    handleFileSelection(null);
                }
            }
        });
    }

    // عند اختيار ملف جديد
    if (imageInput) {
        imageInput.addEventListener('change', (e) => handleFileSelection(e.target.files[0]));
    }
    
    // عند سحب وإفلات ملف
    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => dropArea.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
        ['dragenter', 'dragover'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.add('drag-over')));
        ['dragleave', 'drop'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.remove('drag-over')));
        dropArea.addEventListener('drop', e => {
            imageInput.files = e.dataTransfer.files;
            handleFileSelection(e.dataTransfer.files[0]);
        });
    }

    // عند إرسال الفورم
    editForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('saving')}`;
        displayValidationErrors({});

        try {
            const formData = new FormData();
            const locales = [];
            window.AppConfig.pageData.locales.forEach(locale => {
                locales.push({
                    locale: locale,
                    name: document.getElementById(`name_${locale}`).value,
                });
            });
            formData.append('locales', JSON.stringify(locales));

            // إضافة بيانات الصورة بناءً على حالة المستخدم
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            } else if (isImageMarkedForDeletion) {
                formData.append('image', ''); // إرسال قيمة فارغة للإشارة إلى الحذف
            }

            // تزييف طريقة الطلب إلى PUT
            formData.append('_method', 'PUT');

            await multipartApi.post(getRoute('api.category.update'), formData);

            Swal.fire({
                icon: 'success', title: translate('update_success_title'), text: translate('update_success_text'),
                timer: 2000, showConfirmButton: false
            }).then(() => window.location.href = getRoute('dashboard.category.index'));

        } catch (error) {
            if (error.response && error.response.status === 422) {
                displayValidationErrors(error.response.data.errors);
            }
            Swal.fire({
                icon: 'error', title: translate('error_title'),
                text: error.response?.data?.message || translate('form_error_text')
            });
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = translate('save');
        }
    });
}

// --- 5. تشغيل الإعداد عند تحميل الصفحة ---
document.addEventListener('DOMContentLoaded', setupEditForm);