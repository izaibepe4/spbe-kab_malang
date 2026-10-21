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
        Schema::table('agenda_images', function (Blueprint $table) {
            if (Schema::hasColumn('agenda_images', 'judul')) {
                $table->dropColumn('judul');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_images', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('id');
        });
    }
};
