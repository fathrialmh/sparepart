<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('alamat_kirim')->nullable();
            $table->string('no_po', 60)->nullable();
            $table->string('no_polisi', 30)->nullable();
            $table->string('sopir', 80)->nullable();
            $table->enum('tipe_pajak', ['kena_pajak', 'tidak_kena_pajak'])->default('tidak_kena_pajak');
            $table->decimal('ppn_persen', 5, 2)->default(11.00);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->string('pembayaran', 50)->nullable()->default('C.O.D');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
