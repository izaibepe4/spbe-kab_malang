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
        if (! Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip')->nullable()->after('nama_opd');
            });
        }

        if (! Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_hp')->nullable()->after('nip');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nip');
            });
        }

        if (Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('no_hp');
            });
        }
    }
};
