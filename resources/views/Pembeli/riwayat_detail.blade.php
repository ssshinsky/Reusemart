@extends('layouts.main')

@section('content')
<div class="container py-4">
    <a href="{{ route('pembeli.riwayat') }}" class="btn btn-outline-secondary mb-3">← Kembali ke Riwayat</a>

    <div class="card shadow-sm p-4">
        <h5 class="fw-bold text-success">Detail Transaksi</h5>
        <p class="mb-1">No Nota: <strong>{{ $transaksi->no_resi }}</strong></p>
        <p class="mb-1">Tanggal Pembelian: <strong>{{ \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->format('d M Y') }}</strong></p>
        <p class="mb-3">Status: 
            <span class="badge bg-{{ $transaksi->status_transaksi === 'Selesai' ? 'success' : 'secondary' }}">
                {{ $transaksi->status_transaksi }}
            </span>
        </p>

        <hr>

        <h6 class="fw-semibold">Alamat Pengiriman:</h6>
        {{-- Pastikan relasi 'alamat' ada dan di-eager load di controller --}}
        <p>{{ $transaksi->alamat->alamat_lengkap ?? '-' }}</p>

        <h6 class="fw-semibold mt-4">Metode Pengiriman:</h6>
        <p>{{ $transaksi->metode_pengiriman }}</p>

        <h6 class="fw-semibold mt-4">Detail Barang:</h6>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Berat</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    {{-- UBAH BAGIAN INI --}}
                    @if($transaksi->keranjang && !$transaksi->keranjang->detailKeranjangs->isEmpty())
                        @foreach($transaksi->keranjang->detailKeranjangs as $detailKeranjang)
                            @if($detailKeranjang->itemKeranjang && $detailKeranjang->itemKeranjang->barang)
                            <tr>
                                <td>{{ $detailKeranjang->itemKeranjang->barang->nama_barang }}</td>
                                <td>Rp {{ number_format($detailKeranjang->itemKeranjang->barang->harga_barang, 0, ',', '.') }}</td>
                                <td>{{ $detailKeranjang->itemKeranjang->barang->berat_barang }} kg</td>
                                {{-- Jika tidak ada kolom jumlah di detail_keranjang, asumsikan 1 unit barang --}}
                                <td>Rp {{ number_format($detailKeranjang->itemKeranjang->barang->harga_barang, 0, ',', '.') }}</td>
                                @php $subtotal += $detailKeranjang->itemKeranjang->barang->harga_barang; @endphp
                            </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">Tidak ada detail barang untuk transaksi ini.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Subtotal Harga Barang</th>
                        <th>Rp {{ number_format($transaksi->total_harga_barang ?? 0, 0, ',', '.') }}</th> {{-- Menggunakan total_harga_barang dari transaksi --}}
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Ongkir</th>
                        <th>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Poin Terpakai</th>
                        <th>{{ $transaksi->poin_terpakai ?? 0 }} poin</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Total Pembayaran</th>
                        <th>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Poin Diperoleh</th>
                        <th>{{ $transaksi->poin_pembeli ?? 0 }} poin</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection