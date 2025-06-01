<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Pembeli
        DB::table('pembeli')->insert([
            'nama_pembeli'    => 'Russel Pembeli',
            'email_pembeli'   => 'russel.pembeli@gmail.com',
            'tanggal_lahir'   => '2001-01-01',
            'nomor_telepon'   => '0812300001',
            'password'        => Hash::make('1234567890'),
            'poin_pembeli'    => 0,
            'profil_pict'     => 'default.png',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Penitip
        DB::table('penitip')->insert([
            'nik_penitip'     => '220123',
            'nama_penitip'    => 'Russel Penitip',
            'email_penitip'   => 'russel.penitip@gmail.com',
            'password'        => Hash::make('1234567890'),
            'foto_ktp'        => null,
            'poin_penitip'    => 0,
            'no_telp'         => '0812300002',
            'alamat'          => 'Jl Percobaan',
            'rata_rating'     => 0,
            'status_penitip'  => 'Active',
            'saldo_penitip'   => 0,
            'profil_pict'     => 'default.png',
            'badge'           => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Organisasi
        DB::table('organisasi')->insert([
            'nama_organisasi'   => 'Russel Organisasi',
            'alamat'            => 'Jl Percobaan',
            'kontak'            => '0812300003',
            'email_organisasi'  => 'russel.organisasi@gmail.com',
            'password'          => Hash::make('1234567890'),
            'status_organisasi' => 'Active',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
