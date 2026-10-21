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
        Schema::create('regulasi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('regulasi_categories')
                ->onDelete('cascade');
            $table->string('judul_regulasi');
            $table->string('file_pdf_path'); // regulasi biasanya wajib PDF
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulasi_items');
    }
};
