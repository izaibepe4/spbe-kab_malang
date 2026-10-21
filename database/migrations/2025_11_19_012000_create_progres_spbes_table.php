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
        Schema::create('progres_spbes', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('gambar')->nullable(); // Path gambar thumbnail
            $table->string('file_pdf_path')->nullable(); // Path file PDF
            $table->decimal('nilai', 8, 2)->nullable(); // Nilai progres
            $table->integer('urutan')->default(0); // Urutan tampil
            $table->boolean('aktif')->default(true); // Status aktif/tidak aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_spbes');
    }
};
