<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('tahun');
            $table->string('unit_pelaksana');
            $table->string('nama_pic')->nullable();
            $table->string('email_pic')->nullable();
            $table->string('hp_telp_pic')->nullable();
            $table->string('bidang_bagian');
            $table->enum('status_layanan', ['berjalan', 'direncanakan', 'dihentikan']);
            $table->string('nama_layanan');
            $table->text('deskripsi_layanan')->nullable();
            $table->boolean('didukung_aplikasi')->default(false);
            $table->enum('target_pengguna', ['Publik/Masyarakat', 'Internal Pemerintahan']);
            $table->text('kementerian_lembaga_terkait')->nullable(); // JSON atau text
            $table->text('supplier_data')->nullable();
            $table->text('data_masuk')->nullable();
            $table->text('data_hasil')->nullable();
            $table->enum('keterbukaan_data', ['terbuka', 'terbatas', 'tertutup'])->nullable();
            $table->string('jenis_data')->nullable();
            $table->enum('frekuensi_pembaruan', [
                'Realtime', 'Harian', 'Mingguan', 'Bulanan',
                'Tiga Bulanan', 'Enam Bulanan', 'Tahunan',
                'Dua Tahunan', 'Tiga Tahunan', 'Lima Tahunan',
            ])->nullable();
            $table->boolean('terhubung_sistem_lain')->default(false);
            $table->string('nama_aplikasi')->nullable();
            $table->string('jenis_aplikasi')->nullable(); // Web, Mobile, Desktop, Others
            $table->string('jenis_aplikasi_lainnya')->nullable(); // Jika pilih Others
            $table->text('url_akses')->nullable();
            $table->text('hasil_output_aplikasi')->nullable();
            $table->text('lokasi_penyimpanan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'tahun']);
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
