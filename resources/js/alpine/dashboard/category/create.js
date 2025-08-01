import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

// تهيئة نسخة axios لإرسال بيانات FormData (للصور)
const api = http({ multipart: true });

/**
 * دالة لإعداد منطق فورم إنشاء فئة جديدة
 */
function setupCreateForm() {
    // --- 1. تحديد العناصر ---
    const createForm = document.getElementById('createCategoryForm');
    if (!createForm) return; // الخروج المبكر إذا لم يتم العثور على الفورم

    const saveBtn = document.getElementById('createCategoryBtn');
    const imageInput = document.getElementById('imageUpload');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const dropArea = document.getElementById('image-drop-area');

    // --- 2. الدوال المساعدة ---

    /**
     * عرض أخطاء التحقق من الصحة التي تأتي من السيرفر
     * @param {object} errors - كائن الأخطاء
     */
    const displayValidationErrors = (errors) => {
        // إزالة الأخطاء السابقة
        document.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        for (const [field, messages] of Object.entries(errors)) {
            const fieldParts = field.split('.');
            let input, errorElement;

            // معالجة حقول اللغات مثل 'locales.0.name'
            if (fieldParts[0] === 'locales' && fieldParts.length === 3) {
                const locale = window.AppConfig.pageData.locales[fieldParts[1]];
                input = document.getElementById(`name_${locale}`);
                errorElement = document.getElementById(`name_${locale}_error`);
            } else {
                // معالجة الحقول العادية مثل 'image'
                input = document.querySelector(`[name="${field}"]`);
                errorElement = document.getElementById(`${field}_error`);
            }

            if (input) input.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
            if (errorElement) errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
        }

        // التمرير إلى أول حقل به خطأ
        const firstError = document.querySelector('.border-danger, .invalid-feedback:not(:empty)');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    /**
     * معالجة عرض الصورة المختارة
     * @param {File|null} file - الملف المختار
     */
    const handleFileSelection = (file) => {
        if (!file) {
            imagePreviewContainer.innerHTML = `<p class="self-center w-full text-sm text-center text-text-secondary">${translate('no_image_preview')}</p>`;
            return;
        }

        // التحقق من حجم ونوع الملف
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
            imagePreviewContainer.innerHTML = `
                <div class="relative w-28 h-28 group">
                    <img src="${event.target.result}" alt="Image Preview" class="object-cover w-full h-full rounded-md border border-border-color">
                    <button type="button" class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none" title="${translate('button_remove_image')}">×</button>
                </div>`;
            imagePreviewContainer.querySelector('button').addEventListener('click', () => {
                imageInput.value = '';
                handleFileSelection(null);
            });
        };
        reader.readAsDataURL(file);
    };

    // --- 3. ربط الأحداث ---

    // عند اختيار ملف
    if (imageInput && imagePreviewContainer) {
        imageInput.addEventListener('change', (e) => handleFileSelection(e.target.files[0]));
    }

    // عند سحب وإفلات الملف
    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
            document.body.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
        });
        ['dragenter', 'dragover'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-over')));
        ['dragleave', 'drop'].forEach(eventName => dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-over')));
        dropArea.addEventListener('drop', e => {
            imageInput.files = e.dataTransfer.files;
            handleFileSelection(e.dataTransfer.files[0]);
        });
    }

    // عند إرسال الفورم
    createForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('saving')}`;
        displayValidationErrors({});

        try {
            const formData = new FormData(this);
            const locales = [];
            window.AppConfig.pageData.locales.forEach(locale => {
                locales.push({
                    locale: locale,
                    name: formData.get(`name_${locale}`),
                });
            });
            // إضافة مصفوفة اللغات كسلسلة نصية JSON
            formData.append('locales', JSON.stringify(locales));

            await api.post(getRoute('api.category.store'), formData);

            Swal.fire({
                icon: 'success', title: translate('create_success_title'), text: translate('create_success_text'),
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

// --- 4. تشغيل الإعداد عند تحميل الصفحة ---
document.addEventListener('DOMContentLoaded', setupCreateForm);