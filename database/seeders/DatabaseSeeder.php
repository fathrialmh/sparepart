<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => '$2y$12$chs3hMFocVbT863VjSKyQ.eLWJ5eKLjNSZVXCJR.O21VbS5mVb2Ba',
            'nama' => 'Administrator',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('suppliers')->insert([
            [
                'kode' => 'SUP-0001',
                'nama' => 'PT Lokal Jaya',
                'alamat' => 'Bandung',
                'telepon' => '081200000001',
                'email' => 'lokaljaya@example.com',
                'npwp' => null,
                'tipe' => 'lokal',
                'negara_asal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'SUP-0002',
                'nama' => 'Global Vent Import',
                'alamat' => 'Jakarta',
                'telepon' => '081200000002',
                'email' => 'globalvent@example.com',
                'npwp' => null,
                'tipe' => 'impor',
                'negara_asal' => 'China',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('customers')->insert([
            [
                'kode' => 'CUS-0001',
                'nama' => 'PT Tani Jaya Santosa',
                'alamat' => 'Bandung',
                'telepon' => '081300000001',
                'email' => 'tani@example.com',
                'npwp' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'CUS-0002',
                'nama' => 'Bpk. Anwar',
                'alamat' => 'Bandung',
                'telepon' => '081300000002',
                'email' => null,
                'npwp' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'CUS-0003',
                'nama' => 'PT Sukses Investa Anugrah Propertindo',
                'alamat' => 'Jakarta',
                'telepon' => '081300000003',
                'email' => null,
                'npwp' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('barang')->insert([
            [
                'kode' => 'BRG-0001',
                'nama' => 'sparepart VENTILATOR AJT24 + ADAPTOR 32',
                'satuan' => 'PCS',
                'harga_beli' => 900000,
                'harga_jual' => 1410000,
                'stok' => 50,
                'tipe' => 'lokal',
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'BRG-0002',
                'nama' => 'sparepart VENTILATOR AJT30 + ADAPTOR 35',
                'satuan' => 'PCS',
                'harga_beli' => 1450000,
                'harga_jual' => 2100000,
                'stok' => 30,
                'tipe' => 'lokal',
                'supplier_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'BRG-0003',
                'nama' => 'ADAPTOR AJT 24',
                'satuan' => 'PCS',
                'harga_beli' => 230000,
                'harga_jual' => 370000,
                'stok' => 100,
                'tipe' => 'impor',
                'supplier_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
