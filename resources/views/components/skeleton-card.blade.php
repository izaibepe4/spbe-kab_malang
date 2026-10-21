<div {{ $attributes->merge(['class' => 'skeleton-card']) }}>
    <div class="skeleton skeleton-image"></div>
    <div class="skeleton-content">
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text short"></div>
    </div>
</div>

<style>
.skeleton-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.skeleton-image {
    width: 100%;
    height: 200px;
}

.skeleton-content {
    padding: 1rem;
}

.skeleton-title {
    height: 24px;
    margin-bottom: 12px;
    border-radius: 4px;
}

.skeleton-text {
    height: 16px;
    margin-bottom: 8px;
    border-radius: 4px;
}

.skeleton-text.short {
    width: 60%;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
