@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-success fw-bold border-bottom pb-2" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">Riwayat Pembelian</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse ($riwayat as $transaksi)
        <div class="card mb-4 shadow-sm border-0 rounded-3">
            <div class="card-body p-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-dark fw-bold">Transaksi #{{ $transaksi->id_pembelian }}</h5>
                    <span class="badge bg-success text-white">{{ ucfirst($transaksi->status_transaksi) }}</span>
                </div>
                <p class="card-text text-muted">
                    Tanggal: <span class="fw-medium">{{ $transaksi->created_at->format('d M Y') }}</span><br>
                    Total: <span class="fw-medium text-success">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span><br>
                    Pengiriman: <span class="fw-medium">{{ ucfirst($transaksi->metode_pengiriman) }}</span>
                </p>
                <h6 class="mt-3 text-primary">Item:</h6>
                <ul class="list-group list-group-flush">
                    @foreach ($transaksi->keranjang->detailKeranjang as $detail)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span>{{ $detail->itemKeranjang->barang->nama_barang }}</span>
                                @if ($detail->itemKeranjang->barang->rating)
                                    <div class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $detail->itemKeranjang->barang->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="text-muted small">({{ $detail->itemKeranjang->barang->rating }}/5)</span>
                                    </div>
                                @else
                                    <div class="text-muted small">Belum dirating</div>
                                @endif
                            </div>
                            <span class="text-success">Rp {{ number_format($detail->itemKeranjang->barang->harga_barang, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                @if ($transaksi->status_transaksi === 'selesai' && $transaksi->keranjang->detailKeranjang->contains(function ($detail) {
                    return is_null($detail->itemKeranjang->barang->rating);
                }))
                    <div class="mt-3">
                        <a href="{{ route('pembeli.rating', $transaksi->id_pembelian) }}" class="btn btn-primary btn-sm">Beri Rating</a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center py-5">Belum ada transaksi pembelian.</div>
    @endforelse
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.25rem 0.75rem;
    }
    .list-group-item {
        background-color: transparent;
        border: none;
        padding: 0.5rem 0;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    .fa-star {
        font-size: 0.9rem;
    }
</style>
@endsection
