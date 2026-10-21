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
        Schema::table('progres_spbes', function (Blueprint $table) {
            $table->smallInteger('tahun')
                ->nullable()
                ->after('nilai')
                ->comment('Tahun capaian indikator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progres_spbes', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
