{{-- Floating Action Button dengan Dropdown ke Atas --}}
<div x-data="{ 
    open: false, 
    a11yOpen: false,
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
    if (highContrast) {
        document.documentElement.classList.add('a11y-high-contrast');
    }
    if (largeCursor) document.body.classList.add('a11y-large-cursor');
    if (lineSpacing) document.body.classList.add('a11y-line-spacing');
    
    // Initialize speech synthesis - check with better detection
    if (typeof window !== 'undefined' && 'speechSynthesis' in window && window.speechSynthesis) {
        speechSynthesis = window.speechSynthesis;
        // Check if voices are available
        if (speechSynthesis.getVoices().length === 0) {
            speechSynthesis.addEventListener('voiceschanged', function() {
                if (speechSynthesis.getVoices().length > 0) {
                    speechSynthesis = window.speechSynthesis;
                }
            });
        }
    }
"
    class="fixed bottom-6 right-6 z-50 a11y-floating-button"
    style="position: fixed !important; bottom: 1.5rem !important; right: 1.5rem !important; z-index: 99999 !important;">
    {{-- Menu Dropdown (muncul ke atas) --}}
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2" class="absolute bottom-16 right-0 mb-2 flex flex-col gap-3">

        {{-- Disabilitas Button --}}
        <button @click="a11yOpen = !a11yOpen; open = false"
            class="flex items-center gap-3 bg-white text-[#102A66] font-semibold text-[14px] px-4 py-3 rounded-full shadow-lg hover:bg-[#1C2B78] hover:text-white transition-all duration-200 min-w-[160px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span>Disabilitas</span>
        </button>

        {{-- Helpdesk Button --}}
        <a href="https://helpdesk-kominfo.malangkab.go.id/" target="_blank" rel="noopener noreferrer" @click="open = false"
            class="flex items-center gap-3 bg-white text-[#102A66] font-semibold text-[14px] px-4 py-3 rounded-full shadow-lg hover:bg-[#FFA84A] hover:text-white transition-all duration-200 min-w-[160px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            <span>Helpdesk</span>
        </a>

        {{-- WhatsApp Button --}}
        <a href="https://wa.me/6281252577736" target="_blank" rel="noopener noreferrer" @click="open = false"
            class="flex items-center gap-3 bg-white text-[#102A66] font-semibold text-[14px] px-4 py-3 rounded-full shadow-lg hover:bg-[#25D366] hover:text-white transition-all duration-200 min-w-[160px]">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z">
                </path>
            </svg>
            <span>WhatsApp</span>
        </a>
    </div>

    {{-- Submenu Aksesibilitas --}}
    <div x-cloak
        x-show="a11yOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-2"
        class="absolute bottom-0 right-[180px] mb-2 bg-white rounded-lg shadow-xl p-4 min-w-[280px] z-50"
        @click.away="a11yOpen = false">

        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-[#102A66]">Aksesibilitas</h3>
            <button @click="a11yOpen = false" class="text-gray-400 hover:text-gray-600" aria-label="Tutup menu">
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
                        if (voiceEnabled) {
                            // Stop speech immediately
                            if (window.speechSynthesis) {
                                window.speechSynthesis.cancel();
                                window.speechSynthesis.stop();
                            }
                            voiceEnabled = false;
                            return;
                        }
                        
                        if (window.speechSynthesis && typeof SpeechSynthesisUtterance !== 'undefined') {
                            try {
                                // Get main content text only (exclude navigation, buttons, etc)
                                const mainContent = document.querySelector('main') || document.body;
                                const text = mainContent.innerText || mainContent.textContent || '';
                                
                                if (text.trim().length === 0) {
                                    alert('Tidak ada konten untuk dibacakan');
                                    return;
                                }
                                
                                // Cancel any existing speech first
                                if (window.speechSynthesis.speaking) {
                                    window.speechSynthesis.cancel();
                                }
                                
                                const utterance = new SpeechSynthesisUtterance(text.substring(0, 5000)); // Limit to 5000 chars
                                utterance.lang = 'id-ID';
                                utterance.rate = 1;
                                utterance.pitch = 1;
                                
                                utterance.onend = () => { 
                                    voiceEnabled = false;
                                };
                                utterance.onerror = (e) => { 
                                    voiceEnabled = false;
                                    console.error('Speech error:', e);
                                };
                                
                                window.speechSynthesis.speak(utterance);
                                voiceEnabled = true;
                            } catch (e) {
                                console.error('Speech synthesis error:', e);
                                alert('Text-to-Speech tidak dapat digunakan. Pastikan browser mendukung fitur ini.');
                                voiceEnabled = false;
                            }
                        } else {
                            alert('Text-to-Speech tidak didukung di browser Anda. Silakan gunakan browser modern seperti Chrome, Edge, atau Safari.');
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
                            document.documentElement.classList.add('a11y-high-contrast');
                        } else {
                            document.documentElement.classList.remove('a11y-high-contrast');
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

            {{-- Reset Button --}}
            <div class="pt-3 mt-3 border-t border-gray-200">
                <button
                    @click="
                        // Reset all settings
                        fontSize = 'normal';
                        highContrast = false;
                        largeCursor = false;
                        lineSpacing = false;
                        voiceEnabled = false;
                        
                        // Stop speech if playing
                        if (window.speechSynthesis) {
                            window.speechSynthesis.cancel();
                        }
                        
                        // Remove all classes
                        document.body.classList.remove('a11y-font-small', 'a11y-font-large', 'a11y-large-cursor', 'a11y-line-spacing');
                        document.documentElement.classList.remove('a11y-high-contrast');
                        
                        // Clear localStorage
                        localStorage.removeItem('a11y-fontSize');
                        localStorage.removeItem('a11y-highContrast');
                        localStorage.removeItem('a11y-largeCursor');
                        localStorage.removeItem('a11y-lineSpacing');
                    "
                    class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white font-medium text-sm px-4 py-2.5 rounded-lg transition-colors opacity-80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Reset Semua</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Floating Button --}}
    <button @click="open = !open"
        class="w-14 h-14 bg-[#FFA84A] hover:bg-[#FF9A1F] text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[#FFA84A] focus:ring-offset-2">
        {{-- Plus Icon yang berotasi 45 derajat jadi X --}}
        <svg class="w-6 h-6 transition-transform duration-300 ease-in-out"
            :class="open ? 'rotate-45' : 'rotate-0'"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
        </svg>
    </button>
</div>