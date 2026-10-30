/**
 * PRANATA HR - Progressive Web App Service Worker
 */

const CACHE_NAME = 'pranata-hr-pwa-v1.0.1';

// Essential assets to cache on install for offline fallback
const PRECACHE_ASSETS = [
    '/offline.html',
    '/favicon.ico',
    '/manifest.json',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/apple-touch-icon.png',
    '/css/pwa-install.css',
    '/js/pwa-install.js'
];

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                PRECACHE_ASSETS.map((url) => {
                    return cache.add(url).catch((err) => {
                        console.warn('[PWA SW] Precache missed:', url, err);
                    });
                })
            );
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - clean up obsolete caches
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
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Ignore non-GET requests (e.g. POST for login, clock in/out, form submits)
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Only handle HTTP/HTTPS (ignore chrome-extension, etc.)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // 1. Navigation request (HTML pages) -> Network first, fallback to offline.html
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(async () => {
                const cache = await caches.open(CACHE_NAME);
                const offlineResp = await cache.match('/offline.html');
                if (offlineResp) {
                    return offlineResp;
                }
                return new Response('Offline - PRANATA HR', {
                    headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                });
            })
        );
        return;
    }

    // 2. Static assets (images, icons, css, js, fonts) -> Stale-while-revalidate or Cache first
    const isStaticAsset = (
        request.destination === 'image' ||
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'font' ||
        url.pathname.includes('/images/icons/')
    );

    if (isStaticAsset) {
        event.respondWith(
            caches.open(CACHE_NAME).then(async (cache) => {
                const cachedResponse = await cache.match(request);
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && (networkResponse.status === 200 || networkResponse.status === 0)) {
                        cache.put(request, networkResponse.clone());
                    }
                    return networkResponse;
                }).catch(() => {
                    // Ignore network errors for background cache updates
                });

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Default: Network fetch
    event.respondWith(fetch(request));
});
