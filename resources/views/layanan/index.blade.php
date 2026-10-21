<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Data Layanan SPBE</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .sticky-col {
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
        }

        .sticky-col-right {
            box-shadow: -2px 0 4px rgba(0, 0, 0, 0.1);
        }

        .text-cell-clickable {
            cursor: pointer;
            transition: all 0.2s;
        }

        .text-cell-clickable:hover {
            background-color: #f0f9ff;
            text-decoration: underline;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">
                        <span class="text-[#FFA84A]">Data Layanan</span>
                        <span class="text-[#102A66]"> SPBE</span>
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">
                            @php
                            $admin = Auth::guard('admin')->user();
                            $user = Auth::guard('web')->user();
                            @endphp
                            @if($admin)
                            {{ $admin->name }} (Admin)
                            @elseif($user)
                            {{ $user->name }}
                            @endif
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#102A66]">Tahun: <span class="text-[#FFA84A]">{{ $tahun }}</span></h2>
                    @if(isset($isAdmin) && $isAdmin)
                    <p class="text-sm text-gray-600 mt-1">Menampilkan semua data dari semua OPD</p>
                    @endif
                </div>
                <div class="flex gap-4">
                    @if(isset($isAdmin) && $isAdmin)
                    @if($users->count() > 0)
                    <select id="opdFilter" onchange="filterByOpd(this.value)" class="border border-gray-300 rounded px-3 py-2 min-w-[200px]">
                        <option value="">-- Semua OPD --</option>
                        @foreach($users as $opdUser)
                        <option value="{{ $opdUser->id }}" {{ request('opd_id') == $opdUser->id ? 'selected' : '' }}>
                            {{ $opdUser->nama_opd }} {{ !$opdUser->is_approved ? '(Belum Disetujui)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @else
                    <div class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded bg-gray-50">
                        Belum ada OPD terdaftar
                    </div>
                    @endif
                    @endif
                    <select id="tahunSelect" onchange="window.location.href='?tahun='+this.value{{ request('opd_id') ? '&opd_id='.request('opd_id') : '' }}" class="border border-gray-300 rounded px-3 py-2">
                        @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <a href="{{ route('layanan.create') }}?tahun={{ $tahun }}" class="bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold px-4 py-2 rounded-lg transition">
                        + Tambah Data
                    </a>
                </div>
            </div>

            @if($layanans->count() > 0)
            <div class="flex gap-0 overflow-hidden">
                <div class="flex-1 overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full divide-y divide-gray-200" style="min-width: 2000px;">
                        <thead class="bg-[#102A66] text-white">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap" style="min-width: 60px;">No</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Tahun</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Triwulan</th>
                                @if(isset($isAdmin) && $isAdmin)
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">OPD</th>
                                @endif
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Unit Pelaksana</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Nama PIC</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Email PIC</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">HP/Telp PIC</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Bidang/Bagian</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Status</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Nama Layanan</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Deskripsi</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Didukung Aplikasi</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Target Pengguna</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Kementerian/Lembaga</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Supplier Data</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Data Masuk</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Data Hasil</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Keterbukaan Data</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Jenis Data</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Frekuensi Pembaruan</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Terhubung Sistem Lain</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Nama Aplikasi</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Jenis Aplikasi</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Jenis Aplikasi Lainnya</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">URL Akses</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Hasil Output</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase whitespace-nowrap">Lokasi Penyimpanan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($layanans as $index => $layanan)
                            <tr class="hover:bg-gray-50 group">
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-center" style="min-width: 60px;">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap text-center">{{ $layanan->tahun }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap text-center">
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                        @php
                                        $romanNumerals = ['', 'I', 'II', 'III', 'IV'];
                                        @endphp
                                        Triwulan {{ $romanNumerals[$layanan->triwulan] ?? $layanan->triwulan }}
                                    </span>
                                </td>
                                @if(isset($isAdmin) && $isAdmin)
                                <td class="px-3 py-3 text-sm whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                        {{ $layanan->user->nama_opd ?? '-' }}
                                    </span>
                                </td>
                                @endif
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->unit_pelaksana }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->nama_pic ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->email_pic ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->hp_telp_pic ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->bidang_bagian }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $layanan->status_layanan == 'berjalan' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $layanan->status_layanan == 'direncanakan' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $layanan->status_layanan == 'dihentikan' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($layanan->status_layanan) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Nama Layanan"
                                    data-content="{{ htmlspecialchars($layanan->nama_layanan, ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ $layanan->nama_layanan }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Deskripsi Layanan"
                                    data-content="{{ htmlspecialchars($layanan->deskripsi_layanan ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->deskripsi_layanan ?? '-', 50) }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs {{ $layanan->didukung_aplikasi ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $layanan->didukung_aplikasi ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->target_pengguna ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Kementerian/Lembaga Terkait"
                                    data-content="{{ htmlspecialchars($layanan->kementerian_lembaga_terkait ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->kementerian_lembaga_terkait ?? '-', 40) }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Supplier Data"
                                    data-content="{{ htmlspecialchars($layanan->supplier_data ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->supplier_data ?? '-', 40) }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Data Masuk"
                                    data-content="{{ htmlspecialchars($layanan->data_masuk ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->data_masuk ?? '-', 40) }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Data Hasil"
                                    data-content="{{ htmlspecialchars($layanan->data_hasil ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->data_hasil ?? '-', 40) }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">
                                    @if($layanan->keterbukaan_data)
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                                {{ $layanan->keterbukaan_data == 'terbuka' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $layanan->keterbukaan_data == 'terbatas' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $layanan->keterbukaan_data == 'tertutup' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($layanan->keterbukaan_data) }}
                                    </span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->jenis_data ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->frekuensi_pembaruan ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs {{ $layanan->terhubung_sistem_lain ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $layanan->terhubung_sistem_lain ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Nama Aplikasi"
                                    data-content="{{ htmlspecialchars($layanan->nama_aplikasi ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ $layanan->nama_aplikasi ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ $layanan->jenis_aplikasi ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Jenis Aplikasi Lainnya"
                                    data-content="{{ htmlspecialchars($layanan->jenis_aplikasi_lainnya ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ $layanan->jenis_aplikasi_lainnya ?? '-' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap max-w-xs truncate">
                                    @if($layanan->url_akses)
                                    <a href="{{ $layanan->url_akses }}" target="_blank" class="text-blue-600 hover:underline truncate block max-w-xs" title="{{ $layanan->url_akses }}">{{ Str::limit($layanan->url_akses, 30) }}</a>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Hasil Output Aplikasi"
                                    data-content="{{ htmlspecialchars($layanan->hasil_output_aplikasi ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->hasil_output_aplikasi ?? '-', 40) }}</td>
                                <td class="px-3 py-3 text-sm max-w-xs truncate text-cell-clickable"
                                    data-title="Lokasi Penyimpanan"
                                    data-content="{{ htmlspecialchars($layanan->lokasi_penyimpanan ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openTextModalFromCell(this)"
                                    title="Klik untuk melihat lengkap">{{ Str::limit($layanan->lokasi_penyimpanan ?? '-', 40) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="w-36 flex-shrink-0 border-l-2 border-gray-300 bg-white">
                    <div class="sticky top-0 z-10">
                        <div class="bg-[#102A66] text-white px-3 py-3 text-xs font-semibold uppercase flex items-center">
                            Aksi
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($layanans as $index => $layanan)
                        <div class="px-3 py-3 flex flex-row gap-2 items-center justify-center border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <a href="{{ route('layanan.edit', $layanan) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm whitespace-nowrap transition-colors">Edit</a>
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('layanan.destroy', $layanan) }}" onsubmit="return confirm('Yakin hapus data ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm whitespace-nowrap transition-colors">Hapus</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg mb-2">Belum ada data layanan untuk tahun {{ $tahun }}</p>
                <a href="{{ route('layanan.create') }}?tahun={{ $tahun }}" class="text-[#FFA84A] hover:underline">Tambah data pertama</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal untuk menampilkan teks lengkap -->
    <div id="textModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-overlay" onclick="closeTextModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full mx-4 max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-[#102A66] text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold" id="modalTitle">Detail Teks</h3>
                <button onclick="closeTextModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
                <p class="text-gray-700 whitespace-pre-wrap break-words" id="modalContent">-</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button onclick="closeTextModal()" class="px-4 py-2 bg-[#102A66] text-white rounded-lg hover:bg-[#0d1f4d] transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openTextModalFromCell(cell) {
            const title = cell.getAttribute('data-title');
            const content = cell.getAttribute('data-content');
            openTextModal(title, content);
        }

        function openTextModal(title, content) {
            document.getElementById('modalTitle').textContent = title;
            const displayContent = (content === '-' || !content || content === 'null') ? 'Tidak ada data' : content;
            document.getElementById('modalContent').textContent = displayContent;
            document.getElementById('textModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTextModal() {
            document.getElementById('textModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTextModal();
            }
        });

        // Filter by OPD (for admin)
        function filterByOpd(opdId) {
            const url = new URL(window.location.href);
            if (opdId) {
                url.searchParams.set('opd_id', opdId);
            } else {
                url.searchParams.delete('opd_id');
            }
            window.location.href = url.toString();
        }
    </script>
</body>

</html>