const CACHE_NAME = 'vitrina-pwa-v2';
const PRECACHE = [
  './offline.html',
  './assets/img/icon-192.png',
  './assets/img/icon-512.png',
  './assets/img/apple-touch-icon.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      await Promise.all(PRECACHE.map((url) => cache.add(url).catch(() => {})));
      await self.skipWaiting();
    })
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
    )).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('./offline.html'))
    );
    return;
  }

  const isAsset = url.pathname.includes('/assets/') || url.pathname.endsWith('.webmanifest');
  if (!isAsset) return;

  event.respondWith(
    caches.open(CACHE_NAME).then(async (cache) => {
      const cached = await cache.match(request);
      const network = fetch(request).then((response) => {
        if (response && response.ok) cache.put(request, response.clone());
        return response;
      }).catch(() => cached);
      return cached || network;
    })
  );
});
