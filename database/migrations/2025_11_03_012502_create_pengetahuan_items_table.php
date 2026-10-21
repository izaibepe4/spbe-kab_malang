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
        Schema::create('pengetahuan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('pengetahuan_categories')
                ->onDelete('cascade');
            $table->string('judul_item');
            $table->string('file_pdf_path')->nullable(); // optional PDF
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengetahuan_items');
    }
};
