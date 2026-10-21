@php
$isEdit = isset($layanan);
$action = $isEdit ? route('layanan.update', $layanan) : route('layanan.store');
$method = $isEdit ? 'PUT' : 'POST';
$data = $isEdit ? $layanan : null;
$tahun = $isEdit ? $layanan->tahun : (request('tahun') ?? date('Y'));
$didukungAplikasi = old('didukung_aplikasi', $data->didukung_aplikasi ?? false);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8" x-data="{ didukungAplikasi: {{ $didukungAplikasi ? 'true' : 'false' }} }">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <!-- Section 1: Identitas OPD & PIC -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-[#102A66] mb-4">Identitas OPD & PIC</h3>

        @php
        $isAdmin = isset($isAdmin) ? $isAdmin : false;
        $currentUser = isset($user) ? $user : Auth::guard('web')->user();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun *</label>
                <input type="number" name="tahun" value="{{ old('tahun', $tahun) }}" required min="2020" max="2100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
            <div x-data="{ triwulan: {{ old('triwulan', $data->triwulan ?? 1) }} }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Triwulan *</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="triwulan = 1" :class="triwulan === 1 ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Triwulan I
                    </button>
                    <button type="button" @click="triwulan = 2" :class="triwulan === 2 ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Triwulan II
                    </button>
                    <button type="button" @click="triwulan = 3" :class="triwulan === 3 ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Triwulan III
                    </button>
                    <button type="button" @click="triwulan = 4" :class="triwulan === 4 ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Triwulan IV
                    </button>
                </div>
                <input type="hidden" name="triwulan" :value="triwulan" required>
            </div>
            @if($isAdmin)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih OPD *</label>
                <select name="user_id" id="user_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                    <option value="">-- Pilih OPD --</option>
                    @foreach($users as $opdUser)
                    <option value="{{ $opdUser->id }}" {{ old('user_id', $data->user_id ?? '') == $opdUser->id ? 'selected' : '' }}>
                        {{ $opdUser->nama_opd }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Pelaksana *</label>
                <input type="text" name="unit_pelaksana" id="unit_pelaksana" value="{{ old('unit_pelaksana', $data->unit_pelaksana ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
            @else
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Pelaksana *</label>
                <input type="text" name="unit_pelaksana" value="{{ old('unit_pelaksana', $data->unit_pelaksana ?? Auth::user()->nama_opd ?? '') }}" required readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
            </div>
            @endif
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama PIC</label>
                @if($isAdmin)
                <input type="text" name="nama_pic" id="nama_pic" value="{{ old('nama_pic', $data->nama_pic ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                @else
                <input type="text" name="nama_pic" value="{{ old('nama_pic', $data->nama_pic ?? Auth::user()->name ?? '') }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email PIC</label>
                @if($isAdmin)
                <input type="email" name="email_pic" id="email_pic" value="{{ old('email_pic', $data->email_pic ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                @else
                <input type="email" name="email_pic" value="{{ old('email_pic', $data->email_pic ?? Auth::user()->email ?? '') }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">HP/Telp PIC</label>
                @if($isAdmin)
                <input type="text" name="hp_telp_pic" id="hp_telp_pic" value="{{ old('hp_telp_pic', $data->hp_telp_pic ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                @else
                <input type="text" name="hp_telp_pic" value="{{ old('hp_telp_pic', $data->hp_telp_pic ?? Auth::user()->no_hp ?? '') }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                @endif
            </div>
        </div>
    </div>

    <!-- Section 2: Detail Layanan -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-[#102A66] mb-4">Detail Layanan</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang/Bagian *</label>
                <input type="text" name="bidang_bagian" value="{{ old('bidang_bagian', $data->bidang_bagian ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
            <div x-data="{ status: '{{ old('status_layanan', $data->status_layanan ?? 'berjalan') }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Layanan *</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="status = 'berjalan'" :class="status === 'berjalan' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Berjalan
                    </button>
                    <button type="button" @click="status = 'direncanakan'" :class="status === 'direncanakan' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Direncanakan
                    </button>
                    <button type="button" @click="status = 'dihentikan'" :class="status === 'dihentikan' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Dihentikan
                    </button>
                </div>
                <input type="hidden" name="status_layanan" :value="status" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Layanan *</label>
                <input type="text" name="nama_layanan" value="{{ old('nama_layanan', $data->nama_layanan ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Layanan</label>
                <textarea name="deskripsi_layanan" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('deskripsi_layanan', $data->deskripsi_layanan ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Apakah layanan ini didukung oleh aplikasi? *</label>
                <div class="flex gap-4">
                    <button type="button" @click="didukungAplikasi = true" :class="didukungAplikasi ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                        Ya
                    </button>
                    <button type="button" @click="didukungAplikasi = false" :class="!didukungAplikasi ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                        Tidak
                    </button>
                </div>
                <input type="hidden" name="didukung_aplikasi" :value="didukungAplikasi ? '1' : '0'">
            </div>
            <div x-data="{ target: '{{ old('target_pengguna', $data->target_pengguna ?? 'Publik/Masyarakat') }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Target Pengguna *</label>
                <div class="flex gap-4">
                    <button type="button" @click="target = 'Publik/Masyarakat'" :class="target === 'Publik/Masyarakat' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                        Publik/Masyarakat
                    </button>
                    <button type="button" @click="target = 'Internal Pemerintahan'" :class="target === 'Internal Pemerintahan' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                        Internal Pemerintahan
                    </button>
                </div>
                <input type="hidden" name="target_pengguna" :value="target">
            </div>
        </div>
    </div>

    <!-- Section 3: Data & Keterkaitan -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-[#102A66] mb-4">Data & Keterkaitan</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kementerian/Lembaga Terkait</label>
                <textarea name="kementerian_lembaga_terkait" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('kementerian_lembaga_terkait', $data->kementerian_lembaga_terkait ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Supplier Data</label>
                <textarea name="supplier_data" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('supplier_data', $data->supplier_data ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Data yang Masuk</label>
                <textarea name="data_masuk" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('data_masuk', $data->data_masuk ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Data/Hasil yang Dihasilkan</label>
                <textarea name="data_hasil" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('data_hasil', $data->data_hasil ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div x-data="{ keterbukaan: '{{ old('keterbukaan_data', $data->keterbukaan_data ?? '') }}' }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterbukaan Data</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="keterbukaan = 'terbuka'" :class="keterbukaan === 'terbuka' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                            Terbuka
                        </button>
                        <button type="button" @click="keterbukaan = 'terbatas'" :class="keterbukaan === 'terbatas' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                            Terbatas
                        </button>
                        <button type="button" @click="keterbukaan = 'tertutup'" :class="keterbukaan === 'tertutup' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                            Tertutup
                        </button>
                    </div>
                    <input type="hidden" name="keterbukaan_data" :value="keterbukaan">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Data</label>
                    <input type="text" name="jenis_data" value="{{ old('jenis_data', $data->jenis_data ?? '') }}" placeholder="Statistik, Keuangan, Geospasial, dll" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Frekuensi Pembaruan</label>
                    <select name="frekuensi_pembaruan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                        <option value="">-- Pilih --</option>
                        <option value="Realtime" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Realtime' ? 'selected' : '' }}>Realtime</option>
                        <option value="Harian" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Harian' ? 'selected' : '' }}>Harian</option>
                        <option value="Mingguan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="Bulanan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="Tiga Bulanan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Tiga Bulanan' ? 'selected' : '' }}>Tiga Bulanan</option>
                        <option value="Enam Bulanan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Enam Bulanan' ? 'selected' : '' }}>Enam Bulanan</option>
                        <option value="Tahunan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                        <option value="Dua Tahunan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Dua Tahunan' ? 'selected' : '' }}>Dua Tahunan</option>
                        <option value="Tiga Tahunan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Tiga Tahunan' ? 'selected' : '' }}>Tiga Tahunan</option>
                        <option value="Lima Tahunan" {{ old('frekuensi_pembaruan', $data->frekuensi_pembaruan ?? '') == 'Lima Tahunan' ? 'selected' : '' }}>Lima Tahunan</option>
                    </select>
                </div>
                <div x-data="{ terhubung: {{ (old('terhubung_sistem_lain', $data->terhubung_sistem_lain ?? false)) ? 'true' : 'false' }} }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bisa terhubung ke sistem lain?</label>
                    <div class="flex gap-4">
                        <button type="button" @click="terhubung = true" :class="terhubung ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                            Ya
                        </button>
                        <button type="button" @click="terhubung = false" :class="!terhubung ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-6 py-2 rounded-lg font-semibold transition">
                            Tidak
                        </button>
                    </div>
                    <input type="hidden" name="terhubung_sistem_lain" :value="terhubung ? '1' : '0'">
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: Aplikasi -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-[#102A66] mb-4">Informasi Aplikasi</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Aplikasi</label>
                <input type="text" name="nama_aplikasi" value="{{ old('nama_aplikasi', $data->nama_aplikasi ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
            <div x-data="{ jenis: '{{ old('jenis_aplikasi', $data->jenis_aplikasi ?? '') }}', lainnya: '{{ old('jenis_aplikasi_lainnya', $data->jenis_aplikasi_lainnya ?? '') }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Aplikasi</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    <button type="button" @click="jenis = 'Web'; lainnya = ''" :class="jenis === 'Web' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Web
                    </button>
                    <button type="button" @click="jenis = 'Mobile'; lainnya = ''" :class="jenis === 'Mobile' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Mobile
                    </button>
                    <button type="button" @click="jenis = 'Desktop'; lainnya = ''" :class="jenis === 'Desktop' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Desktop
                    </button>
                    <button type="button" @click="jenis = 'Others'; lainnya = ''" :class="jenis === 'Others' ? 'bg-[#FFA84A] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-lg font-semibold transition text-sm">
                        Lainnya
                    </button>
                </div>
                <input type="hidden" name="jenis_aplikasi" :value="jenis">
                <div x-show="jenis === 'Others'" class="mt-2">
                    <input type="text" name="jenis_aplikasi_lainnya" x-model="lainnya" placeholder="Sebutkan jenis aplikasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat URL/Link Akses</label>
                <textarea name="url_akses" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('url_akses', $data->url_akses ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil/Output Utama</label>
                <textarea name="hasil_output_aplikasi" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">{{ old('hasil_output_aplikasi', $data->hasil_output_aplikasi ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Penyimpanan</label>
                <input type="text" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan', $data->lokasi_penyimpanan ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A]">
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="flex justify-end gap-4">
        <a href="{{ route('layanan.index', ['tahun' => $tahun]) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</a>
        <button type="submit" class="px-6 py-2 bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold rounded-lg transition">
            {{ $isEdit ? 'Update' : 'Simpan' }}
        </button>
    </div>
</form>

@if($isAdmin)
@php
$usersJson = $users->mapWithKeys(function($user) {
return [$user->id => [
'nama_opd' => $user->nama_opd,
'name' => $user->name,
'email' => $user->email,
'no_hp' => $user->no_hp,
]];
});
@endphp
<script>
    window.usersData = @json($usersJson);
</script>
<script src="{{ asset('js/layanan-form.js') }}"></script>
@endif