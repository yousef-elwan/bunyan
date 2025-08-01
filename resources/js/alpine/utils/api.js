import axios from 'axios';
import { showModalMessage, translate } from './helpers';

export function http({
    baseURL = '/',
    multipart = false,
    onStatusCodeError = {},
    customOnRejected = null
} = {}) {

    const instance = axios.create({
        baseURL,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': multipart ? 'multipart/form-data' : 'application/json',
            // 'Authorization': `Bearer ${window.AppConfig.apiToken}`,
            'Accept-Language': window.AppConfig.locale,
            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        withCredentials: true,
    });

    // const token = document.querySelector('meta[name="csrf-token"]');
    // if (token) {
    //     instance.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // }

    instance.interceptors.response.use(
        response => response,
        error => {
            const status = error?.response?.status;
            const message = error?.response?.data?.message;
            // const validationErrors = error?.response?.data?.errors;

            if (customOnRejected) {
                customOnRejected(error);
                return Promise.reject(error);
            }

            if (onStatusCodeError[status]) {
                onStatusCodeError[status](error);
            } else {
                switch (status) {
                    case 500:
                    default:
                        const networkErrorMsg = message ?? translate('network_error');
                        showModalMessage?.(
                            'error',
                            {
                                title: translate('error_title'),
                                bodyHtml: networkErrorMsg,
                            }

                        );
                        break;

                }
            }

            return Promise.reject(error);
        }
    );

    return instance;
}
