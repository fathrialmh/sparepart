<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe_pajak', ['ppn', 'non-ppn'])->default('ppn');
            $table->decimal('ppn_persen', 5, 2)->default(11);
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected'])->default('draft');
            $table->text('catatan')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('price_quotation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quotation_details');
        Schema::dropIfExists('price_quotations');
    }
};
