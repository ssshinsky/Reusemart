@extends('gudang.gudang_layout')

@section('title', 'Deliver & Pickup Product')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-2">Transaksi Pengiriman & Pengambilan</h2>
            <p class="text-muted mb-0">Daftar transaksi pembelian yang perlu diproses</p>
        </div>
    </div>

    <!-- Transaction Cards -->
    <div class="row g-4">
        @forelse ($transaksi as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg border-0 transition-all card-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-primary mb-3">
                            <i class="fas fa-receipt me-2"></i>Invoice #{{ $item->no_nota }}
                        </h5>
                        @foreach ($item->detailKeranjangs as $detail)
                            <div class="d-flex flex-column gap-2">
                                @if($detail->itemKeranjang && $detail->itemKeranjang->barang->status_barang == 'Ready for Pickup' && $detail->itemKeranjang->barang->batas_pengambilan > now())
                                    <p class="card-text mb-1"><i class="fas fa-user me-2"></i><strong>Owner (Penitip):</strong> {{ $detail->itemKeranjang->barang->transaksiPenitipan->penitip->nama_penitip ?? '-' }}</p>
                                @else
                                    <p class="card-text mb-1"><i class="fas fa-user me-2"></i><strong>Buyer:</strong> {{ $item->pembeli->nama_pembeli ?? '-' }}</p>
                                @endif
                                <p class="card-text mb-1"><i class="fas fa-calendar-alt me-2"></i><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y, H:i') }}</p>
                                <p class="card-text mb-1"><i class="fas fa-truck me-2"></i><strong>Shipping Method:</strong> {{ ucfirst($item->metode_pengiriman) }}</p>
                                <p class="card-text mb-1"><i class="fas fa-info-circle me-2"></i><strong>Status:</strong> <span class="badge bg-warning text-dark">{{ $item->status_transaksi }}</span></p>
                                <span class="badge bg-warning text-dark">{{ $item->status_pengiriman }}</span>
                            </div>
                        @endforeach

                        <div class="row mt-3">
                            @foreach ($item->detailKeranjangs as $detail)
                                <div class="col-6">
                                    <div class="border p-2 rounded bg-light">
                                        <p class="mb-1"><strong>{{ $detail->itemKeranjang->barang->nama_barang }}</strong></p>
                                        @foreach ($detail->itemKeranjang->barang->gambar->take(2) as $gambar)
                                            <img src="{{ asset('storage/gambar_barang/' . $gambar->gambar_barang) }}" alt="gambar" class="img-fluid rounded" style="height: 80px; object-fit: cover;">
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="{{ route('gudang.transaksi.detail', ['id' => $item->id_pembelian]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                            <a href="{{ route('gudang.transaksi.schedule', ['id' => $item->id_pembelian]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-calendar-plus me-1"></i> Schedule
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-muted small">
                        <i class="fas fa-clock me-1"></i>Last Update: {{ $item->updated_at?->diffForHumans() ?? 'Just Now' }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-lg border-0 text-center p-5 bg-light rounded-4">
                    <i class="fas fa-info-circle text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 fs-4">Tidak ada transaksi yang perlu diproses saat ini.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

