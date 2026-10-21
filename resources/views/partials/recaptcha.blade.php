@if(isset($recaptchaEnabled) && $recaptchaEnabled && isset($recaptchaSiteKey) && !empty($recaptchaSiteKey))
    @php
        $recaptchaVersion = config('recaptcha.version', 'v2'); // Default to v2 for checkbox
        $action = $action ?? 'submit';
    @endphp

    @if($recaptchaVersion === 'v3')
        {{-- reCAPTCHA v3 - Script should be loaded in head, this is just the handler --}}
        {{-- Visual indicator that reCAPTCHA is active (v3 is invisible) --}}
        <div class="mb-4 flex items-center justify-center text-xs text-gray-500">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Dilindungi oleh reCAPTCHA</span>
        </div>
        <script>
            (function() {
                const siteKey = '{{ $recaptchaSiteKey }}';
                const action = '{{ $action }}';
                const RECAPTCHA_TIMEOUT = {{ (config('recaptcha.frontend_timeout', 10) * 1000) }};
                
                function setupFormHandler() {
                    const targetForm = document.querySelector('form');
                    if (!targetForm) return;
                    
                    let isSubmitting = false;
                    
                    targetForm.addEventListener('submit', function(e) {
                        const existingToken = targetForm.querySelector('input[name="g-recaptcha-response"]');
                        if (existingToken && existingToken.value) {
                            return;
                        }
                        
                        if (isSubmitting) {
                            e.preventDefault();
                            return;
                        }
                        
                        e.preventDefault();
                        isSubmitting = true;
                        
                        // Show loading
                        if (typeof Alpine !== 'undefined' && targetForm._x_dataStack) {
                            const formData = targetForm._x_dataStack[0];
                            if (formData && typeof formData.submitting !== 'undefined') {
                                formData.submitting = true;
                            }
                        }
                        
                        // Wait and execute
                        const executeRecaptcha = new Promise((resolve, reject) => {
                            let attempts = 0;
                            const maxAttempts = 100;
                            
                            const checkAndExecute = () => {
                                attempts++;
                                
                                if (typeof window.grecaptcha !== 'undefined' && window.grecaptcha.ready) {
                                    window.grecaptcha.ready(() => {
                                        window.grecaptcha.execute(siteKey, { action: action })
                                            .then(resolve)
                                            .catch(reject);
                                    });
                                } else if (attempts >= maxAttempts) {
                                    reject(new Error('reCAPTCHA tidak dapat dimuat. Periksa koneksi internet Anda.'));
                                } else {
                                    setTimeout(checkAndExecute, 100);
                                }
                            };
                            
                            checkAndExecute();
                        });
                        
                        const timeout = new Promise((_, reject) => {
                            setTimeout(() => reject(new Error('Timeout')), RECAPTCHA_TIMEOUT);
                        });
                        
                        Promise.race([executeRecaptcha, timeout])
                            .then(function(token) {
                                if (!token) {
                                    throw new Error('Token reCAPTCHA kosong');
                                }
                                
                                const oldToken = targetForm.querySelector('input[name="g-recaptcha-response"]');
                                if (oldToken) oldToken.remove();
                                
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'g-recaptcha-response';
                                input.value = token;
                                targetForm.appendChild(input);
                                
                                isSubmitting = false;
                                targetForm.submit();
                            })
                            .catch(function(error) {
                                console.error('reCAPTCHA error:', error);
                                isSubmitting = false;
                                
                                if (typeof Alpine !== 'undefined' && targetForm._x_dataStack) {
                                    const formData = targetForm._x_dataStack[0];
                                    if (formData && typeof formData.submitting !== 'undefined') {
                                        formData.submitting = false;
                                    }
                                }
                                
                                const errorMsg = error.message || 'Silakan refresh halaman dan coba lagi.';
                                alert('Error reCAPTCHA: ' + errorMsg + '\n\nJika masalah berlanjut, pastikan:\n- Koneksi internet aktif\n- Tidak ada firewall yang memblokir Google\n- Browser mendukung JavaScript');
                            });
                    }, true);
                }
                
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupFormHandler);
                } else {
                    setupFormHandler();
                }
            })();
        </script>
    @else
        {{-- reCAPTCHA v2 Checkbox --}}
        <div class="mb-4">
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}" data-theme="light"></div>
            @error('g-recaptcha-response')
                <p class="mt-1 text-sm text-red-600 flex items-center" role="alert">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
        {{-- Script v2 is loaded in head, no need to load here to avoid duplication --}}
    @endif
@endif

