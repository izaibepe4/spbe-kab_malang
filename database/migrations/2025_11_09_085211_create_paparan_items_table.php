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
        Schema::create('paparan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('paparan_categories')
                ->onDelete('cascade');

            $table->string('judul_item');
            $table->enum('tipe_embed', ['slides', 'pdf'])->default('slides');
            $table->string('embed_url')->nullable();     // ex: https://docs.google.com/presentation/d/ID/embed
            $table->string('file_pdf_path')->nullable(); // disimpan di storage/public/paparan
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paparan_items');
    }
};
