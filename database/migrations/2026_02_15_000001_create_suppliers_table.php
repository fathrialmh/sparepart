<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 120);
            $table->text('alamat')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->enum('tipe', ['lokal', 'impor'])->default('lokal');
            $table->string('negara_asal', 80)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
