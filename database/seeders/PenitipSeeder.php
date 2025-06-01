<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenitipSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('penitip')->insert([
            [
                'nik_penitip' => '3471010101010001',
                'nama_penitip' => 'Rahmat Suryadi',
                'email_penitip' => 'rahmat.suryadi@example.com',
                'password' => Hash::make('Tg8Rpa39'),
                'no_telp' => '081345671001',
                'alamat' => 'Jl. Kaliurang Km 6, Sleman',
                'status_penitip' => 'Active',
                'saldo_penitip' => 1200000,
            ],
            [
                'nik_penitip' => '3471020202020002',
                'nama_penitip' => 'Winda Lestari',
                'email_penitip' => 'winda.lestari@example.com',
                'password' => Hash::make('Ae93TpQz'),
                'no_telp' => '081345671002',
                'alamat' => 'Jl. Palagan Tentara Pelajar, Sleman',
                'status_penitip' => 'Active',
                'saldo_penitip' => 980000,
            ],
            [
                'nik_penitip' => '3471030303030003',
                'nama_penitip' => 'Surya Prakoso',
                'email_penitip' => 'surya.prakoso@example.com',
                'password' => Hash::make('Xv91RtLp'),
                'no_telp' => '081345671003',
                'alamat' => 'Jl. Magelang Km 5, Sleman',
                'status_penitip' => 'Active',
                'saldo_penitip' => 1570000,
            ],
            [
                'nik_penitip' => '3471040404040004',
                'nama_penitip' => 'Dewi Marlina',
                'email_penitip' => 'dewi.marlina@example.com',
                'password' => Hash::make('Mn23KjTp'),
                'no_telp' => '081345671004',
                'alamat' => 'Jl. Gejayan No. 20, Sleman',
                'status_penitip' => 'Active',
                'saldo_penitip' => 450000,
            ],
            [
                'nik_penitip' => '3471050505050005',
                'nama_penitip' => 'Hendra Wijaya',
                'email_penitip' => 'hendra.wijaya@example.com',
                'password' => Hash::make('Pl56QrUz'),
                'no_telp' => '081345671005',
                'alamat' => 'Jl. Babarsari No. 17, Sleman',
                'status_penitip' => 'Non Active',
                'saldo_penitip' => 2100000,
            ],
            [
                'nik_penitip' => '3471060606060006',
                'nama_penitip' => 'Tari Ningsih',
                'email_penitip' => 'tari.ningsih@example.com',
                'password' => Hash::make('As7TlZxRe'),
                'no_telp' => '081345671006',
                'alamat' => 'Jl. Wonosari Km 8, Bantul',
                'status_penitip' => 'Active',
                'saldo_penitip' => 1020000,
            ],
            [
                'nik_penitip' => '3471070707070007',
                'nama_penitip' => 'Doni Kurniawan',
                'email_penitip' => 'doni.kurniawan@example.com',
                'password' => Hash::make('Vc38MnLp'),
                'no_telp' => '081345671007',
                'alamat' => 'Jl. Imogiri Timur, Bantul',
                'status_penitip' => 'Active',
                'saldo_penitip' => 320000,
            ],
            [
                'nik_penitip' => '3471080808080008',
                'nama_penitip' => 'Nina Andriani',
                'email_penitip' => 'nina.andriani@example.com',
                'password' => Hash::make('Lo45BxNz'),
                'no_telp' => '081345671008',
                'alamat' => 'Jl. Ringroad Selatan, Bantul',
                'status_penitip' => 'Active',
                'saldo_penitip' => 1450000,
            ],
            [
                'nik_penitip' => '3471090909090009',
                'nama_penitip' => 'Fajar Maulana',
                'email_penitip' => 'fajar.maulana@example.com',
                'password' => Hash::make('Zw84TpOk'),
                'no_telp' => '081345671009',
                'alamat' => 'Jl. Timoho No. 3, Yogyakarta',
                'status_penitip' => 'Active',
                'saldo_penitip' => 875000,
            ],
            [
                'nik_penitip' => '3471101010100010',
                'nama_penitip' => 'Siska Amelia',
                'email_penitip' => 'siska.amelia@example.com',
                'password' => Hash::make('Hg9lZmQa'),
                'no_telp' => '081345671010',
                'alamat' => 'Jl. Affandi, Yogyakarta',
                'status_penitip' => 'Active',
                'saldo_penitip' => 2330000,
            ],
        ]);
    }
}
