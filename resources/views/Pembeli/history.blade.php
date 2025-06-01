@extends('layouts.main')

@section('content')
    <div class="container">
        <h2 class="mb-4">Purchase History</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @forelse ($riwayat as $transaksi)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Transaction #{{ $transaksi->id }}</h5>
                    <p class="card-text">
                        Date: {{ $transaksi->created_at->format('d M Y') }}<br>
                        Total: IDR {{ number_format($transaksi->total_harga, 0, ',', '.') }}<br>
                        Shipping Method: {{ ucfirst($transaksi->metode_pengiriman) }}<br>
                        Status: <strong>{{ ucfirst($transaksi->status_pembayaran) }}</strong>
                    </p>
                    <h6>Items:</h6>
                    <ul>
                        @foreach ($transaksi->detail as $detail)
                            <li>{{ $detail->barang->nama_barang }} - IDR
                                {{ number_format($detail->barang->harga_barang, 0, ',', '.') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No purchases have been made yet.</div>
        @endforelse
    </div>
@endsection
