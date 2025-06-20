@extends('layouts.main')

@section('content')
    <div class="container my-5">
        <h2 class="mb-4 text-center fw-bold">Riwayat Pembelian</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @forelse ($transaksi as $item)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi #{{ $item->id }}</h5>
                    <span class="badge 
                        @if ($item->status_transaksi == 'Menunggu Konfirmasi') bg-warning 
                        @elseif ($item->status_transaksi == 'Disiapkan') bg-info 
                        @elseif ($item->status_transaksi == 'Dikirim') bg-primary 
                        @elseif ($item->status_transaksi == 'Selesai') bg-success 
                        @elseif ($item->status_transaksi == 'Dibatalkan') bg-danger 
                        @else bg-secondary @endif">
                        {{ ucfirst($item->status_transaksi) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tanggal:</strong> {{ $item->created_at->format('d M Y, H:i') }}</p>
                            <p class="mb-1"><strong>Total:</strong> Rp {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                            <p class="mb-1"><strong>Metode Pengiriman:</strong> {{ ucfirst($item->metode_pengiriman ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('pembeli.transaksi.detail', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold">Daftar Barang:</h6>
                    <ul class="list-group list-group-flush">
                        @foreach ($item->detail as $detail)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $detail->barang->nama_barang }} (x{{ $detail->jumlah ?? 1 }})</span>
                                <span>Rp {{ number_format($detail->barang->harga_barang, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                Belum ada riwayat pembelian.
            </div>
        @endforelse

        @if ($transaksi->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>
@endsection