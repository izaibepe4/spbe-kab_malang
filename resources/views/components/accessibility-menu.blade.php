{{-- Submenu Aksesibilitas --}}
<div x-data="{ 
    fontSize: localStorage.getItem('a11y-fontSize') || 'normal',
    highContrast: localStorage.getItem('a11y-highContrast') === 'true',
    largeCursor: localStorage.getItem('a11y-largeCursor') === 'true',
    lineSpacing: localStorage.getItem('a11y-lineSpacing') === 'true',
    voiceEnabled: false,
    speechSynthesis: null
}" 
x-init="
    // Load saved preferences
    if (fontSize === 'small') document.body.classList.add('a11y-font-small');
    if (fontSize === 'large') document.body.classList.add('a11y-font-large');
    if (highContrast) document.body.classList.add('a11y-high-contrast');
    if (largeCursor) document.body.classList.add('a11y-large-cursor');
    if (lineSpacing) document.body.classList.add('a11y-line-spacing');
    
    // Initialize speech synthesis
    if ('speechSynthesis' in window) {
        speechSynthesis = window.speechSynthesis;
    }
"
x-cloak 
x-show="$parent.a11yOpen"
x-transition:enter="transition ease-out duration-200"
x-transition:enter-start="opacity-0 translate-x-2" 
x-transition:enter-end="opacity-100 translate-x-0"
x-transition:leave="transition ease-in duration-150" 
x-transition:leave-start="opacity-100 translate-x-0"
x-transition:leave-end="opacity-0 translate-x-2"
class="absolute bottom-0 right-[180px] mb-2 bg-white rounded-lg shadow-xl p-4 min-w-[280px] z-50"
@click.away="$parent.a11yOpen = false">
    
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-[#102A66]">Aksesibilitas</h3>
        <button @click="$parent.a11yOpen = false" class="text-gray-400 hover:text-gray-600" aria-label="Tutup menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="space-y-3">
        {{-- Voice / Text-to-Speech --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#102A66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg>
                <span class="text-sm font-medium text-[#102A66]">Text-to-Speech</span>
            </div>
            <button 
                @click="
                    if (voiceEnabled && speechSynthesis) {
                        speechSynthesis.cancel();
                        voiceEnabled = false;
                    } else if (speechSynthesis) {
                        const utterance = new SpeechSynthesisUtterance(document.body.innerText);
                        utterance.lang = 'id-ID';
                        utterance.rate = 1;
                        utterance.pitch = 1;
                        speechSynthesis.speak(utterance);
                        voiceEnabled = true;
                        utterance.onend = () => { voiceEnabled = false; };
                    } else {
                        alert('Text-to-Speech tidak didukung di browser Anda');
                    }
                "
                :class="voiceEnabled ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                <span x-text="voiceEnabled ? 'Stop' : 'Mulai'"></span>
            </button>
        </div>

        {{-- Ukuran Font --}}
        <div>
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-[#102A66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-sm font-medium text-[#102A66]">Ukuran Font</span>
            </div>
            <div class="flex items-center gap-2 ml-8">
                <button 
                    @click="
                        fontSize = 'small';
                        document.body.classList.remove('a11y-font-large');
                        document.body.classList.add('a11y-font-small');
                        localStorage.setItem('a11y-fontSize', 'small');
                    "
                    :class="fontSize === 'small' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-1.5 rounded text-xs font-medium transition-colors">A-</button>
                <button 
                    @click="
                        fontSize = 'normal';
                        document.body.classList.remove('a11y-font-small', 'a11y-font-large');
                        localStorage.setItem('a11y-fontSize', 'normal');
                    "
                    :class="fontSize === 'normal' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-1.5 rounded text-xs font-medium transition-colors">A</button>
                <button 
                    @click="
                        fontSize = 'large';
                        document.body.classList.remove('a11y-font-small');
                        document.body.classList.add('a11y-font-large');
                        localStorage.setItem('a11y-fontSize', 'large');
                    "
                    :class="fontSize === 'large' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-1.5 rounded text-xs font-medium transition-colors">A+</button>
            </div>
        </div>

        {{-- Warna Hitam Putih (High Contrast) --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#102A66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                <span class="text-sm font-medium text-[#102A66]">Kontras Tinggi</span>
            </div>
            <button 
                @click="
                    highContrast = !highContrast;
                    if (highContrast) {
                        document.body.classList.add('a11y-high-contrast');
                    } else {
                        document.body.classList.remove('a11y-high-contrast');
                    }
                    localStorage.setItem('a11y-highContrast', highContrast);
                "
                :class="highContrast ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                <span x-text="highContrast ? 'ON' : 'OFF'"></span>
            </button>
        </div>

        {{-- Kursor Besar --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#102A66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                </svg>
                <span class="text-sm font-medium text-[#102A66]">Kursor Besar</span>
            </div>
            <button 
                @click="
                    largeCursor = !largeCursor;
                    if (largeCursor) {
                        document.body.classList.add('a11y-large-cursor');
                    } else {
                        document.body.classList.remove('a11y-large-cursor');
                    }
                    localStorage.setItem('a11y-largeCursor', largeCursor);
                "
                :class="largeCursor ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                <span x-text="largeCursor ? 'ON' : 'OFF'"></span>
            </button>
        </div>

        {{-- Line Spacing --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#102A66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span class="text-sm font-medium text-[#102A66]">Spasi Baris</span>
            </div>
            <button 
                @click="
                    lineSpacing = !lineSpacing;
                    if (lineSpacing) {
                        document.body.classList.add('a11y-line-spacing');
                    } else {
                        document.body.classList.remove('a11y-line-spacing');
                    }
                    localStorage.setItem('a11y-lineSpacing', lineSpacing);
                "
                :class="lineSpacing ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                <span x-text="lineSpacing ? 'ON' : 'OFF'"></span>
            </button>
        </div>
    </div>
</div>

