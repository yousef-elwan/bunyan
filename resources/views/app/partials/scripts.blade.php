@php
    $assetUrl = Storage::disk('asset')->url('');
@endphp

<script>
    // class PasswordInputInput {
    //     constructor(inputSelector = '.password-input') {
    //         this.inputs = document.querySelectorAll(inputSelector);
    //         this.instances = new Map();
    //         if (!this.inputs.length) return;
    //         this.inputs.forEach((input) => {

    //             // console.log(input);

    //             const togglePassword = document.getElementById(input.getAttribute('data-toggleId'));
    //             const eyeIcon = document.getElementById(input.getAttribute('data-eyeIconId'));

    //             togglePassword?.addEventListener('click', function() {
    //                 const type = input.getAttribute('type') === 'password' ? 'text' :
    //                     'password';
    //                 input.setAttribute('type', type);

    //                 // Toggle eye icon fill
    //                 if (type === 'text') {
    //                     eyeIcon.innerHTML =
    //                         '<path d="M17.94 17.94L6.06 6.06M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    //                 } else {
    //                     eyeIcon.innerHTML =
    //                         '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    //                 }
    //             });
    //         });
    //     }
    // }
    // document.addEventListener('DOMContentLoaded', () => {
    //     window.globalPasswordInput = new PasswordInputInput();
    // });
</script>

<!-- SwiperJS JS CDN -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>