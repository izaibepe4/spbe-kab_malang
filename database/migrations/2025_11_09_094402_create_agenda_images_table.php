<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_images', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();          // opsional, untuk keterangan singkat
            $table->string('image_path');                 // lokasi file di storage/public
            $table->integer('urutan')->default(0);        // untuk sorting tampilan
            $table->boolean('aktif')->default(true);      // bisa di-nonaktifkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_images');
    }
};
