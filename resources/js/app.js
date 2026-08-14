import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let deferredInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    document.querySelectorAll('[data-install-pwa]').forEach((button) => {
        button.hidden = false;
    });
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-install-pwa]');

    if (! button || ! deferredInstallPrompt) return;

    deferredInstallPrompt.prompt();
    await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;
    button.hidden = true;
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch((error) => {
            console.warn('Sneakyard service worker could not register.', error);
        });
    });
}

window.addEventListener('sneakyard:enable-notifications', async () => {
    if ('Notification' in window && Notification.permission === 'default') {
        await Notification.requestPermission();
    }
});

if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    if (document.body.dataset.admin === 'true') {
        window.Echo.private('admin.orders').listen('.order.placed', (order) => {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Sneakyard order', {
                    body: `${order.order_number} from ${order.customer_name}`,
                    icon: '/images/icons/icon-192.png',
                    data: { url: order.url },
                });
            }

            window.Livewire?.dispatch('notification-received');
        });
    }
}
