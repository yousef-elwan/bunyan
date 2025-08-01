import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

const api = http();

function setupEditForm() {
    const editForm = document.getElementById('editFloorForm');
    if (!editForm) return;

    const saveBtn = document.getElementById('updateFloorBtn');

    const displayValidationErrors = (errors) => {
        document.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger'));
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

            if (input) input.classList.add('border-danger');
            if (errorElement) errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
        document.querySelector('.border-danger, .invalid-feedback:not(:empty)')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    editForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('saving')}`;
        displayValidationErrors({});

        try {
            const formData = new FormData(this);
            const payload = {
                _method: 'PUT',
                value: formData.get('value') || null,
                locales: []
            };

            window.AppConfig.pageData.locales.forEach(locale => {
                payload.locales.push({
                    locale: locale,
                    name: formData.get(`name_${locale}`),
                });
            });

            await api.post(getRoute('api.floor.update'), payload);

            Swal.fire({
                icon: 'success', title: translate('update_success_title'), text: translate('update_success_text'),
                timer: 2000, showConfirmButton: false
            }).then(() => window.location.href = getRoute('dashboard.floor.index'));

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

document.addEventListener('DOMContentLoaded', setupEditForm);