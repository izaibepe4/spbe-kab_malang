@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    /* Footer - Semua elemen menggunakan Poppins */
    footer,
    footer *,
    footer a,
    footer span,
    footer div,
    footer p,
    footer ul,
    footer li,
    footer h1,
    footer h2,
    footer h3,
    footer h4,
    footer h5,
    footer h6 {
        font-family: 'Poppins', sans-serif;
    }

    /* Font weight sesuai kebutuhan */
    footer {
        font-weight: 400;
        /* Default regular */
    }

    footer .font-semibold,
    footer [class*="font-semibold"],
    footer [class*="semibold"],
    footer a {
        font-weight: 600;
        /* Semibold untuk judul kolom dan link */
    }

    footer .font-bold,
    footer [class*="font-bold"],
    footer [class*="bold"] {
        font-weight: 700;
        /* Bold jika ada */
    }

    /* Text size kecil tetap regular */
    footer .text-sm,
    footer .text-xs {
        font-weight: 400;
    }
</style>
@endpush

<footer class="w-full bg-[#1C2B78] text-white">
    <div class="mx-auto w-[1520px] px-0">

        {{-- ================= WRAP-1 ================= --}}
        <div class="mx-auto w-[1200px] h-[128px] flex items-center">
            <div class="w-full grid grid-cols-12 items-start">

                {{-- Brand (kiri) --}}
                <div class="col-span-3 flex items-center gap-4">
                    <img src="{{ asset('assets/img/logo-pemkab.png') }}" alt="Logo Pemkab" class="h-10 w-auto shrink-0">
                    <div class="text-2xl font-bold tracking-wide">SPBE</div>
                </div>

                {{-- Alamat --}}
                <div class="col-span-6 flex justify-end text-left">
                    <div class="flex items-start gap-3">
                        {{-- ICON --}}
                        <svg class="h-6 w-6 mt-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 21s-7-5.4-7-11a7 7 0 1 1 14 0c0 5.6-7 11-7 11z" />
                            <circle cx="12" cy="10" r="2.5" stroke-width="1.5" />
                        </svg>

                        {{-- TEXT --}}
                        <div class="leading-tight">
                            <div class="font-semibold mb-1 text-[#FFA84A]">Alamat</div>
                            <div class="text-sm text-white/90 leading-snug">
                                Jl. Agus Salim, Kiduldalem, Kec. Klojen, <br>
                                Kota Malang, Jawa Timur 65143
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Portal --}}
                <div class="col-span-3 flex justify-end text-left">
                    <div class="flex items-start gap-3">
                        {{-- ICON --}}
                        <svg class="h-6 w-6 mt-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>

                        {{-- TEXT --}}
                        <div class="leading-tight">
                            <div class="font-semibold mb-1 text-[#FFA84A]">Portal</div>
                            <div class="text-sm text-white/90">
                                <a href="https://malangkab.go.id/" target="_blank"
                                    class="hover:text-[#FFA84A] transition underline">
                                    Portal Kabupaten Malang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- separator --}}
        <div class="mx-auto w-[1200px] border-t border-white/30"></div>

        {{-- ================= WRAP-2: 1200 × 251 ================= --}}
        <div class="mx-auto w-[1200px] h-[251px] pt-6">
            <div class="grid grid-cols-4 gap-8">

                {{-- Kolom 1 --}}
                <div>
                    <div class="font-semibold mb-3 text-[#FFA84A]">SPBE</div>
                    <p class="text-sm text-white/90">
                        Sistem Pemerintahan Berbasis Elektronik
                    </p>
                </div>

                {{-- Kolom 2 --}}
                <div>
                    <div class="font-semibold mb-3 text-[#FFA84A]">Domain SPBE</div>

                    @php
                    $arrow = '<svg class="h-3 w-3" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M6.25 3.75l4 4-4 4" />
                    </svg>';
                    @endphp

                    <ul class="space-y-2">
                        @forelse($pengetahuanCategories ?? [] as $category)
                        <li>
                            <a href="{{ route('public.pengetahuan.index', ['category' => $category->id]) }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">{{ $category->nama_kategori }}</span>
                            </a>
                        </li>
                        @empty
                        <li class="text-white/70 text-sm italic">Belum ada kategori pengetahuan.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Kolom 3 --}}
                <div>
                    <div class="font-semibold mb-3 text-[#FFA84A]">Tentang SPBE</div>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('public.regulasi.index') }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">Regulasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.sop.index') }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">SOP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.arsitektur_spbe') }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">Arsitektur SPBE</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.selengkapnya.paparan') }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">Paparan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.selengkapnya.agenda') }}"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">Agenda</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Kolom 4 --}}
                <div>
                    <div class="font-semibold mb-3 text-[#FFA84A]">Tautan Terkait</div>
                    <ul class="space-y-2">
                        <li>
                            <a href="https://www.menpan.go.id" target="_blank"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">KemenPANRB</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.komdigi.go.id" target="_blank"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">Kemenkominfo</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://bssn.go.id" target="_blank"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">BSSN</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://brin.go.id" target="_blank"
                                class="group inline-flex items-center gap-2 text-sm">
                                <span class="text-white/80 group-hover:text-[#FFA84A] transition">{!! $arrow !!}</span>
                                <span class="text-white/90 group-hover:text-[#FFA84A] transition">BRIN</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- ================= COPYRIGHT ================= --}}
        <div class="mx-auto w-[1200px] h-[64px] bg-[#FFA84A] grid place-items-center text-xs text-slate-900">
            © {{ now()->year }} Diskominfo Kabupaten Malang. Hak cipta dilindungi.
        </div>

    </div>
</footer>