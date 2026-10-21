<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLayananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'unit_pelaksana' => ['required', 'string', 'max:255'],
            'nama_pic' => ['nullable', 'string', 'max:255'],
            'email_pic' => ['nullable', 'email', 'max:255'],
            'hp_telp_pic' => ['nullable', 'string', 'max:20'],
            'bidang_bagian' => ['nullable', 'string', 'max:255'],
            'status_layanan' => ['nullable', 'string', 'max:100'],
            'nama_layanan' => ['required', 'string', 'max:500'],
            'deskripsi_layanan' => ['nullable', 'string'],
            'didukung_aplikasi' => ['required', 'boolean'],
            'target_pengguna' => ['nullable', 'string', 'max:255'],
            'kementerian_lembaga_terkait' => ['nullable', 'string', 'max:255'],
            'supplier_data' => ['nullable', 'string', 'max:255'],
            'data_masuk' => ['nullable', 'string', 'max:255'],
            'data_hasil' => ['nullable', 'string', 'max:255'],
            'keterbukaan_data' => ['nullable', 'string', 'max:100'],
            'jenis_data' => ['nullable', 'string', 'max:255'],
            'frekuensi_pembaruan' => ['nullable', 'string', 'max:100'],
            'terhubung_sistem_lain' => ['nullable', 'boolean'],
            'nama_aplikasi' => ['nullable', 'string', 'max:255'],
            'jenis_aplikasi' => ['nullable', 'string', 'max:100'],
            'jenis_aplikasi_lainnya' => ['nullable', 'string', 'max:255'],
            'url_akses' => ['nullable', 'url', 'max:500'],
            'hasil_output_aplikasi' => ['nullable', 'string'],
            'lokasi_penyimpanan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun minimal 2020.',
            'tahun.max' => 'Tahun maksimal 2100.',
            'unit_pelaksana.required' => 'Unit pelaksana wajib diisi.',
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'didukung_aplikasi.required' => 'Status dukungan aplikasi wajib diisi.',
            'email_pic.email' => 'Format email tidak valid.',
            'url_akses.url' => 'Format URL tidak valid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tahun' => 'tahun',
            'unit_pelaksana' => 'unit pelaksana',
            'nama_pic' => 'nama PIC',
            'email_pic' => 'email PIC',
            'hp_telp_pic' => 'HP/Telp PIC',
            'nama_layanan' => 'nama layanan',
            'didukung_aplikasi' => 'dukungan aplikasi',
        ];
    }
}
