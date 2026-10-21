<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\RecaptchaRule;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Get list of OPDs
        $opds = $this->getOpdList();

        // Get already registered OPDs (termasuk yang belum di-approve)
        // Setiap OPD hanya bisa didaftarkan sekali, terlepas dari status approval
        $registeredOpds = User::whereNotNull('nama_opd')
            ->pluck('nama_opd')
            ->map(fn ($nama) => trim($nama)) // Normalize whitespace
            ->unique()
            ->values()
            ->toArray();

        // Filter out registered OPDs (case-insensitive comparison untuk keamanan)
        $availableOpds = array_filter($opds, function ($opd) use ($registeredOpds) {
            $opdNormalized = trim($opd);
            foreach ($registeredOpds as $registered) {
                // Case-insensitive comparison untuk memastikan tidak ada duplikasi
                if (strcasecmp($opdNormalized, $registered) === 0) {
                    return false;
                }
            }

            return true;
        });

        $recaptchaService = app(RecaptchaService::class);

        return view('auth.register', [
            'availableOpds' => $availableOpds,
            'recaptchaEnabled' => $recaptchaService->isEnabled(),
            'recaptchaSiteKey' => $recaptchaService->getSiteKey(),
        ]);
    }

    private function getOpdList(): array
    {
        return [
            'Dinas Pendidikan',
            'Dinas Kesehatan',
            'Dinas Pekerjaan Umum dan Penataan Ruang',
            'Dinas Perumahan Rakyat dan Kawasan Permukiman',
            'Dinas Sosial',
            'Dinas Tenaga Kerja',
            'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
            'Dinas Lingkungan Hidup',
            'Dinas Kependudukan dan Pencatatan Sipil',
            'Dinas Pemberdayaan Masyarakat dan Desa',
            'Dinas Pengendalian Penduduk dan Keluarga Berencana',
            'Dinas Perhubungan',
            'Dinas Komunikasi dan Informatika',
            'Dinas Koperasi, Usaha Kecil dan Menengah',
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
            'Dinas Kebudayaan dan Pariwisata',
            'Dinas Perpustakaan dan Kearsipan',
            'Dinas Kepemudaan dan Olahraga',
            'Dinas Ketahanan Pangan',
            'Dinas Pertanian',
            'Dinas Perikanan',
            'Dinas Peternakan',
            'Dinas Kehutanan',
            'Dinas Energi dan Sumber Daya Mineral',
            'Dinas Perindustrian dan Perdagangan',
            'Badan Perencanaan Pembangunan Daerah',
            'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia',
            'Badan Pengelolaan Keuangan dan Aset Daerah',
            'Badan Pendapatan Daerah',
            'Badan Kesatuan Bangsa dan Politik',
            'Badan Penanggulangan Bencana Daerah',
            'Inspektorat',
            'Sekretariat Daerah',
            'Sekretariat DPRD',
        ];
    }

    public function success()
    {
        return view('auth.register-success');
    }

    public function register(Request $request)
    {
        // Check if OPD is already registered
        $opds = $this->getOpdList();
        // Get already registered OPDs (termasuk yang belum di-approve)
        // Setiap OPD hanya bisa didaftarkan sekali, terlepas dari status approval
        $registeredOpds = User::whereNotNull('nama_opd')
            ->pluck('nama_opd')
            ->map(fn ($nama) => trim($nama)) // Normalize whitespace
            ->unique()
            ->values()
            ->toArray();

        $recaptchaService = app(RecaptchaService::class);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',

                // ============================================================
                // VALIDASI DOMAIN EMAIL @malangkab.go.id
                // ============================================================
                // Validasi ini DINONAKTIFKAN untuk keperluan testing/development
                //
                // CARA MENGAKTIFKAN:
                // 1. Hapus komentar (//) pada 3 baris di bawah ini
                // 2. Pastikan semua user OPD menggunakan email @malangkab.go.id
                //
                // CATATAN:
                // - Validasi ini memastikan hanya email resmi pemerintah yang bisa mendaftar
                // - Email admin tidak terpengaruh (admin menggunakan tabel terpisah)
                // ============================================================

                // VALIDASI DOMAIN EMAIL - DINONAKTIFKAN UNTUK DEVELOPMENT
                // Uncomment 4 baris di bawah untuk mengaktifkan validasi domain @malangkab.go.id
                // function ($attribute, $value, $fail) {
                //     if (!str_ends_with($value, '@malangkab.go.id')) {
                //         $fail('Email harus menggunakan domain resmi @malangkab.go.id');
                //     }
                // },
            ],
            'nip' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
                function ($attribute, $value, $fail) {
                    // Check if password contains only alphanumeric characters (no special chars)
                    if (! preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                        $fail('Password tidak boleh menggunakan karakter khusus.');
                    }
                    // Check if password contains at least one letter AND at least one number
                    if (! preg_match('/[a-zA-Z]/', $value) || ! preg_match('/[0-9]/', $value)) {
                        $fail('Password harus mengandung huruf dan angka.');
                    }
                },
            ],
            'nama_opd' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($opds, $registeredOpds) {
                    $valueNormalized = trim($value);

                    // Check if OPD is in the list
                    if (! in_array($valueNormalized, $opds)) {
                        $fail('OPD yang dipilih tidak valid.');
                    }

                    // Check if OPD is already registered (case-insensitive)
                    foreach ($registeredOpds as $registered) {
                        if (strcasecmp($valueNormalized, $registered) === 0) {
                            $fail('OPD ini sudah pernah melakukan pendaftaran. Silakan hubungi admin jika Anda memerlukan akses.');
                            break;
                        }
                    }
                },
            ],
        ];

        // Tambahkan validasi reCAPTCHA jika enabled
        $messages = [];
        if ($recaptchaService->isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule(config('recaptcha.actions.register', 'register'))];
            $messages['g-recaptcha-response.required'] = 'Silakan centang kotak "I\'m not a robot" untuk melanjutkan.';
        }

        $validated = $request->validate($rules, $messages);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nama_opd' => $validated['nama_opd'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'is_approved' => false, // Menunggu persetujuan admin
        ]);

        // Tidak langsung login, tunggu approval
        return redirect()->route('register.success')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan admin.');
    }
}
