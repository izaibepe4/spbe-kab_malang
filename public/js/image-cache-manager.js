/**
 * Image Cache Manager
 * Mengelola cache gambar di client-side menggunakan SessionStorage
 * dan browser cache untuk optimasi performa
 */
class ImageCacheManager {
    constructor() {
        this.cacheKey = 'spbe_image_cache';
        this.cacheTimestampKey = 'spbe_image_cache_timestamp';
        this.cacheDuration = 60 * 60 * 1000; // 1 jam dalam milliseconds
        this.preloadedImages = new Set();
        this.init();
    }

    init() {
        // Clean old cache on page load
        this.cleanOldCache();
        
        // Preload critical images
        this.preloadCriticalImages();
    }

    /**
     * Cek apakah gambar sudah di-cache
     */
    isCached(imageUrl) {
        const cache = this.getCache();
        return cache.has(imageUrl);
    }

    /**
     * Tambahkan gambar ke cache
     */
    addToCache(imageUrl) {
        const cache = this.getCache();
        cache.add(imageUrl);
        this.saveCache(cache);
    }

    /**
     * Ambil semua gambar dari cache
     */
    getCache() {
        try {
            const cached = sessionStorage.getItem(this.cacheKey);
            if (cached) {
                return new Set(JSON.parse(cached));
            }
        } catch (e) {
            console.warn('Error reading image cache:', e);
        }
        return new Set();
    }

    /**
     * Simpan cache ke SessionStorage
     */
    saveCache(cache) {
        try {
            sessionStorage.setItem(this.cacheKey, JSON.stringify(Array.from(cache)));
            sessionStorage.setItem(this.cacheTimestampKey, Date.now().toString());
        } catch (e) {
            console.warn('Error saving image cache:', e);
            // Jika SessionStorage penuh, clear old entries
            this.clearCache();
        }
    }

    /**
     * Preload gambar penting
     */
    preloadCriticalImages() {
        // Preload logo dan assets penting
        const criticalImages = [
            document.querySelector('img[src*="logo-pemkab"]')?.src,
            document.querySelector('img[src*="hero_section"]')?.src,
        ].filter(Boolean);

        criticalImages.forEach(url => {
            if (url && !this.preloadedImages.has(url)) {
                this.preloadImage(url);
            }
        });
    }

    /**
     * Preload single image
     */
    preloadImage(url) {
        if (!url || this.preloadedImages.has(url)) {
            return Promise.resolve();
        }

        this.preloadedImages.add(url);

        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                this.addToCache(url);
                resolve(img);
            };
            img.onerror = reject;
            img.src = url;
        });
    }

    /**
     * Preload multiple images
     */
    async preloadImages(urls) {
        const promises = urls
            .filter(url => url && !this.preloadedImages.has(url))
            .map(url => this.preloadImage(url));
        
        return Promise.allSettled(promises);
    }

    /**
     * Lazy load image dengan intersection observer
     */
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src || img.getAttribute('data-src');
                        
                        if (src) {
                            // Preload image
                            this.preloadImage(src).then(() => {
                                img.src = src;
                                img.removeAttribute('data-src');
                                img.classList.add('loaded');
                            });
                        }
                        
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px' // Start loading 50px before image enters viewport
            });

            // Observe all images with data-src attribute
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            document.querySelectorAll('img[data-src]').forEach(img => {
                const src = img.dataset.src;
                if (src) {
                    this.preloadImage(src).then(() => {
                        img.src = src;
                        img.removeAttribute('data-src');
                    });
                }
            });
        }
    }

    /**
     * Clean old cache entries
     */
    cleanOldCache() {
        try {
            const timestamp = sessionStorage.getItem(this.cacheTimestampKey);
            if (timestamp) {
                const age = Date.now() - parseInt(timestamp);
                if (age > this.cacheDuration) {
                    this.clearCache();
                }
            }
        } catch (e) {
            console.warn('Error cleaning cache:', e);
        }
    }

    /**
     * Clear all cache
     */
    clearCache() {
        try {
            sessionStorage.removeItem(this.cacheKey);
            sessionStorage.removeItem(this.cacheTimestampKey);
            this.preloadedImages.clear();
        } catch (e) {
            console.warn('Error clearing cache:', e);
        }
    }

    /**
     * Get cache statistics
     */
    getStats() {
        const cache = this.getCache();
        return {
            cachedImages: cache.size,
            preloadedImages: this.preloadedImages.size,
            cacheAge: this.getCacheAge()
        };
    }

    /**
     * Get cache age in minutes
     */
    getCacheAge() {
        try {
            const timestamp = sessionStorage.getItem(this.cacheTimestampKey);
            if (timestamp) {
                return Math.floor((Date.now() - parseInt(timestamp)) / 60000);
            }
        } catch (e) {
            return 0;
        }
        return 0;
    }
}

// Initialize global instance
window.imageCacheManager = new ImageCacheManager();

// Setup lazy loading when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.imageCacheManager.setupLazyLoading();
    });
} else {
    window.imageCacheManager.setupLazyLoading();
}

// Re-setup lazy loading after dynamic content loads
const originalPush = Array.prototype.push;
Array.prototype.push = function(...items) {
    const result = originalPush.apply(this, items);
    if (window.imageCacheManager) {
        setTimeout(() => window.imageCacheManager.setupLazyLoading(), 100);
    }
    return result;
};

















