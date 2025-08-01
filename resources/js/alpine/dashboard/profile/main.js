import Swal from 'sweetalert2';
import { getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';
import Alpine from 'alpinejs';

const jsonApi = http();
const multipartApi = http({ multipart: true });

Alpine.data('profilePage', () => ({
    activeTab: 'tab-personal-info',

    setActiveTab(tabId) {
        this.activeTab = tabId;
        if (history.replaceState) {
            const url = new URL(window.location);
            url.hash = '#' + tabId;
            history.replaceState(null, null, url.toString());
        } else {
            window.location.hash = '#' + tabId;
        }
    },

    init() {
        const initialTab = window.location.hash.substring(1);
        if (initialTab && ['tab-personal-info', 'tab-security', 'tab-notifications', 'tab-preferences'].includes(initialTab)) {
            this.activeTab = initialTab;
        }
        setupNonAlpineFeatures();
    }
}));


function setupNonAlpineFeatures() {

    const displayFormErrors = (form, errors) => {

        clearFormErrors(form);
        // إزالة التنسيق الخطأ من جميع الحقول
        // form.querySelectorAll('.border-danger').forEach(el =>
        //     el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger')
        // );

        // // مسح جميع رسائل الخطأ القديمة
        // Object.keys(errors).forEach(field => {
        //     const errorElement = document.getElementById(`${field}_error`);
        //     if (errorElement) errorElement.textContent = '';
        // });

        // عرض الأخطاء الجديدة
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            const errorElement = document.getElementById(`${field}_error`);

            if (input) {
                input.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
            }

            if (errorElement && messages.length > 0) {
                errorElement.textContent = messages[0];
            }
        }

        // setupAvatarDeletion();

    };

    const setupAvatarUpload = () => {
        const avatarUploadInput = document.getElementById('avatarUpload');
        const avatarPreview = document.getElementById('profileAvatarPreview');
        const headerAvatar = document.getElementById('headerUserAvatar');
        const slideBarUserAvatar = document.getElementById('slideBarUserAvatar');

        if (avatarUploadInput && avatarPreview) {
            avatarUploadInput.addEventListener('change', async function (event) {
                const file = event.target.files[0];
                if (!file) return;

                // إظهار رسالة تأكيد
                const result = await Swal.fire({
                    title: translate('avatar_upload_confirm_title'),
                    text: translate('avatar_upload_confirm_text'),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: translate('confirm_upload_button'),
                    cancelButtonText: translate('cancelButton')
                });

                if (!result.isConfirmed) {
                    avatarUploadInput.value = '';
                    return;
                }

                const imageUrl = URL.createObjectURL(file);

                avatarPreview.src = imageUrl;

                // تحديث صورة الهيدر
                if (headerAvatar) {
                    headerAvatar.src = imageUrl;
                }

                if (slideBarUserAvatar) {
                    slideBarUserAvatar.src = imageUrl;
                }

                const formData = new FormData();
                formData.append('image', file);

                try {
                    await multipartApi.post(getRoute('api.auth.updateAvatar'), formData);
                    Swal.fire({
                        icon: 'success',
                        title: translate('success_title'),
                        text: translate('avatar_upload_success'),
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // إظهار زر الحذف بعد التحديث
                    // alpineContext.showDeleteButton = true;
                    const avatarContainer = avatarPreview.closest('[x-data]');
                    if (avatarContainer) {
                        const alpineComponent = Alpine.$data(avatarContainer);
                        alpineComponent.showDeleteButton = true;
                    }

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: translate('error_title'),
                        text: error.response?.data?.message || translate('avatar_upload_error')
                    });
                }
            });
        }
    };


    const setupPhoneInput = () => {
        const phoneInput = document.querySelector('#mobile');
        if (!phoneInput || typeof window.intlTelInput !== 'function') return null;
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "sy",
            geoIpLookup: cb => fetch("https://ipapi.co/json").then(r => r.json()).then(d => cb(d.country_code)).catch(() => cb("us")),
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            preferredCountries: ['sy', 'sa', 'ae', 'eg', 'jo', 'lb'],
            separateDialCode: true,
        });
        if (window.AppConfig?.user?.mobile) iti.setNumber(window.AppConfig.user.mobile);
        return iti;
    };

    const iti = setupPhoneInput();

    const setupPasswordToggles = () => {
        // document.querySelectorAll('.password-toggle-icon').forEach(icon => {
        //     icon.addEventListener('click', function () {
        //         const passwordInput = this.previousElementSibling;
        //         if (passwordInput) {
        //             const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        //             passwordInput.setAttribute('type', type);
        //             this.classList.toggle('fa-eye');
        //             this.classList.toggle('fa-eye-slash');
        //         }
        //     });
        // });
        document.querySelectorAll('.password-toggle-icon').forEach(button => {
            button.addEventListener('click', function () {
                // UPDATED: The input is now the button's previous sibling element
                const passwordInput = this.previousElementSibling;
                if (passwordInput && passwordInput.tagName === 'INPUT') {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle the icon class on the <i> element inside the button
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                }
            });
        });
    };

    const setupLanguageSwitcher = () => {
        const languageSelect = document.getElementById('languageSelect');
        if (!languageSelect) return;
        languageSelect.addEventListener('change', function () {
            const url = this.value;
            Swal.fire({
                title: 'Changing Language...', text: 'Please wait.',
                allowOutsideClick: false, didOpen: () => Swal.showLoading()
            });
            window.location.href = url;
        });
    };
    const clearFormErrors = (form) => {
        // إزالة تنسيق الخطأ من جميع الحقول
        form.querySelectorAll('.border-danger').forEach(el => {
            el.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger');
        });

        // مسح جميع رسائل الخطأ
        form.querySelectorAll('[id$="_error"]').forEach(el => {
            el.textContent = '';
        });
    };
    const handleInfoForm = () => {
        const infoForm = document.getElementById('updateInfoForm');
        if (infoForm) {
            infoForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                clearFormErrors(infoForm);

                const submitBtn = infoForm.querySelector('button[type="submit"]');
                if (iti && !iti.isValidNumber()) {
                    Swal.fire({ icon: 'error', title: translate('error_title'), text: translate('invalid_phone') });
                    return;
                }
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('saving')}`;

                try {
                    const formData = new FormData(infoForm);
                    if (iti) formData.set('mobile', iti.getNumber());
                    const payload = Object.fromEntries(formData.entries());
                    await jsonApi.post(getRoute('api.auth.updateInfo'), payload);
                    Swal.fire({ icon: 'success', title: translate('success_title'), text: translate('info_update_success'), timer: 1500, showConfirmButton: false });

                    const fullName = `${payload.first_name} ${payload.last_name}`;

                    document.getElementById('profilePageNameDisplay').textContent = fullName;
                    document.getElementById('headerUserName').textContent = fullName;
                    document.getElementById('sidebarUserName').textContent = fullName;

                    document.getElementById('profilePagePhoneDisplay').textContent = payload.mobile;
                } catch (error) {
                    if (error.response?.status === 422) displayFormErrors(infoForm, error.response.data.errors);
                    else Swal.fire({ icon: 'error', title: translate('error_title'), text: error.response?.data?.message || translate('info_update_error') });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = translate('save');
                }
            });

            // UPDATED: Custom reset logic for info form
            infoForm.addEventListener('reset', (e) => {
                e.preventDefault();
                clearFormErrors(infoForm);
                const userData = window.AppConfig.user;
                if (userData) {
                    infoForm.querySelector('[name="first_name"]').value = userData.first_name;
                    infoForm.querySelector('[name="last_name"]').value = userData.last_name;
                    if (iti) {
                        iti.setNumber(userData.mobile || '');
                    }
                }
            });
        }
    };

    const handlePasswordForm = () => {
        const passwordForm = document.getElementById('updatePasswordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                clearFormErrors(passwordForm);

                const submitBtn = passwordForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('updatingPassword')}`;

                try {
                    const payload = Object.fromEntries(new FormData(passwordForm).entries());
                    if (payload.new_password !== payload.new_password_confirmation) {
                        throw new Error(translate('password_mismatch'));
                    }
                    await jsonApi.post(getRoute('api.auth.updatePassword'), payload);
                    Swal.fire({ icon: 'success', title: translate('success_title'), text: translate('password_update_success'), timer: 1500, showConfirmButton: false });
                    passwordForm.reset();
                } catch (error) {

                    clearFormErrors(passwordForm);

                    if (error.response?.status === 422) displayFormErrors(passwordForm, error.response.data.errors);
                    else Swal.fire({ icon: 'error', title: translate('error_title'), text: error.message || error.response?.data?.message || translate('password_update_error') });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = translate('changePassword');
                }
            });

            // ADDED: Event listener for form reset
            passwordForm.addEventListener('reset', () => {
                clearFormErrors(passwordForm);
            });
        }
    };

    const handleNotificationsForm = () => {
        const notificationsForm = document.getElementById('notificationSettingsForm');
        if (notificationsForm) {
            notificationsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = notificationsForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${translate('saving')}`;

                try {
                    const formData = new FormData(notificationsForm);
                    const payload = Object.fromEntries(formData.entries());
                    await jsonApi.post(getRoute('api.auth.updateNotifications'), payload);
                    Swal.fire({
                        icon: 'success',
                        title: translate('success_title'),
                        text: translate('notifications_update_success'),
                        timer: 1500,
                        showConfirmButton: false
                    });
                } catch (error) {
                    if (error.response?.status === 422) {
                        displayFormErrors(notificationsForm, error.response.data.errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: translate('error_title'),
                            text: error.response?.data?.message || translate('notifications_update_error')
                        });
                    }
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = translate('save_settings');
                }
            });

            // UPDATED: Custom reset logic for notifications form
            notificationsForm.addEventListener('reset', (e) => {
                e.preventDefault();
                clearFormErrors(notificationsForm);
                const userData = window.AppConfig.user;
                if (userData) {
                    const emailCheckbox = notificationsForm.querySelector('[name="email_notifications"]');
                    const newsletterCheckbox = notificationsForm.querySelector('[name="newsletter_notifications"]');
                    if (emailCheckbox) emailCheckbox.checked = userData.email_notifications == '1';
                    if (newsletterCheckbox) newsletterCheckbox.checked = userData.newsletter_notifications == '1';
                }
            });
        }
    };

    const setupAvatarDeletion = () => {
        const deleteBtn = document.getElementById('deleteAvatarBtn');
        const avatarPreview = document.getElementById('profileAvatarPreview');
        const headerAvatar = document.getElementById('headerUserAvatar');
        const slideBarUserAvatar = document.getElementById('slideBarUserAvatar');

        if (!deleteBtn || !avatarPreview) return;

        deleteBtn.addEventListener('click', async () => {
            const result = await Swal.fire({
                title: translate('confirmDeleteTitle'),
                text: translate('confirmDeleteText'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: translate('confirmDeleteButton'),
                cancelButtonText: translate('cancelButton')
            });

            if (!result.isConfirmed) return;

            try {
                const response = await jsonApi.delete(getRoute('api.auth.deleteAvatar'));

                const data = response.data.data;
                const default_avatar_url = data.default_avatar_url;
                avatarPreview.src = default_avatar_url;

                // تحديث صورة الهيدر
                if (headerAvatar) {
                    headerAvatar.src = default_avatar_url;
                }
                if (slideBarUserAvatar) {
                    slideBarUserAvatar.src = default_avatar_url;
                }

                // إخفاء زر الحذف بعد الحذف
                // alpineContext.showDeleteButton = false;
                const avatarContainer = avatarPreview.closest('[x-data]');
                if (avatarContainer) {
                    const alpineComponent = Alpine.$data(avatarContainer);
                    alpineComponent.showDeleteButton = false;
                }

                Swal.fire({
                    icon: 'success',
                    title: translate('deletedSuccessTitle'),
                    text: translate('deletedSuccessText'),
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (error) {
                throw error;
                Swal.fire({
                    icon: 'error',
                    title: translate('error_title'),
                    text: error.response?.data?.message || translate('networkError')
                });
            }
        });
    };

    // Initialize all parts
    setupAvatarUpload();
    setupPasswordToggles();
    setupLanguageSwitcher();
    handleInfoForm();
    handlePasswordForm();
    handleNotificationsForm();
    setupAvatarDeletion();
}