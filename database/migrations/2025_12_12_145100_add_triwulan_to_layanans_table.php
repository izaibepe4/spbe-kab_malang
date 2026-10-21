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
        Schema::table('layanans', function (Blueprint $table) {
            $table->tinyInteger('triwulan')->after('tahun')->default(1)->comment('1=Triwulan I, 2=Triwulan II, 3=Triwulan III, 4=Triwulan IV');
            $table->index(['tahun', 'triwulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->dropIndex(['tahun', 'triwulan']);
            $table->dropColumn('triwulan');
        });
    }
};
