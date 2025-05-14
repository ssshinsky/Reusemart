<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Organisasi;
use App\Models\Pembeli;

class DashboardController extends Controller
{
    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
         
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
