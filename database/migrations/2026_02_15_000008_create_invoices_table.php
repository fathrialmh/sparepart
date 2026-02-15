<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('surat_jalan_id')->unique()->constrained('surat_jalan')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nilai', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('ongkos_kirim', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('sisa', 15, 2)->default(0);
            $table->string('pembayaran', 50)->nullable()->default('Transfer');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
