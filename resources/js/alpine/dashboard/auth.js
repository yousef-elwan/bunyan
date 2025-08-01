import Swal from 'sweetalert2';
import { getRoute, translate } from '../utils/helpers.js';
import { http } from '../utils/api.js';

function initAuthModal() {
    const logoutButtons = document.querySelectorAll('.js-logoutButton');

    logoutButtons.forEach(logoutButton => {
        logoutButton.addEventListener('click', (event) => {
            event.preventDefault();

            // if (!window.AppConfig.csrfToken) {
            //     Swal.fire({
            //         icon: 'error',
            //         title: translate('error_title'),
            //         html: translate('csrf_error'),
            //     });
            //     return;
            // }

            Swal.fire({
                icon: 'warning',
                title: translate('confirm_logout_title'),
                html: translate('confirm_logout_text'),
                showCancelButton: true,
                confirmButtonText: translate('confirm_logout_yes'),
                cancelButtonText: translate('cancel_button_text'),
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
            }).then((result) => {
                if (result.isConfirmed) {
                    performLogout();
                }
            });
        });
    });

    async function performLogout() {
        try {
            const response = await http().post(getRoute('auth.logout'), {});
            const data = response.data;
            console.log(data );

            await Swal.fire({
                icon: 'success',
                title: translate('logged_out_title') || 'Logged Out!',
                html: data.message || translate('logged_out_successfully'),
                confirmButtonText: translate('ok_button_text'),
                timer: 1000,
                willClose: () => {
                    window.location.href = data.data?.redirect || getRoute('home');
                }
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: translate('error_title'),
                html: translate('logout_failed_message'),
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initAuthModal();
});
