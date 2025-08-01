import { openModal, closeModal } from './modals';
import { showAuthRequired, showModalMessage, displayActionFormErrors, clearActionFormErrors, getRoute, translate } from '../../utils/helpers';
import { createPhoneInputValidator, initializePhoneInput } from '../../utils/phoneInput';
import { http } from '../../utils/api';

export function initReportAction() {
    console.log('[ReportAction] initReportAction() CALLED');

    const reportPropertyModal = document.getElementById('reportPropertyModal');
    const reportForm = document.getElementById('reportFormV3');
    const openReportModalBtns = document.querySelectorAll('.report-btn-trigger-v3');


    if (openReportModalBtns.length === 0) {
        console.log('[ReportAction] No .report-btn-trigger-v3 buttons found.');
        // No trigger buttons, so modal and form logic might not be needed unless opened programmatically elsewhere.
        // If modal exists without triggers, it could be an issue.
        if (reportPropertyModal) console.warn('[ReportAction] #reportPropertyModal exists, but no .report-btn-trigger-v3 found.');
        return;
    }

    if (!reportPropertyModal) {
        console.warn('[ReportAction] .report-btn-trigger-v3 found, but #reportPropertyModal is missing.');
        return; // Cannot proceed without the modal
    }
    if (!reportForm) {
        console.warn('[ReportAction] #reportPropertyModal found, but #reportFormV3 is missing.');
        // Modal might still be usable for display, but form submission will fail.
    }

    const closeReportModalBtn = reportPropertyModal.querySelector('[data-close-report-modal]');
    let currentReportPropertyId = null; // Stores the property ID for the current report context

    // User detail fields (for guest reporting, hidden if authenticated)
    const reportUserNameInput = document.getElementById('reportUserName');
    const reportUserEmailInput = document.getElementById('reportUserEmail');
    const reportUserMobileInput = document.getElementById('reportUserMobile');
    const closeReportBtn = document.getElementById('closeReportBtn');
    const userDetailsGroups = reportPropertyModal.querySelectorAll('.user-details-group');



    const authUser = window.AppConfig.user;
    const currentUserMobile = authUser?.mobile || '';

    const itiInstance = initializePhoneInput(reportUserMobileInput, currentUserMobile);
    const phoneInputValidator = createPhoneInputValidator(
        itiInstance,
        reportUserMobileInput,
        reportForm.querySelector('.invalid-feedback[data-field="mobile"]'),
        translate
    );
    phoneInputValidator.setupEventListeners();

    /**
     * Opens the report dialog for a given property ID.
     * Resets the form and manages visibility of user detail fields based on auth status.
     * @param {string} propertyIdToReport - The ID of the property to be reported.
     */
    function openReportDialog(propertyIdToReport) {
        currentReportPropertyId = propertyIdToReport;
        if (reportForm) {
            reportForm.reset();
            // Field names for reportForm: 'type_id', 'message', 'name', 'email', 'mobile'
            // Map API error keys to form input names if they differ for clearActionFormErrors.
            // Here, 'type_id' and 'message' from API map to 'reportReason' and 'reportDetails' in form (as handled by displayActionFormErrors).
            // So, we clear using API keys for consistency with display.
            clearActionFormErrors(reportForm, ['type_id', 'message', 'name', 'email', 'mobile']);

            if (window.AppConfig.isAuthenticated) {
                userDetailsGroups.forEach(group => group.style.display = 'none');
            } else {
                // Only show user detail fields if guest reporting is allowed on the form
                if (reportForm.classList.contains('allow-guest-submission')) {
                    userDetailsGroups.forEach(group => group.style.display = 'block');
                    // Optional: Clear and enable fields (reset should handle clearing)
                    if (reportUserNameInput) reportUserNameInput.readOnly = false;
                    if (reportUserEmailInput) reportUserEmailInput.readOnly = false;
                    if (reportUserMobileInput) reportUserMobileInput.readOnly = false;
                } else {
                    userDetailsGroups.forEach(group => group.style.display = 'none');
                }
            }
        }
        openModal(reportPropertyModal)
        reportPropertyModal.style.display = 'flex'; // Or Bootstrap modal('show')
    }

    /** Closes the report dialog, resets state, and restores submit button. */
    function closeReportDialog() {
        reportPropertyModal.style.display = 'none'; // Or Bootstrap modal('hide')
        currentReportPropertyId = null;
        if (reportForm) {
            const submitBtn = reportForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                // Restore original text, ensure translation key exists
                submitBtn.textContent = translate('report_submit_button_text') || 'Submit Report';
            }
        }
        closeModal(reportPropertyModal);
    }

    openReportModalBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const propId = this.dataset.propertyId;
            console.log('[ReportAction] OpenReportBtn clicked, data-property-id:', propId);

            // Check if guest reporting is allowed for this specific button or form context
            const allowGuest = this.classList.contains('allow-guest-report') ||
                (reportForm && reportForm.classList.contains('allow-guest-submission'));

            if (!window.AppConfig.isAuthenticated && !allowGuest) {
                showAuthRequired();
                return;
            }

            if (propId) {
                openReportDialog(propId);
            }
        });
    });

    if (closeReportModalBtn) closeReportModalBtn.addEventListener('click', closeReportDialog);

    reportPropertyModal.addEventListener('click', (event) => { // Close on overlay click
        if (event.target === reportPropertyModal) closeReportDialog();
    });

    closeReportBtn.addEventListener('click', (event) => {
        closeReportDialog();
    });
    document.addEventListener('keydown', (event) => { // Close on Escape
        if (event.key === 'Escape' && reportPropertyModal.style.display === 'flex') {
            closeReportDialog();
        }
    });

    if (reportForm) {
        reportForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log('[ReportAction] Report form submitted for propertyId:', currentReportPropertyId);

            if (!currentReportPropertyId) {
                showModalMessage(
                    'error',
                    {
                        title: translate('error_title'),
                        bodyHtml: translate('generic_error_try_again'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
                return;
            }

            const allowGuestSubmission = reportForm.classList.contains('allow-guest-submission');
            if (!window.AppConfig.isAuthenticated && !allowGuestSubmission) { // Security check
                showAuthRequired();
                return;
            }

            // if (!window.AppConfig.csrfToken) {
            //     showModalMessage(
            //         'error',
            //         {
            //             title: translate('error_title'),
            //             bodyHtml: translate('csrf_error'),
            //             buttons: [
            //                 {
            //                     text: translate('close'),
            //                     class: 'my-modal-btn-primary'
            //                 }
            //             ],
            //             showCloseIcon: true
            //         });
            //     return;
            // }
            if (!getRoute('properties.submit-report')) {
                showModalMessage(
                    'error',
                    {
                        title: translate('error_title'),
                        bodyHtml: translate('generic_error_try_again'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
                return;
            }

            const submitBtn = reportForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : (translate('report_submit_button_text'));
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = translate('submitting');
            }

            // Clear previous errors for these specific fields
            // Note: API error keys 'type_id', 'message' map to form input names 'reportReason', 'reportDetails'
            // The displayActionFormErrors helper needs to know this mapping if not handled internally
            // const errorFieldMap = { 'type_id': 'reportReason', 'message': 'reportDetails' };
            clearActionFormErrors(reportForm, ['type_id', 'message', 'name', 'email', 'mobile']);


            const formData = new FormData(reportForm);

            const reportData = {
                type_id: formData.get('reportReason') || '',
                message: formData.get('reportDetails') || ''
            };


            if (!window.AppConfig.isAuthenticated && allowGuestSubmission) {
                reportData.name = reportUserNameInput?.value || '';
                reportData.email = reportUserEmailInput?.value || '';

                const rawPhone = formData.get('mobile');
                let fullPhone = rawPhone;
                if (phoneInputValidator) {
                    if (rawPhone && rawPhone.trim() !== '' && !phoneInputValidator.validate()) {
                        displayActionFormErrors(reportForm, { mobile: [translate('phone_invalid')] }, true);
                        showModalMessage(
                            'error',
                            {
                                bodyHtml: translate('phone_invalid'),
                                buttons: [
                                    {
                                        text: translate('close'),
                                        class: 'my-modal-btn-primary'
                                    }
                                ],
                                showCloseIcon: true
                            });
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalBtnText;
                        }
                        return;
                    }
                }
                if (phoneInputValidator.getInstance()?.isValidNumber()) { // Only get number if valid to avoid errors
                    fullPhone = phoneInputValidator.getInstance()?.getNumber() || rawPhone;
                } else if (!rawPhone || rawPhone.trim() === '') {
                    fullPhone = ""; // Ensure empty if raw input is empty
                }
                reportData.mobile = fullPhone;

            }

            try {

                const apiUrl = getRoute('properties.submit-report').replace('{propertyId}', currentReportPropertyId);

                const response = await http({
                    onStatusCodeError: {
                        401: (error) => {
                            const message = error?.response?.data?.message;
                            showModalMessage?.(
                                'error',
                                {
                                    bodyHtml: message || translate('auth_required_message'),
                                    buttons: [
                                        {
                                            text: translate('ok_button_text'),
                                            class: 'my-modal-btn-primary'
                                        }
                                    ],
                                    showCloseIcon: true
                                });
                            closeReportDialog();
                        },
                        422: (error) => {
                            const message = error?.response?.data?.message;
                            const errors = error?.response?.data?.errors;
                            displayActionFormErrors(reportForm, errors, false);
                            showModalMessage(
                                'error',
                                {
                                    bodyHtml: message,
                                    buttons: [
                                        {
                                            text: translate('close'),
                                            class: 'my-modal-btn-primary'
                                        }
                                    ],
                                    showCloseIcon: true
                                });
                        }
                    }
                }).post(apiUrl, JSON.stringify(reportData));


                const data = response.data;
                if (data.success === true && data.data) {
                    closeReportDialog();
                    showModalMessage(
                        'success',
                        {
                            bodyHtml: data.message,
                            timerSeconds: 1,
                            showCloseIcon: true
                        });
                } else {
                    showModalMessage(
                        'error',
                        {
                            bodyHtml: data.message,
                            buttons: [
                                {
                                    text: translate('ok_button_text'),
                                    class: 'my-modal-btn-primary'
                                }
                            ],
                            showCloseIcon: true
                        });
                }


                // const response = await fetch(getRoute('properties.submit-report'), {
                //     method: 'POST',
                //     headers: {
                //         'X-CSRF-TOKEN': window.AppConfig.csrfToken,
                //         'Accept': 'application/json',
                //         'X-Requested-With': 'XMLHttpRequest',
                //         'Content-Type': 'application/json'
                //     },
                //     body: JSON.stringify(reportData)
                // });
                // const data = await response.json();

                // if (response.ok && data.success === true) {
                //     closeReportDialog(); // Resets form via its own logic
                //     showModalMessage('success', {
                //         bodyHtml: data.message || translate('report_submit_success'),
                //         timerSeconds: 1,
                //         showCloseIcon: true
                //     });
                // } else if (response.status === 422 && data.errors) {
                //     // API returns errors with keys like 'message', 'message'.
                //     // Our displayActionFormErrors can map them if configured, e.g. 'message' -> 'reportReason' input.
                //     displayActionFormErrors(reportForm, data.errors, false);
                //     showModalMessage(
                //         'error',
                //         {
                //             bodyHtml: data.message || translate('validation_error'),
                //             buttons: [
                //                 {
                //                     text: translate('close'),
                //                     class: 'my-modal-btn-primary'
                //                 }
                //             ],
                //             showCloseIcon: true
                //         });

                // } else if (response.status === 401) {
                //     showModalMessage(
                //         'error',
                //         {
                //             bodyHtml: data.message || translate('auth_required_message'),
                //             buttons: [
                //                 {
                //                     text: translate('close'),
                //                     class: 'my-modal-btn-primary'
                //                 }
                //             ],
                //             showCloseIcon: true
                //         });
                // } else {
                //     showModalMessage(
                //         'error',
                //         {
                //             bodyHtml: data.message || translate('report_submit_failed'),
                //             buttons: [
                //                 {
                //                     text: translate('close'),
                //                     class: 'my-modal-btn-primary'
                //                 }
                //             ],
                //             showCloseIcon: true
                //         });
                // }
            } catch (error) {
                console.error('[ReportAction] Report API submission error:', error);
                showModalMessage(
                    'error',
                    {
                        bodyHtml: translate('network_error'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
            } finally {
                closeReportDialog();
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            }
        });
    }
}
