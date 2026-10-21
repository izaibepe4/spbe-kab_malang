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
        Schema::table('section_info_spbes', function (Blueprint $table) {
            if (Schema::hasColumn('section_info_spbes', 'aktif')) {
                $table->dropColumn('aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_info_spbes', function (Blueprint $table) {
            $table->boolean('aktif')->default(true);
        });
    }
};
