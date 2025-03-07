import Echo from 'laravel-echo';
import Reverb from '@mrelmiz/reverb-client';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? `${window.location.hostname}:8080`,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

Echo.private(`notifications.${userId}`)
    .listen('NewNotificationEvent', (e) => {
        Notification.make()
            .title(e.notification.title)
            .body(e.notification.body)
            .send();
    });
