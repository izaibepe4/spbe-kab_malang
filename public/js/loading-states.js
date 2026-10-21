/**
 * Loading States Handler
 * Automatically show loading states for AJAX requests and page transitions
 */

(function() {
    'use strict';

    // Show loading overlay
    function showLoading(message = 'Loading...') {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.querySelector('.loading-message').textContent = message;
            overlay.classList.remove('hidden');
        }
    }

    // Hide loading overlay
    function hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    // Show loading on form submit
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.dataset.noLoading) return;
        
        showLoading('Processing...');
    });

    // Show loading on AJAX requests
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            showLoading();
            return originalFetch.apply(this, args)
                .finally(() => hideLoading());
        };
    }

    // Show loading on page navigation
    window.addEventListener('beforeunload', function() {
        showLoading('Loading page...');
    });

    // Hide loading when page loads
    window.addEventListener('load', function() {
        hideLoading();
    });

    // Expose functions globally
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
})();
