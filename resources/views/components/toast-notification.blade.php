{{-- Toast Notification Component untuk Heuristic 1: Visibility of System Status --}}
<div x-data="{
    show: false,
    message: '',
    type: 'success', // success, error, warning, info
    timeout: null,
    showToast(msg, toastType = 'success', duration = 5000) {
        this.message = msg;
        this.type = toastType;
        this.show = true;
        
        // Clear existing timeout
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
        
        // Auto hide after duration
        this.timeout = setTimeout(() => {
            this.show = false;
        }, duration);
    }
}"
x-init="
    // Listen for toast events
    window.addEventListener('toast', (e) => {
        showToast(e.detail.message, e.detail.type || 'success', e.detail.duration || 5000);
    });
"
@toast.window="showToast($event.detail.message, $event.detail.type || 'success', $event.detail.duration || 5000)"
class="fixed top-4 right-4 z-[10000] transition-all duration-300"
:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-2 pointer-events-none'"
x-show="show"
x-cloak>
    <div class="bg-white rounded-lg shadow-xl border-l-4 min-w-[300px] max-w-md p-4 flex items-start gap-3"
        :class="{
            'border-green-500': type === 'success',
            'border-red-500': type === 'error',
            'border-yellow-500': type === 'warning',
            'border-blue-500': type === 'info'
        }">
        {{-- Icon --}}
        <div class="flex-shrink-0 mt-0.5">
            <svg x-show="type === 'success'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="type === 'error'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="type === 'warning'" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <svg x-show="type === 'info'" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        {{-- Message --}}
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900" x-text="message"></p>
        </div>
        
        {{-- Close Button --}}
        <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>




