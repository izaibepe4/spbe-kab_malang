<?php

namespace App\Exports;

use App\Models\Layanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LayananExport implements FromCollection, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected $layanans;

    protected $firstLayanan;

    public function __construct($layanans)
    {
        $this->layanans = $layanans;
        $this->firstLayanan = $layanans->first();
    }

    public function collection()
    {
        return $this->layanans->map(function ($layanan, $index) {
            return [
                'No' => $index + 1,
                'Tahun' => $layanan->tahun ?? '',
                'Triwulan' => 'Q' . ($layanan->triwulan ?? ''),
                'Unit Pelaksana' => $layanan->unit_pelaksana ?? '',
                'Bidang/Bagian' => $layanan->bidang_bagian ?? '',
                'Status Layanan' => ucfirst($layanan->status_layanan ?? ''),
                'Nama Layanan' => $layanan->nama_layanan ?? '',
                'Deskripsi Layanan' => $layanan->deskripsi_layanan ?? '',
                'Ketersedian Aplikasi' => $layanan->didukung_aplikasi ? 'Ya' : 'Tidak',
                'Target Pengguna' => $layanan->target_pengguna ?? '',
                'K/L Terkait' => $layanan->kementerian_lembaga_terkait ?? '',
                'Supplier Data' => $layanan->supplier_data ?? '',
                'Nama Data Input' => $layanan->data_masuk ?? '',
                'Nama Data Output' => $layanan->data_hasil ?? '',
                'Sifat Data' => ucfirst($layanan->keterbukaan_data ?? ''),
                'Jenis Data' => $layanan->jenis_data ?? '',
                'Validitas Data' => '', // Field tidak ada di database
                'Interoperabilitas' => $layanan->terhubung_sistem_lain ? 'Ya' : 'Tidak',
                'Nama Aplikasi (Eksisting dan Direncanakan)' => $layanan->nama_aplikasi ?? '',
                'Tipe Aplikasi' => ($layanan->jenis_aplikasi === 'Others' && $layanan->jenis_aplikasi_lainnya)
                    ? $layanan->jenis_aplikasi_lainnya
                    : ($layanan->jenis_aplikasi ?? ''),
                'Link Aplikasi (Website, Mobile dan Lain Lain)' => $layanan->url_akses ?? '',
                'Keluaran Aplikasi' => $layanan->hasil_output_aplikasi ?? '',
                'Letak Server' => $layanan->lokasi_penyimpanan ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Triwulan',
            'Unit Pelaksana',
            'Bidang/Bagian',
            'Status Layanan',
            'Nama Layanan',
            'Deskripsi Layanan',
            'Ketersedian Aplikasi',
            'Target Pengguna',
            'K/L Terkait',
            'Supplier Data',
            'Nama Data Input',
            'Nama Data Output',
            'Sifat Data',
            'Jenis Data',
            'Validitas Data',
            'Interoperabilitas',
            'Nama Aplikasi (Eksisting dan Direncanakan)',
            'Tipe Aplikasi',
            'Link Aplikasi (Website, Mobile dan Lain Lain)',
            'Keluaran Aplikasi',
            'Letak Server',
        ];
    }

    public function title(): string
    {
        return 'Data Layanan';
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 10,  // Tahun
            'C' => 12,  // Triwulan
            'D' => 35,  // Unit Pelaksana
            'E' => 20,  // Bidang/Bagian
            'F' => 18,  // Status Layanan
            'G' => 40,  // Nama Layanan
            'H' => 50,  // Deskripsi Layanan
            'I' => 20,  // Ketersedian Aplikasi
            'J' => 25,  // Target Pengguna
            'K' => 30,  // K/L Terkait
            'L' => 30,  // Supplier Data
            'M' => 30,  // Nama Data Input
            'N' => 30,  // Nama Data Output
            'O' => 15,  // Sifat Data
            'P' => 20,  // Jenis Data
            'Q' => 18,  // Validitas Data
            'R' => 18,  // Interoperabilitas
            'S' => 40,  // Nama Aplikasi
            'T' => 20,  // Tipe Aplikasi
            'U' => 40,  // Link Aplikasi
            'V' => 40,  // Keluaran Aplikasi
            'W' => 30,  // Letak Server
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            8 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header informasi OPD (Baris 1-5)
                $firstLayanan = $this->firstLayanan;
                if ($firstLayanan && $firstLayanan->user) {
                    $opdName = $firstLayanan->user->name ?? '';
                    $unitPelaksana = $firstLayanan->unit_pelaksana ?? '';
                    $namaPic = $firstLayanan->nama_pic ?? '';
                    $emailPic = $firstLayanan->email_pic ?? '-';
                    $hpTelpPic = $firstLayanan->hp_telp_pic ?? '';

                    // Baris 1: OPD
                    $sheet->setCellValue('B1', $opdName);

                    // Baris 2: Unit Pelaksana
                    $sheet->setCellValue('B2', 'Unit Pelaksana : ' . $unitPelaksana);
                    $sheet->setCellValue('D2', 'GOES TO STATUS');

                    // Baris 3: Nama PIC
                    $sheet->setCellValue('B3', 'Nama : ' . $namaPic);

                    // Baris 4: Email PIC
                    $sheet->setCellValue('B4', 'Email : ' . $emailPic);

                    // Baris 5: HP/Telp PIC - akan ditimpa oleh header deskripsi, jadi kita skip atau set di tempat lain
                    // Dari gambar 2, HP/Telp sepertinya tidak ditampilkan di baris 5 karena tertimpa header deskripsi
                }

                // Header deskripsi panjang (Baris 5, kolom A-W)
                $descriptions = [
                    'A5' => 'Urutan Nomor',
                    'B5' => 'Tahun Pelaporan',
                    'C5' => 'Triwulan Pelaporan (I, II, III, IV)',
                    'D5' => 'Nama Organisasi Perangkat Daerah (OPD) atau unit pelaksana yang bertanggung jawab terhadap layanan',
                    'E5' => 'Nama bidang, seksi, atau bagian yang menangani layanan tersebut dalam unit pelaksana.',
                    'F5' => 'Apakah layanan (berjalan, direncanakan, atau dihentikan.)',
                    'G5' => 'Nama layanan digital atau non-digital yang diberikan kepada publik atau internal pemerintahan.',
                    'H5' => 'Penjelasan singkat mengenai layanan: tujuan, siapa yang dilayani, dan lingkup kegiatannya.',
                    'I5' => 'Apakah layanan ini didukung oleh aplikasi? (Ya / Tidak). Jika ya, lanjutkan ke kolom aplikasi.',
                    'J5' => 'Sasaran pengguna layanan: Publik/Masyarakat Atau Internal Pemerintahan',
                    'K5' => 'Kementerian/Lembaga lain yang',
                    'L5' => 'Supplier Data',
                    'M5' => 'Nama Data Input',
                    'N5' => 'Nama Data Output',
                    'O5' => 'Sifat Data',
                    'P5' => 'Jenis Data',
                    'Q5' => 'Validitas Data',
                    'R5' => 'Interoperabilitas',
                    'S5' => 'Nama Aplikasi (Eksisting dan Direncanakan)',
                    'T5' => 'Tipe Aplikasi',
                    'U5' => 'Link Aplikasi (Website, Mobile dan Lain Lain)',
                    'V5' => 'Keluaran Aplikasi',
                    'W5' => 'Letak Server',
                ];

                foreach ($descriptions as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                    $sheet->getStyle($cell)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE2EFDA'); // Light green background
                    $sheet->getStyle($cell)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Set HP/Telp PIC di baris 5 kolom J (setelah header deskripsi) atau skip jika tidak perlu
                // Dari gambar 2, HP/Telp sepertinya tidak ditampilkan di baris 5 karena tertimpa header
                // Tapi jika perlu, kita bisa set di kolom lain atau baris lain
                if ($firstLayanan) {
                    $hpTelpPic = $firstLayanan->hp_telp_pic ?? '';
                    // Skip untuk sekarang karena akan tertimpa header deskripsi
                    // Jika perlu, bisa ditambahkan di baris 6 atau kolom J
                }

                // Header nama kolom singkat (Baris 7)
                $headers = [
                    'A7' => 'No',
                    'B7' => 'Tahun',
                    'C7' => 'Triwulan',
                    'D7' => 'Unit Pelaksana',
                    'E7' => 'Bidang/Bagian',
                    'F7' => 'Status Layanan',
                    'G7' => 'Nama Layanan',
                    'H7' => 'Deskripsi Layanan',
                    'I7' => 'Ketersedian Aplikasi',
                    'J7' => 'Target Pengguna',
                    'K7' => 'K/L Terkait',
                    'L7' => 'Supplier Data',
                    'M7' => 'Nama Data Input',
                    'N7' => 'Nama Data Output',
                    'O7' => 'Sifat Data',
                    'P7' => 'Jenis Data',
                    'Q7' => 'Validitas Data',
                    'R7' => 'Interoperabilitas',
                    'S7' => 'Nama Aplikasi (Eksisting dan Direncanakan)',
                    'T7' => 'Tipe Aplikasi',
                    'U7' => 'Link Aplikasi (Website, Mobile dan Lain Lain)',
                    'V7' => 'Keluaran Aplikasi',
                    'W7' => 'Letak Server',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($cell)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD9E1F2'); // Light blue background
                    $sheet->getStyle($cell)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Set tinggi baris untuk header deskripsi
                $sheet->getRowDimension(5)->setRowHeight(60);
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Styling untuk data rows (mulai baris 8)
                $lastRow = $sheet->getHighestRow();
                for ($row = 8; $row <= $lastRow; $row++) {
                    // Border untuk semua cell (A sampai W)
                    $sheet->getStyle('A' . $row . ':W' . $row)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    // Alignment untuk kolom tertentu
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('R' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Wrap text untuk kolom deskripsi dan text panjang
                    $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('H' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    $sheet->getStyle('L' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('M' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('N' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('V' . $row)->getAlignment()->setWrapText(true);
                }

                // Merge cells untuk header deskripsi jika perlu
                // Tidak perlu merge karena setiap kolom punya deskripsi sendiri

                // Styling untuk header informasi OPD
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B2:B5')->getFont()->setSize(10);
            },
        ];
    }
}
