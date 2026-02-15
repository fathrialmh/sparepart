<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('tipe', ['lokal', 'impor'])->default('lokal');
            $table->string('nomor_bc', 50)->nullable();
            $table->date('tanggal_bc')->nullable();
            $table->string('pelabuhan', 100)->nullable();
            $table->string('negara_asal', 80)->nullable();
            $table->decimal('total_nilai', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};
