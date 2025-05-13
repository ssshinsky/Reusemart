<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Organisasi;
use App\Models\Pembeli;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahPegawai = Pegawai::count();
        $jumlahPenitip = Penitip::count();
        $jumlahOrganisasi = Organisasi::count();
        $jumlahPembeli = Pembeli::count();

        return view('Admin.dashboard', compact(
            'jumlahPegawai',
            'jumlahPenitip',
            'jumlahOrganisasi',
            'jumlahPembeli'
        ));
    }
}
