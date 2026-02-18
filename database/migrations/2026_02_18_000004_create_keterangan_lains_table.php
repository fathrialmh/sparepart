<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keterangan_lains', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->string('judul');
            $table->enum('kategori', ['kebijakan', 'prosedur', 'peraturan', 'informasi', 'komunikasi', 'lainnya'])->default('informasi');
            $table->enum('tipe_pajak', ['ppn', 'non-ppn'])->default('ppn');
            $table->text('konten')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keterangan_lains');
    }
};
