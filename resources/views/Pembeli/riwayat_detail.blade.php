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
                    @foreach($transaksi->detailPembelians as $item)
                    <tr>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td>Rp {{ number_format($item->barang->harga_barang, 0, ',', '.') }}</td>
                        <td>{{ $item->barang->berat_barang }} kg</td>
                        <td>Rp {{ number_format($item->barang->harga_barang, 0, ',', '.') }}</td>
                        @php $subtotal += $item->barang->harga_barang; @endphp
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Subtotal</th>
                        <th>Rp {{ number_format($subtotal, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Ongkir</th>
                        <th>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
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
