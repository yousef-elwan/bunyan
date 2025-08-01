import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { http } from './alpine/utils/api';

const basePath = new URL(import.meta.env.VITE_APP_URL).pathname;

const host = window.location.hostname;
const port = window.location.port || (window.location.protocol === 'https:' ? 443 : 80);
const scheme = window.location.protocol.replace(':', '');
const isSecure = scheme === 'https';

// const authEndpoint = `${window.location.origin}/broadcasting/auth`;

let origin = `${scheme}://${host}`;
if (port && ((scheme === 'http' && port !== '80') || (scheme === 'https' && port !== '443'))) {
    origin += `:${port}`;
}
const authEndpoint = `${origin}${basePath}/broadcasting/auth`;

window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: isSecure,
    enabledTransports: ['ws', 'wss'],
    // authEndpoint: authEndpoint,
    // auth: {
    //     headers: {
    //         'X-CSRF-Token': window.AppConfig?.csrfToken
    //     }
    // }
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                // 3. هنا نستخدم دالة http() للقيام بطلب المصادقة
                // هذا يضمن استخدام axios مع كل إعداداته الصحيحة (withCredentials, الخ)
                http().post(authEndpoint, {
                    socket_id: socketId,
                    channel_name: channel.name,
                })
                    .then(response => {
                        // في حالة النجاح، أرسل البيانات إلى Echo للمتابعة
                        callback(null, response.data);
                    })
                    .catch(error => {
                        // في حالة الفشل، أبلغ Echo بالخطأ
                        callback(error, null);
                    });
            }
        };
    },
});
