<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" style="margin-bottom: 1rem;"></div>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</x-dynamic-component>
