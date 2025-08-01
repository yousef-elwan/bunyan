
import { initToTop } from './alpine/app/common/toTop';
import { initHeader } from './alpine/app/common/header-drawer';
import { initAuthModal } from './alpine/app/common/auth-modal';
import { initNewsletter } from './alpine/app/newsletter/main';
import './alpine/utils/passwordStrengthChecker';
import Swal from 'sweetalert2';


function handleSweetAlerts() {
    const successMessage = document.getElementById('swal-success-message');
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: successMessage.textContent,
            showConfirmButton: false,
            timer: 3000
        });

        // Swal.fire({
        //     icon: 'success',
        //     title: successMessage.textContent,
        //     // position: 'top-end',
        //     // showConfirmButton: false,
        //     timer: 3000,
        //     // toast: true,
        //     background: '#f0fdf4',
        //     iconColor: '#16a34a',
        //     timerProgressBar: true,
        //     didOpen: (toast) => {
        //         toast.addEventListener('mouseenter', Swal.stopTimer)
        //         toast.addEventListener('mouseleave', Swal.resumeTimer)
        //     }
        // });
    }
}

initAuthModal()
initHeader()
initToTop()
initNewsletter()
handleSweetAlerts()
