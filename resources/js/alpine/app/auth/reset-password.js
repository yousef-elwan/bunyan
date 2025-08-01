// resources/js/pages/reset-password.js

import { PasswordInputManager } from '../../utils/passwordManager.js';
import { PasswordGenerator } from '../../utils/passwordGenerator.js';
import { http } from '../../utils/api.js';

document.addEventListener('DOMContentLoaded', () => {
    // تهيئة مدير كلمات المرور
    const passwordManager = PasswordInputManager.init();

    // تهيئة مولد كلمات المرور
    const passwordGenerator = new PasswordGenerator('passwordGeneratorModal');

    // تهيئة فتح مولد كلمات المرور
    const openGeneratorBtn = document.getElementById('passwordGenerateBtn');
    if (openGeneratorBtn) {
        openGeneratorBtn.addEventListener('click', () => {
            passwordGenerator.openModal();

            // ربط الحقول بعد فتح المودال
            const mainPasswordInput = document.getElementById('resetPasswordInput');
            const confirmPasswordInput = document.getElementById('resetConfirmPasswordInput');

            if (mainPasswordInput && confirmPasswordInput) {
                passwordGenerator.generatedInput.addEventListener('input', () => {
                    mainPasswordInput.value = passwordGenerator.generatedInput.value;
                    confirmPasswordInput.value = passwordGenerator.generatedInput.value;

                    // تحديث أيقونات المدير
                    passwordManager.updateIcon('resetPasswordInput');
                    passwordManager.updateIcon('resetConfirmPasswordInput');
                });
            }
        });
    }

    // معالجة إرسال النموذج
    const resetForm = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const statusDisplay = document.getElementById('status-display');

    if (resetForm) {
        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            statusDisplay.innerHTML = '';
            statusDisplay.style.display = 'none';
            submitBtn.disabled = true;

            const spinner = submitBtn.querySelector('.spinner-border');
            if (spinner) spinner.classList.remove('d-none');

            try {
                const formData = new FormData(resetForm);
                var response = await http().post(resetForm.action, formData);

                response = response.data;

                statusDisplay.className = 'alert alert-success';
                statusDisplay.innerHTML = response.message;
                resetForm.style.display = 'none';

                setTimeout(() => {
                    window.location.href = response.data?.redirect || '/';
                }, 3000);

            } catch (error) {
                var message = error.response?.data?.message ?? null;

                statusDisplay.className = 'alert alert-danger';
                statusDisplay.innerHTML = message || 'A network error occurred. Please try again.';
            } finally {
                statusDisplay.style.display = 'block';
                submitBtn.disabled = false;
                if (spinner) spinner.classList.add('d-none');
            }
        });
    }
});
