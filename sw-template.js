importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
    // 1. Precaching Module (Satisfies requirement 1 & 2)
    // Grunt will replace self.__WB_MANIFEST with the actual hashed file list
    workbox.precaching.precacheAndRoute(self.__WB_MANIFEST);

    // 2. Cache External Fonts (FontAwesome)
    workbox.routing.registerRoute(
        ({url}) => url.origin === 'https://cdnjs.cloudflare.com',
        new workbox.strategies.CacheFirst({
            cacheName: 'font-awesome-cache',
            plugins: [
                new workbox.cacheableResponse.CacheableResponsePlugin({ statuses: [0, 200] }),
                new workbox.expiration.ExpirationPlugin({ maxAgeSeconds: 60 * 60 * 24 * 365, maxEntries: 10 }),
            ],
        })
    );

    // 3. Dynamic PHP Routing
    workbox.routing.registerRoute(
        ({url}) => url.pathname.endsWith('.php'),
        new workbox.strategies.NetworkFirst({
            cacheName: 'php-dynamic-pages'
        })
    );

    // 4. Offline Fallback Logic (Satisfies requirement 3)
    const OFFLINE_URL = '/offline.html';
    
    // Store the offline page immediately upon SW installation
    self.addEventListener('install', (event) => {
        event.waitUntil(
            caches.open('offline-fallback').then((cache) => cache.add(OFFLINE_URL))
        );
    });

    // If a network request fails and isn't cached, show the offline page
    workbox.routing.setCatchHandler(({ event }) => {
        if (event.request.destination === 'document') {
            return caches.match(OFFLINE_URL);
        }
        return Response.error();
    });

}