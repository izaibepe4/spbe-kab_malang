const CACHE_NAME = "spbe-v1";
const urlsToCache = [
    "/",
    "/berita-kab-malang",
    "/informasi-spbe",
    "/assets/img/logo-pemkab.png",
];

// Install event - Cache resources saat pertama kali install
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("Opened cache");
                return cache.addAll(urlsToCache);
            })
            .catch((err) => {
                console.error("Cache install failed:", err);
            })
    );
    self.skipWaiting();
});

// Activate event - Clean up old caches
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log("Deleting old cache:", cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch event - Network First Strategy untuk better performance
self.addEventListener("fetch", (event) => {
    // Skip non-GET requests
    if (event.request.method !== "GET") {
        return;
    }

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip API requests and dynamic content
    if (
        event.request.url.includes("/api/") ||
        event.request.url.includes("?") ||
        event.request.url.includes("/admin/")
    ) {
        return; // Let browser handle normally
    }

    // Network First Strategy - faster for most requests
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Only cache successful responses
                if (
                    response &&
                    response.status === 200 &&
                    response.type === "basic"
                ) {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                // If network fails, try cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // If no cache and it's a document request, return offline page
                    if (event.request.destination === "document") {
                        return caches.match("/");
                    }
                });
            })
    );
});
