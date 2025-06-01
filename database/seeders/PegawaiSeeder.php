<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiData = [
            [
                'id_role' => 1,
                'nama_pegawai' => 'Russel Owner',
                'alamat_pegawai' => 'Jl satu owner',
                'tanggal_lahir' => '2002-02-02',
                'nomor_telepon' => '08191',
                'gaji_pegawai' => 10000000,
                'email_pegawai' => 'russelowner@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 2,
                'nama_pegawai' => 'Russel Admin',
                'alamat_pegawai' => 'Jl dua admin',
                'tanggal_lahir' => '2002-02-02',
                'nomor_telepon' => '08192',
                'gaji_pegawai' => 2000000,
                'email_pegawai' => 'russeladmin@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 3,
                'nama_pegawai' => 'Russel CS',
                'alamat_pegawai' => 'Jl tiga cs',
                'tanggal_lahir' => '2003-03-03',
                'nomor_telepon' => '08193',
                'gaji_pegawai' => 3000000,
                'email_pegawai' => 'russelcs@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 4,
                'nama_pegawai' => 'Russel Gudang',
                'alamat_pegawai' => 'Jl empat Gudang',
                'tanggal_lahir' => '2004-04-04',
                'nomor_telepon' => '08194',
                'gaji_pegawai' => 4000000,
                'email_pegawai' => 'russelgudang@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 5,
                'nama_pegawai' => 'Russel Kurir',
                'alamat_pegawai' => 'Jl lima kurir',
                'tanggal_lahir' => '2005-05-05',
                'nomor_telepon' => '08195',
                'gaji_pegawai' => 5000000,
                'email_pegawai' => 'russelkurir@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 6,
                'nama_pegawai' => 'Russel Hunter',
                'alamat_pegawai' => 'Jl enam hunter',
                'tanggal_lahir' => '2006-06-06',
                'nomor_telepon' => '08196',
                'gaji_pegawai' => 6000000,
                'email_pegawai' => 'russelhunter@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
        ];

        foreach ($pegawaiData as $data) {
            Pegawai::create($data);
        }
    }
}
