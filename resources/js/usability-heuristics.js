/**
 * Usability Heuristics Implementation
 * Menerapkan 10 Usability Heuristics Nielsen
 */

// Heuristic 1: Visibility of System Status
export function showLoadingState(element, message = 'Memproses...') {
    if (element) {
        element.disabled = true;
        const originalText = element.innerHTML;
        element.innerHTML = `
            <svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            ${message}
        `;
        return () => {
            element.disabled = false;
            element.innerHTML = originalText;
        };
    }
}

// Heuristic 1: Toast Notification
export function showToast(message, type = 'success', duration = 5000) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type, duration }
    }));
}

// Heuristic 3 & 5: Confirmation Dialog
export function showConfirmation(title, message, onConfirm, confirmText = 'Ya', cancelText = 'Batal') {
    window.dispatchEvent(new CustomEvent('confirm-dialog', {
        detail: { title, message, onConfirm, confirmText, cancelText }
    }));
}

// Heuristic 7: Keyboard Shortcuts
export function initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + K untuk search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchBtn = document.querySelector('[data-search-trigger]');
            if (searchBtn) {
                searchBtn.click();
            }
        }
        
        // Escape untuk close modals
        if (e.key === 'Escape') {
            // Close any open modals or dropdowns
            const modals = document.querySelectorAll('[x-show]');
            modals.forEach(modal => {
                if (modal.style.display !== 'none') {
                    modal.dispatchEvent(new Event('close'));
                }
            });
        }
    });
}

// Heuristic 6: Recent Items (localStorage)
export function addToRecentItems(type, item) {
    const key = `recent_${type}`;
    let recent = JSON.parse(localStorage.getItem(key) || '[]');
    
    // Remove if already exists
    recent = recent.filter(i => i.id !== item.id);
    
    // Add to beginning
    recent.unshift({
        id: item.id,
        title: item.title,
        url: item.url,
        timestamp: Date.now()
    });
    
    // Keep only last 5
    recent = recent.slice(0, 5);
    
    localStorage.setItem(key, JSON.stringify(recent));
}

export function getRecentItems(type) {
    const key = `recent_${type}`;
    return JSON.parse(localStorage.getItem(key) || '[]');
}

// Heuristic 9: Better Error Messages
export function formatErrorMessage(error, field = null) {
    const errorMessages = {
        'required': field ? `${field} wajib diisi` : 'Field ini wajib diisi',
        'email': 'Format email tidak valid',
        'min': field ? `${field} terlalu pendek` : 'Nilai terlalu kecil',
        'max': field ? `${field} terlalu panjang` : 'Nilai terlalu besar',
        'numeric': field ? `${field} harus berupa angka` : 'Harus berupa angka',
        'unique': field ? `${field} sudah digunakan` : 'Data sudah ada',
    };
    
    if (typeof error === 'string') {
        return error;
    }
    
    if (error && error.message) {
        return error.message;
    }
    
    return 'Terjadi kesalahan. Silakan coba lagi.';
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initKeyboardShortcuts();
    
    // Heuristic 1: Show page load status
    if (document.readyState === 'loading') {
        document.body.classList.add('page-loading');
    }
    
    window.addEventListener('load', () => {
        document.body.classList.remove('page-loading');
        document.body.classList.add('page-loaded');
    });
    
    // Heuristic 1: Form submission feedback
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !form.hasAttribute('data-no-loading')) {
                showLoadingState(submitBtn);
            }
        });
    });
    
    // Heuristic 5: Prevent accidental form submission
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const confirmMessage = form.getAttribute('data-confirm');
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    });
});




