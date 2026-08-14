const CACHE_VERSION = 'sneakyard-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const PAGE_CACHE = `${CACHE_VERSION}-pages`;
const STATIC_ASSETS = [
    '/offline',
    '/manifest.webmanifest',
    '/images/icons/icon-192.png',
    '/images/icons/icon-512.png',
    '/images/brand/sneakyard-profile-original.png',
    '/images/brand/sneakyard-monogram.png',
    '/images/storefront/hero-authentic-always.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(STATIC_CACHE).then((cache) => cache.addAll(STATIC_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((key) => !key.startsWith(CACHE_VERSION)).map((key) => caches.delete(key)))),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin || url.pathname.startsWith('/admin') || ['/checkout', '/bag'].includes(url.pathname)) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) caches.open(PAGE_CACHE).then((cache) => cache.put(request, response.clone()));
                    return response;
                })
                .catch(async () => (await caches.match(request)) || caches.match('/offline')),
        );
        return;
    }

    if (['image', 'style', 'script', 'font'].includes(request.destination)) {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request).then((response) => {
                if (response.ok) caches.open(STATIC_CACHE).then((cache) => cache.put(request, response.clone()));
                return response;
            })),
        );
    }
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data?.url || '/admin'));
});
