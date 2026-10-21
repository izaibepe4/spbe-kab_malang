@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

  /* Header - Semua elemen menggunakan Poppins */
  header,
  header *,
  header a,
  header button,
  header nav,
  header span,
  header div,
  header p,
  header h1,
  header h2,
  header h3,
  header h4,
  header h5,
  header h6 {
    font-family: 'Poppins', sans-serif;
  }

  /* Font weight sesuai kebutuhan */
  header {
    font-weight: 400;
    /* Default regular */
  }

  header .font-semibold,
  header nav a,
  header button,
  header [class*="font-semibold"],
  header [class*="semibold"] {
    font-weight: 600;
    /* Semibold untuk menu dan button */
  }

  header .font-bold,
  header [class*="font-bold"],
  header [class*="bold"],
  header h1,
  header h2,
  header h3 {
    font-weight: 700;
    /* Bold untuk heading */
  }
</style>
@endpush


<header x-data="{
    searchOpen: false,
    compact: false,
    init() {
      const updateState = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        this.compact = scrollTop > 10;
      };
      updateState();
      window.addEventListener('scroll', updateState, { passive: true });
    }
  }"
  @close-search.window="searchOpen = false"
  class="fixed top-0 left-0 right-0 z-50 bg-white transition-all duration-300"
  :class="compact ? 'h-[80px] shadow-md' : 'h-[110px] shadow-sm'"
  role="banner">

  {{-- kontainer tengah fixed 1200px --}}
  <div class="mx-auto w-[1200px] max-w-full h-full flex items-center px-4 gap-6 justify-between transition-all duration-300"
    :class="compact ? 'gap-4' : 'gap-6'">
    {{-- BRAND --}}
    <a href="{{ route('public.home') }}" class="flex items-center gap-3 transition-all duration-200">
      <img src="{{ asset('assets/img/logo-pemkab.png') }}" alt="Logo Pemkab Malang"
        class="h-10 w-auto object-contain">
      <div class="leading-tight">
        <div class="text-[14px] text-[#FFA84A] font-semibold">Sistem Pemerintahan Berbasis Elektronik</div>
        <div class="text-[18px] font-semibold text-[#102A66]">Kabupaten Malang</div>
      </div>
    </a>

    {{-- MENU --}}
    <nav class="flex-1 flex items-center justify-center gap-8 text-[14px] font-semibold text-[#102A66]">
      <a href="{{ route('public.home') }}"
        class="pb-1 border-b-2 transition-all duration-200 {{ request()->routeIs('public.home') ? 'border-[#FFA84A] text-[#FFA84A]' : 'border-transparent hover:text-[#FFA84A] hover:border-[#FFA84A]' }}">Beranda</a>
      <a href="{{ route('public.pengetahuan.index') }}"
        class="pb-1 border-b-2 transition-all duration-200 {{ request()->routeIs('public.pengetahuan.*') ? 'border-[#FFA84A] text-[#FFA84A]' : 'border-transparent hover:text-[#FFA84A] hover:border-[#FFA84A]' }}">Pengetahuan
        SPBE</a>

      {{-- Dropdown Berita --}}
      <div class="relative" x-data="{ openDrop:false }" @mouseenter="openDrop=true" @mouseleave="openDrop=false">
        <button type="button"
          class="pb-1 border-b-2 transition-all duration-200 relative"
          :class="openDrop ? 'border-[#FFA84A] text-[#FFA84A]' : 'border-transparent text-[#102A66]'">
          <span>Berita</span>
        </button>
        <div x-cloak x-show="openDrop" x-transition.origin.top
          class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-64 bg-white rounded-xl shadow z-40 overflow-hidden">
          {{-- isi dropdown --}}
          <ul class="py-1 text-[14px] font-semibold text-[#102A66]">
            <li><a href="{{ route('informasi-spbe.index') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Berita SPBE Kab Malang</a></li>
            <li><a href="{{ route('berita-kab-malang.index') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Berita Kab Malang</a></li>
          </ul>
        </div>
      </div>

      {{-- Dropdown SPBE (tetap) --}}
      <div class="relative" x-data="{ openDrop:false }" @mouseenter="openDrop=true" @mouseleave="openDrop=false">
        <button type="button"
          class="pb-1 border-b-2 transition-all duration-200 relative"
          :class="openDrop ? 'border-[#FFA84A] text-[#FFA84A]' : 'border-transparent text-[#102A66]'">
          <span>SPBE</span>
        </button>
        <div x-cloak x-show="openDrop" x-transition.origin.top
          class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-64 bg-white rounded-xl shadow z-40 overflow-hidden">
          {{-- isi dropdown tetap --}}
          <ul class="py-1 text-[14px] font-semibold text-[#102A66]">
            <li><a href="{{ route('public.regulasi.index') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Regulasi</a></li>
            <li><a href="{{ route('public.sop.index') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">SOP</a></li>
            <li><a href="{{ route('public.arsitektur_spbe') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Arsitektur SPBE</a></li>
            <li><a href="{{ route('public.selengkapnya.paparan') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Paparan</a></li>
            <li><a href="{{ route('public.selengkapnya.agenda') }}"
                class="block px-5 py-3 hover:bg-[#102A66] hover:text-white">Agenda</a></li>
          </ul>
        </div>
      </div>
    </nav>

    {{-- Pencarian & Visitor Counter --}}
    <div class="flex items-center gap-3">
      {{-- Pencarian --}}
      <button
        data-search-trigger
        @click="searchOpen = true; $nextTick(() => { const input = document.querySelector('[x-ref=\'searchInput\']'); if(input) input.focus(); })"
        class="flex items-center gap-2 bg-[#FFA84A] hover:bg-[#FF9A1F] text-[#102A66] font-semibold text-[14px] px-4 py-2 rounded-full transition-all duration-300">
        <img src='{{ asset("assets/icons/search.png") }}' class="w-5 h-5" alt="Cari">
        <span>PENCARIAN</span>
      </button>

      {{-- Visitor Counter --}}
      @php
      $visitorService = app(\App\Services\VisitorCounterService::class);
      $visitorCount = $visitorService->getTotalVisitors();
      @endphp
      <div class="flex items-center gap-2 text-[#102A66] ml-4" title="Jumlah Pengunjung">
        <svg class="w-5 h-5 text-[#FFA84A]" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
        </svg>
        <span class="font-semibold text-[14px]">{{ number_format($visitorCount) }}</span>
      </div>
    </div>
  </div>

  {{-- Search Modal Full Screen --}}
  <div
    x-show="searchOpen"
    x-cloak
    @keydown.escape.window="$dispatch('close-search')"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] bg-white"
    style="display: none;"
    x-data="{
      searchQuery: '',
      searchResults: [],
      isLoading: false,
      searchTimeout: null,
      totalResults: 0,
      async performSearch() {
        if (!this.searchQuery.trim()) {
          this.searchResults = [];
          this.totalResults = 0;
          return;
        }
        
        this.isLoading = true;
        
        try {
          const response = await fetch('{{ route('api.search') }}?q=' + encodeURIComponent(this.searchQuery.trim()) + '&limit=10');
          const data = await response.json();
          
          if (data.success) {
            this.searchResults = data.results || [];
            this.totalResults = data.total || 0;
          } else {
            this.searchResults = [];
            this.totalResults = 0;
          }
        } catch (error) {
          console.error('Search error:', error);
          this.searchResults = [];
          this.totalResults = 0;
        } finally {
          this.isLoading = false;
        }
      },
      handleInput() {
        // Clear existing timeout
        if (this.searchTimeout) {
          clearTimeout(this.searchTimeout);
        }
        
        // Set new timeout for debounce (500ms)
        this.searchTimeout = setTimeout(() => {
          this.performSearch();
        }, 500);
      },
      goToSearchPage() {
        if (this.searchQuery.trim()) {
          window.location.href = '{{ route('public.search') }}?q=' + encodeURIComponent(this.searchQuery.trim());
        }
      },
      resetSearch() {
        this.searchQuery = '';
        this.searchResults = [];
        this.totalResults = 0;
        if (this.searchTimeout) {
          clearTimeout(this.searchTimeout);
          this.searchTimeout = null;
        }
      }
    }"
    @close-search.window="resetSearch()">

    {{-- Close Button --}}
    <button
      @click="$dispatch('close-search')"
      class="absolute top-6 right-6 text-[#FFA84A] hover:text-[#FF9A1F] transition-colors z-10">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>

    {{-- Search Input Container --}}
    <div class="flex flex-col items-center justify-start min-h-screen px-4 pt-20 pb-8">
      <div class="w-full max-w-4xl">
        <form
          @submit.prevent="goToSearchPage()"
          class="relative mb-8">
          <div class="flex items-center justify-center relative">
            <input
              x-ref="searchInput"
              type="text"
              x-model="searchQuery"
              @input="handleInput()"
              placeholder="KETIK DISINI..."
              class="w-full text-4xl md:text-6xl font-bold text-left border-none outline-none bg-transparent text-[#102A66] placeholder-[#102A66] placeholder-opacity-50 focus:placeholder-opacity-30 transition-all"
              autocomplete="off"
              @keydown.enter.prevent="goToSearchPage()">

            {{-- Loading Indicator --}}
            <div x-show="isLoading" class="absolute right-0 top-1/2 -translate-y-1/2">
              <svg class="animate-spin h-8 w-8 text-[#FFA84A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>
        </form>

        {{-- Search Results --}}
        <div x-show="searchQuery.trim() && (searchResults.length > 0 || isLoading)" class="mt-8 max-h-[60vh] overflow-y-auto">
          {{-- Results Count --}}
          <div x-show="!isLoading && totalResults > 0" class="mb-4 text-sm text-gray-600">
            Ditemukan <span x-text="totalResults" class="font-semibold"></span> hasil
            <span x-show="searchResults.length < totalResults">
              (menampilkan <span x-text="searchResults.length"></span> hasil pertama)
            </span>
          </div>

          {{-- Results List --}}
          <div x-show="!isLoading && searchResults.length > 0" class="space-y-3">
            <template x-for="result in searchResults" :key="result.id">
              <a
                :href="result.route"
                class="block bg-white rounded-lg border-2 border-[rgba(16,42,102,0.12)] shadow-[0_18px_40px_-20px_rgba(16,42,102,0.35)] hover:shadow-[0_28px_60px_-20px_rgba(16,42,102,0.45)] transition-all duration-300 hover:-translate-y-1.5 overflow-hidden group">
                <div class="flex gap-0">
                  {{-- Image --}}
                  <div class="flex-shrink-0 w-32 h-32 overflow-hidden bg-gray-200 relative">
                    <img
                      x-show="result.image_url"
                      :src="result.image_url"
                      :alt="result.title"
                      class="w-full h-full object-cover">
                    <div x-show="!result.image_url" class="w-full h-full flex items-center justify-center text-gray-400 text-xs bg-gray-100">
                      No Image
                    </div>
                    {{-- Category Tag --}}
                    <div x-show="result.category" class="absolute top-2 left-2">
                      <span class="inline-flex items-center justify-center bg-[#102A66] text-white px-3 py-1 text-xs font-semibold rounded-tl-lg rounded-br-lg shadow-lg group-hover:bg-[#FFA84A] group-hover:text-[#102A66] transition-all duration-250" x-text="result.category"></span>
                    </div>
                  </div>

                  {{-- Content --}}
                  <div class="flex-1 min-w-0 p-4 flex flex-col justify-between">
                    {{-- Title --}}
                    <h3 class="text-sm font-bold text-[#102A66] group-hover:text-[#FFA84A] transition-colors mb-2 line-clamp-2" x-text="result.title"></h3>

                    {{-- Meta --}}
                    <div class="flex items-center gap-2 text-xs text-[#5f6b94] mt-auto">
                      <span x-show="result.date_formatted" class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-[#FFA84A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-text="result.date_formatted"></span>
                      </span>
                    </div>
                  </div>
                </div>
              </a>
            </template>
          </div>

          {{-- No Results --}}
          <div x-show="!isLoading && searchResults.length === 0 && searchQuery.trim()" class="text-center py-12">
            <p class="text-gray-500 text-lg">Tidak ada hasil ditemukan untuk "<span x-text="searchQuery" class="font-semibold"></span>"</p>
            <p class="text-gray-400 text-sm mt-2">Coba gunakan kata kunci lain</p>
          </div>

          {{-- View All Results Button --}}
          <div x-show="!isLoading && totalResults > 0" class="mt-6 text-center">
            <button
              @click="goToSearchPage()"
              class="inline-flex items-center gap-2 px-6 py-3 bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold rounded-lg transition-colors">
              Lihat Semua Hasil
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</header>