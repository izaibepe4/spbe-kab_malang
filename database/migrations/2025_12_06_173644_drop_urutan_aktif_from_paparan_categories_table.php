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
        Schema::table('paparan_categories', function (Blueprint $table) {
            if (Schema::hasColumn('paparan_categories', 'urutan')) {
                $table->dropColumn('urutan');
            }
            if (Schema::hasColumn('paparan_categories', 'aktif')) {
                $table->dropColumn('aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paparan_categories', function (Blueprint $table) {
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
        });
    }
};
