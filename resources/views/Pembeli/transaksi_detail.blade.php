@extends('layouts.main')

@section('content')
    <div class="container my-5">
        <h2 class="mb-4 text-center fw-bold">Detail Transaksi #{{ $transaksi->id }}</h2>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0">Informasi Transaksi</h5>
            </div>
            <div class="card-body">
                <p><strong>Tanggal:</strong> {{ $transaksi->created_at->format('d M Y, H:i') }}</p>
                <p><strong>Total Harga:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                <p><strong>Metode Pengiriman:</strong> {{ ucfirst($transaksi->metode_pengiriman ?? 'N/A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($transaksi->status_transaksi) }}</p>
                <p><strong>No. Resi:</strong> {{ $transaksi->no_resi ?? 'N/A' }}</p>
                <hr>
                <h6 class="fw-bold">Daftar Barang:</h6>
                <ul class="list-group list-group-flush">
                    @foreach ($transaksi->detail as $detail)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $detail->barang->nama_barang }} (x{{ $detail->jumlah ?? 1 }})</span>
                            <span>Rp {{ number_format($detail->barang->harga_barang, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a href="{{ route('pembeli.riwayat') }}" class="btn btn-outline-secondary mt-3">Kembali ke Riwayat</a>
    </div>
@endsection