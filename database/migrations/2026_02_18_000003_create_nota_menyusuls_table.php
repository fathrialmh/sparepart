<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_menyusuls', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_pajak', ['ppn', 'non-ppn'])->default('ppn');
            $table->string('judul');
            $table->text('konten')->nullable();
            $table->string('referensi_dokumen')->nullable();
            $table->enum('status', ['draft', 'pending', 'completed'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_menyusuls');
    }
};
