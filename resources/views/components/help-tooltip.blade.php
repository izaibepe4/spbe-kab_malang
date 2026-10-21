{{-- Help Tooltip Component untuk Heuristic 10: Help and Documentation --}}
@props(['text', 'position' => 'top'])

<div class="relative inline-block group">
    <button type="button" class="text-gray-400 hover:text-[#FFA84A] transition-colors focus:outline-none" aria-label="Bantuan">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </button>
    <div class="absolute z-50 hidden group-hover:block {{ $position === 'top' ? 'bottom-full mb-2' : 'top-full mt-2' }} left-1/2 transform -translate-x-1/2">
        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 max-w-xs shadow-lg">
            {{ $text }}
            <div class="absolute {{ $position === 'top' ? 'top-full' : 'bottom-full' }} left-1/2 transform -translate-x-1/2 -mt-1">
                <div class="border-4 border-transparent {{ $position === 'top' ? 'border-t-gray-900' : 'border-b-gray-900' }}"></div>
            </div>
        </div>
    </div>
</div>




