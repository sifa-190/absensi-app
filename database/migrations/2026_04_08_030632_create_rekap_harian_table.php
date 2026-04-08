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
Schema::create('rekap_harian', function (Blueprint $table) {
    $table->id();
    $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
    $table->date('tanggal');
    $table->time('jam_masuk');
    $table->time('jam_pulang');
    $table->decimal('total_jam_kerja', 4, 2);
    $table->string('status_kehadiran', 20);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_harian');
    }
};
