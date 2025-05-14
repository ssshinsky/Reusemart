<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenitipController extends Controller
{
    public function create()
    {
        return view('Admin.Penitip.add_penitip'); // sesuaikan dengan view yang kamu miliki
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
        ]);

        Penitip::create([
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
            'status_penitip' => 'Active',
            'saldo_penitip' => 0,
            'rata_rating'     => 0,
        ]);

        return redirect()->route('admin.penitip.index')->with('success', 'Penitip berhasil ditambahkan.');
    }

    // Tampilkan profil penitip yang sedang login
    public function profile()
    {
        if (session('role') !== 'penitip') {
            return redirect('/login')->with('error', 'Unauthorized');
        }

        $id = session('user.id');
        $penitip = Penitip::find($id);

        if (!$penitip) {
            abort(404, 'Data penitip tidak ditemukan.');
        }

        return view('penitip.profile', compact('penitip'));
    }


    // Tampilkan halaman produk yang tersedia
    public function product()
    {
        $produk = \App\Models\Barang::with('kategori')->where('id_penitip', auth()->id())->get();
        return view('Penitip.product', compact('produk'));
    }

    // Tampilkan riwayat transaksi penitip
    public function transaction()
    {
        $transaksis = \App\Models\TransaksiPenitipan::with('barang')->where('id_penitip', auth()->id())->get();
        return view('Penitip.transaction', compact('transaksis'));
    }

    public function filterTransaction($type)
    {
        $query = \App\Models\TransaksiPenitipan::with('barang')
            ->where('id_penitip', auth()->id());

        switch ($type) {
            case 'sold':
                $query->where('status_transaksi', 'COMPLETED');
                break;
            case 'expired':
                $query->where('status_transaksi', 'EXPIRED');
                break;
            case 'donated':
                $query->where('status_transaksi', 'DONATED');
                break;
            case 'all':
            default:
                break;
        }

        $transaksis = $query->get();

        // Kembalikan view partial untuk AJAX
        return view('Penitip.partials.table', compact('transaksis'));
    }

    // Tampilkan produk milik penitip sendiri
    public function myproduct()
    {
        $produkSaya = \App\Models\Barang::where('id_penitip', auth()->id())->get();
        return view('Penitip.myproduct', compact('produkSaya'));
    }

    // Tampilkan saldo dan reward penitip
    public function rewards()
    {
        $penitip = auth()->user();
        return view('Penitip.rewards', compact('penitip'));
    }


    // Menampilkan halaman daftar penitip (item owners)
    public function index()
    {
        $penitips = Penitip::all();
        return view('Admin.Penitip.penitip', compact('penitips'));
    }

    // Menampilkan form edit penitip
    public function edit($id)
    {
        $penitip = Penitip::findOrFail($id);
        return view('Admin.Penitip.edit_penitip', compact('penitip'));
    }

    // Update data penitip
    public function update(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);

        $request->validate([
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip,' . $id . ',id_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip,' . $id . ',id_penitip',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
            'profil_pict' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

       if ($request->hasFile('profil_pict')) {
            $file = $request->file('profil_pict');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_penitip', $filename);

            $penitip->profil_pict = $filename;
        }

        $penitip->update([
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'profil_pict' => $penitip->profil_pict,
        ]);

        return redirect()->route('admin.penitip.index')->with('success', 'Data berhasil diperbarui.');
    }

    // Menonaktifkan penitip
    public function deactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Non Active']);

        return redirect()->route('admin.penitip.index')->with('success', 'Penitip dinonaktifkan.');
    }

    // Mengaktifkan kembali penitip
    public function reactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Active']);

        return redirect()->route('admin.penitip.index')->with('success', 'Penitip diaktifkan kembali.');
    }

    // Reset password penitip (ke tanggal lahir atau default tertentu, misalnya "123456")
    public function resetPassword($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update([
            'password' => Hash::make('123456') // ganti dengan password default sesuai kebutuhan
        ]);

        return redirect()->route('admin.penitip.index')->with('success', 'Password berhasil direset.');
    }

    public function search(Request $request)
    {
        if (!$request->ajax()) {
            return response('', 204);
        }

        $query = $request->query('q');

        $penitips = Penitip::where('nama_penitip', 'LIKE', "%$query%")
            ->orWhere('nik_penitip', 'LIKE', "%$query%")
            ->orWhere('email_penitip', 'LIKE', "%$query%")
            ->orWhere('nik_penitip', 'LIKE', "%$query%")
            ->get();

        $html = '';

        foreach ($penitips as $penitip) {
            $status = strtolower(trim($penitip->status_penitip));
            $html .= '
            <tr>
                <td class="center">'.$penitip->id_penitip.'</td>
                <td'.($status !== 'active' ? ' style="color: #E53E3E; font-weight: bold;"' : '').'>'.$penitip->nama_penitip.'</td>
                <td>'.$penitip->nik_penitip.'</td>
                <td>'.$penitip->email_penitip.'</td>
                <td>'.$penitip->no_telp.'</td>
                <td>'.$penitip->alamat.'</td>
                <td class="center">Rp '.number_format($penitip->saldo_penitip, 0, ',', '.').'</td>
                <td class="center">'.number_format($penitip->rata_rating, 1).'</td>
                <td class="center">'.ucwords($status).'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route('admin.penitip.edit', $penitip->id_penitip).'" class="edit-btn">✏️</a>';

            if ($status === 'active') {
                $html .= '
                    <form action="'.route('admin.penitip.deactivate', $penitip->id_penitip).'" method="POST" class="form-nonaktif" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Deactivate">🛑</button>
                    </form>';
            } else {
                $html .= '
                    <form action="'.route('admin.penitip.reactivate', $penitip->id_penitip).'" method="POST" class="form-reactivate" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Reactivate">♻️</button>
                    </form>';
            }

            $html .= '</td></tr>';

        }

        if ($penitips->isEmpty()) {
            $html = '<tr><td colspan="8" class="center">No item owner found.</td></tr>';
        }

        return response($html);
    }
}
