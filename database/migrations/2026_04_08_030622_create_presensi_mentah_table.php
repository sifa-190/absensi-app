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
Schema::create('presensi_mentah', function (Blueprint $table) {
    $table->id();
    $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
    $table->dateTime('waktu_absensi');
    $table->string('status_mesin', 50);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_mentah');
    }
};
