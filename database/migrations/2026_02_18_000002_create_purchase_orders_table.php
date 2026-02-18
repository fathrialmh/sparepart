<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe', ['lokal', 'impor'])->default('lokal');
            $table->enum('tipe_pajak', ['ppn', 'non-ppn'])->default('ppn');
            $table->decimal('ppn_persen', 5, 2)->default(11);
            $table->enum('status', ['pending', 'confirmed', 'partial', 'completed', 'cancelled'])->default('pending');
            $table->date('expected_date')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
        Schema::dropIfExists('purchase_orders');
    }
};
