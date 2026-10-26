self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));

// Keep portal pages and authentication network-backed so orders and sessions
// always use current server data. The service worker exists for installability.
self.addEventListener('fetch', () => {});
