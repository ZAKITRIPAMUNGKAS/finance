const CACHE_NAME = 'portofinance-v3';
const STATIC_ASSETS = [
    '/favicon.png',
    '/images/logofinance.png',
    '/images/logo.svg',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(() => {});
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
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Only intercept static assets like images, fonts, and build chunks
    const isStaticAsset = url.pathname.startsWith('/build/') || 
                          url.pathname.startsWith('/images/') || 
                          url.pathname === '/favicon.png' || 
                          url.pathname === '/manifest.json';

    if (!isStaticAsset) {
        // Let standard page navigation & livewire requests go directly to network
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }
            return fetch(event.request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            }).catch(() => {
                // If offline and not in cache, fallback
                return new Response('', { status: 408, statusText: 'Offline' });
            });
        })
    );
});
