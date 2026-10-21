{{-- Loading State Component --}}
@props(['type' => 'spinner', 'size' => 'md', 'text' => 'Loading...'])

@php
$sizeClasses = [
    'sm' => 'w-4 h-4',
    'md' => 'w-8 h-8',
    'lg' => 'w-12 h-12',
];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    @if($type === 'spinner')
        {{-- Spinner Loading --}}
        <div class="inline-block {{ $sizeClass }} animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status">
            <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">{{ $text }}</span>
        </div>
    @elseif($type === 'dots')
        {{-- Dots Loading --}}
        <div class="flex space-x-2">
            <div class="w-2 h-2 bg-current rounded-full animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-2 h-2 bg-current rounded-full animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-2 h-2 bg-current rounded-full animate-bounce" style="animation-delay: 300ms"></div>
        </div>
    @elseif($type === 'skeleton')
        {{-- Skeleton Loading --}}
        <div class="animate-pulse space-y-4 w-full">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
    @endif
    
    @if($text && $type !== 'skeleton')
        <span class="ml-2 text-sm text-gray-600">{{ $text }}</span>
    @endif
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-0.5rem); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.animate-bounce {
    animation: bounce 1s infinite;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>
