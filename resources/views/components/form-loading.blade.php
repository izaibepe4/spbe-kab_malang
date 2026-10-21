{{-- Form Loading Overlay untuk Heuristic 1: Visibility of System Status --}}
<div x-data="{ loading: false }"
    x-init="
        // Listen for form submission
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM' && !form.hasAttribute('data-no-loading')) {
                loading = true;
            }
        });
        
        // Listen for form success/error
        window.addEventListener('form-submitted', () => {
            loading = false;
        });
    "
    x-show="loading"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center"
    style="display: none;">
    <div class="bg-white rounded-lg p-6 flex flex-col items-center gap-4 min-w-[200px]">
        <svg class="animate-spin h-8 w-8 text-[#FFA84A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm font-medium text-gray-700">Memproses...</p>
    </div>
</div>




