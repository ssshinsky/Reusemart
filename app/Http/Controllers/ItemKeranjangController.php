<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\DetailKeranjang;
use App\Models\Barang;
use App\Models\Alamat;
use App\Models\TransaksiPembelian;
use App\Models\Pembeli;

class ItemKeranjangController extends Controller
{
    public function index()
    {
        $user = session('user');
        $role = session('role');

        if (!$user || $role !== 'pembeli') {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai pembeli.');
        }

        $items = ItemKeranjang::with('barang')
            ->where('id_pembeli', $user['id'])
            ->get();

        // Hitung total harga dengan kuantitas = 1
        $totalHarga = $items->sum(fn($item) => $item->barang->harga_barang);

        $alamatPembeli = Alamat::where('id_pembeli', $user['id'])->get();

        // Debugging
        \Log::info('Keranjang Index', [
            'user_id' => $user['id'],
            'items_count' => $items->count(),
            'total_harga' => $totalHarga,
            'alamat_count' => $alamatPembeli->count(),
        ]);

        return view('pembeli.keranjang', compact('items', 'totalHarga', 'alamatPembeli'));
    }

    public function tambah($id)
    {
        $user = session('user');
        $role = session('role');

        if ($role !== 'pembeli' || !$user) {
            return response('Unauthorized', 403);
        }

        $idPembeli = $user['id'];

        // Cek apakah barang sudah ada di keranjang pembeli
        $existingItem = ItemKeranjang::where('id_pembeli', $idPembeli)
            ->where('id_barang', $id)
            ->first();

        // Tambahkan ke keranjang
        ItemKeranjang::create([
            'id_pembeli' => $idPembeli,
            'id_barang' => $id,
            'is_selected' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // Hapus item dari keranjang
    public function hapus($id)
    {
        $item = ItemKeranjang::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('message', 'Barang dihapus dari keranjang.');
    }

    // Update pilihan barang untuk checkout
    public function toggleSelect($id)
    {
        $item = ItemKeranjang::findOrFail($id);
        $item->is_selected = !$item->is_selected;
        $item->save();

        return redirect()->back();
    }

    public function checkout(Request $request)
{
    \Log::info('Checkout Request:', $request->all());

    $user = session('user');
    $role = session('role');

    if ($role !== 'pembeli' || !$user) {
        \Log::error('User not logged in or not pembeli', ['user' => $user, 'role' => $role]);
        return redirect()->back()->with('error', 'Anda harus login sebagai pembeli.');
    }

    $idPembeli = $user['id'];

    // Konversi selected_items ke integer
    $selectedItems = array_map('intval', $request->input('selected_items'));
    $request->merge(['selected_items' => $selectedItems]);

    // Validasi input
    try {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*' => [
                'required',
                Rule::exists('item_keranjang', 'id_item_keranjang')->where(function ($query) use ($idPembeli) {
                    $query->where('id_pembeli', $idPembeli);
                }),
            ],
            'metode_pengiriman' => 'required|string|in:kurir,ambil',
            'id_alamat' => 'required_if:metode_pengiriman,kurir|nullable|exists:alamat,id_alamat',
        ], [
            'selected_items.required' => 'Pilih setidaknya satu item untuk checkout.',
            'selected_items.*.exists' => 'Item yang dipilih tidak ada di keranjang Anda atau tidak valid.',
            'metode_pengiriman.required' => 'Pilih metode pengiriman.',
            'id_alamat.required_if' => 'Pilih alamat pengiriman untuk metode kurir.',
            'id_alamat.exists' => 'Alamat yang dipilih tidak valid.',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed', ['errors' => $e->errors()]);
        return redirect()->back()->withErrors($e->validator)->withInput();
    }

    $selectedItems = $request->selected_items;
    $metodePengiriman = $request->metode_pengiriman;
    $idAlamat = $metodePengiriman === 'ambil' ? null : $request->id_alamat;

    $pembeli = Pembeli::find($idPembeli);
    if (!$pembeli) {
        \Log::error('Pembeli not found', ['id_pembeli' => $idPembeli]);
        return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
    }

    $items = ItemKeranjang::whereIn('id_item_keranjang', $selectedItems)
        ->where('id_pembeli', $idPembeli)
        ->with('barang')
        ->get();

    if ($items->isEmpty()) {
        \Log::error('No items found', ['selected_items' => $selectedItems, 'id_pembeli' => $idPembeli]);
        return redirect()->back()->with('error', 'Tidak ada item yang dipilih atau item tidak valid.');
    }

    // Lanjutkan kode seperti sebelumnya
    $totalHargaBarang = $items->sum(fn($item) => $item->barang->harga_barang);
    $ongkir = ($totalHargaBarang >= 1500000 || $metodePengiriman !== 'kurir') ? 0 : 100000;
    $totalHarga = $totalHargaBarang + $ongkir;

    $keranjang = Keranjang::create([
        'banyak_barang' => $items->count(),
    ]);

    foreach ($items as $item) {
        DetailKeranjang::create([
            'id_keranjang' => $keranjang->id_keranjang,
            'id_item_keranjang' => $item->id_item_keranjang,
        ]);
    }

    $poinDimiliki = $user['poin_pembeli'] ?? $pembeli->poin_pembeli ?? 0;

    session([
        'checkout_keranjang_id' => $keranjang->id_keranjang,
        'checkout_selected_items' => $selectedItems,
        'checkout_metode_pengiriman' => $metodePengiriman,
        'checkout_id_alamat' => $idAlamat,
        'checkout_total_harga' => $totalHarga,
        'checkout_total_harga_barang' => $totalHargaBarang,
        'checkout_ongkir' => $ongkir,
    ]);

    $alamat = $idAlamat ? Alamat::where('id_alamat', $idAlamat)->where('id_pembeli', $idPembeli)->first() : null;

    \Log::info('Checkout data', [
        'items' => $items->toArray(),
        'totalHarga' => $totalHarga,
        'poinDimiliki' => $poinDimiliki,
        'metodePengiriman' => $metodePengiriman,
        'alamat' => $alamat,
    ]);

    return view('pembeli.pembelian', [
        'items' => $items,
        'totalHarga' => $totalHarga,
        'poinDimiliki' => $poinDimiliki,
        'metodePengiriman' => $metodePengiriman,
        'alamat' => $alamat,
        'ongkir' => $ongkir,
    ]);
}

}

