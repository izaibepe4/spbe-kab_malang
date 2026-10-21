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
        Schema::table('tujuan_spbes', function (Blueprint $table) {
            if (Schema::hasColumn('tujuan_spbes', 'urutan')) {
                $table->dropColumn('urutan');
            }
            if (Schema::hasColumn('tujuan_spbes', 'aktif')) {
                $table->dropColumn('aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tujuan_spbes', function (Blueprint $table) {
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
        });
    }
};
