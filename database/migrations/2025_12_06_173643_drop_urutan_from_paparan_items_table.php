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
        Schema::table('paparan_items', function (Blueprint $table) {
            if (Schema::hasColumn('paparan_items', 'urutan')) {
                $table->dropColumn('urutan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paparan_items', function (Blueprint $table) {
            $table->integer('urutan')->default(0);
        });
    }
};
