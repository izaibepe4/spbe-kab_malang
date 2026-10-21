{{-- Global Loading Overlay --}}
<div id="loading-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 shadow-xl flex flex-col items-center">
        {{-- Spinner --}}
        <div class="inline-block w-12 h-12 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div>
        
        {{-- Message --}}
        <p class="loading-message mt-4 text-gray-700 font-medium">Loading...</p>
    </div>
</div>

<style>
#loading-overlay {
    transition: opacity 0.3s ease-in-out;
}

#loading-overlay.hidden {
    opacity: 0;
    pointer-events: none;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
