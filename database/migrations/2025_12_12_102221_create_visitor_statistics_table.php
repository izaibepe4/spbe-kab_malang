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
        Schema::create('visitor_statistics', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['male', 'female'])->comment('Jenis kelamin pengunjung');
            $table->string('session_id')->comment('Session ID untuk tracking unique visitor');
            $table->string('ip_address')->nullable()->comment('IP Address pengunjung');
            $table->string('user_agent')->nullable()->comment('User Agent browser');
            $table->date('visit_date')->comment('Tanggal kunjungan');
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['visit_date', 'gender']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_statistics');
    }
};
