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
                'nama_pegawai' => 'Owner',
                'alamat_pegawai' => 'Jl satu owner',
                'tanggal_lahir' => '2002-02-02',
                'nomor_telepon' => '08191',
                'gaji_pegawai' => 10000000,
                'email_pegawai' => 'owner@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 2,
                'nama_pegawai' => 'Admin',
                'alamat_pegawai' => 'Jl dua admin',
                'tanggal_lahir' => '2002-02-02',
                'nomor_telepon' => '08192',
                'gaji_pegawai' => 2000000,
                'email_pegawai' => 'admin@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 3,
                'nama_pegawai' => 'CS',
                'alamat_pegawai' => 'Jl tiga cs',
                'tanggal_lahir' => '2003-03-03',
                'nomor_telepon' => '08193',
                'gaji_pegawai' => 3000000,
                'email_pegawai' => 'cs@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 4,
                'nama_pegawai' => 'Gudang',
                'alamat_pegawai' => 'Jl empat Gudang',
                'tanggal_lahir' => '2004-04-04',
                'nomor_telepon' => '08194',
                'gaji_pegawai' => 4000000,
                'email_pegawai' => 'gudang@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 5,
                'nama_pegawai' => 'Kurir',
                'alamat_pegawai' => 'Jl lima kurir',
                'tanggal_lahir' => '2005-05-05',
                'nomor_telepon' => '08195',
                'gaji_pegawai' => 5000000,
                'email_pegawai' => 'kurir@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
            [
                'id_role' => 6,
                'nama_pegawai' => 'Hunter',
                'alamat_pegawai' => 'Jl enam hunter',
                'tanggal_lahir' => '2006-06-06',
                'nomor_telepon' => '08196',
                'gaji_pegawai' => 6000000,
                'email_pegawai' => 'hunter@gmail.com',
                'password' => Hash::make('1234567890'),
            ],
        ];

        foreach ($pegawaiData as $data) {
            Pegawai::create($data);
        }
    }
}
