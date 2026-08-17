const CACHE_NAME = 'portofinance-v1';
const STATIC_ASSETS = [
    '/',
    '/favicon.png',
    '/images/logo.svg',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('PWA cache.addAll non-critical warning:', err);
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Do not cache livewire dynamic updates or API/auth requests
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/livewire') || url.pathname.startsWith('/api') || url.pathname.startsWith('/logout')) {
        return;
    }

    // Network-first strategy with cache fallback
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // If response is valid, clone and update cache for static fonts/images
                if (response && response.status === 200 && (url.pathname.includes('/build/') || url.pathname.includes('/images/'))) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});
