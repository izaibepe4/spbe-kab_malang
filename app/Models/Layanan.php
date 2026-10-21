<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tahun',
        'triwulan',
        'unit_pelaksana',
        'nama_pic',
        'email_pic',
        'hp_telp_pic',
        'bidang_bagian',
        'status_layanan',
        'nama_layanan',
        'deskripsi_layanan',
        'didukung_aplikasi',
        'target_pengguna',
        'kementerian_lembaga_terkait',
        'supplier_data',
        'data_masuk',
        'data_hasil',
        'keterbukaan_data',
        'jenis_data',
        'frekuensi_pembaruan',
        'terhubung_sistem_lain',
        'nama_aplikasi',
        'jenis_aplikasi',
        'jenis_aplikasi_lainnya',
        'url_akses',
        'hasil_output_aplikasi',
        'lokasi_penyimpanan',
    ];

    protected $casts = [
        'didukung_aplikasi' => 'boolean',
        'terhubung_sistem_lain' => 'boolean',
        'tahun' => 'integer',
        'triwulan' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter berdasarkan triwulan
    public function scopeTriwulan($query, $triwulan)
    {
        return $query->where('triwulan', $triwulan);
    }

    // Accessor untuk nama triwulan
    public function getTriwulanNameAttribute()
    {
        return match ($this->triwulan) {
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4',
            default => 'Tidak Diketahui'
        };
    }
}
