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
        Schema::create('indikator_spbes', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Kebijakan SPBE, Tata Kelola SPBE, Manajemen SPBE, Layanan SPBE
            $table->integer('nilai'); // Nilai indikator
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_spbes');
    }
};
