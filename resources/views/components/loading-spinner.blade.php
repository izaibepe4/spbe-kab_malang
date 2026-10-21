<div {{ $attributes->merge(['class' => 'loading-spinner']) }}>
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    @if(isset($message))
        <p class="mt-2 text-muted">{{ $message }}</p>
    @endif
</div>

<style>
.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3rem;
}
</style>
