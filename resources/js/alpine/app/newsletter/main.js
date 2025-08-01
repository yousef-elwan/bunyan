import { http } from '../../utils/api';
import Swal from 'sweetalert2';
import { getRoute } from '../../utils/helpers';

export function initNewsletter() {
    const newsletterForm = document.getElementById('newsletterForm');

    if (!newsletterForm) return;

    newsletterForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('newsletterEmail').value.trim();
        const api = http({
            onStatusCodeError: {
                "400": () => { 
                    
                }
            }
        }); 

        if (!validateEmail(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address'
            });
            return;
        }

        try {
            const response = await api.post(getRoute('newsletter.subscribe'), { email });

            Swal.fire({
                icon: 'success',
                title: 'Subscription Sent!',
                text: response.data.message || 'Confirmation email has been sent'
            });

            newsletterForm.reset();
        } catch (error) {
            const errorMsg = error.response?.data?.message || 'Subscription failed';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg
            });
        }
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}