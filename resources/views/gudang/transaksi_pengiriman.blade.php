@extends('gudang.gudang_layout')

@section('title', 'Deliver & Pickup Product')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-2">Delivery & Pickup</h2>
            <p class="text-muted mb-0">Waiting list on progress...</p>
        </div>
    </div>
    
    <h3 class="fw-bold text-dark mb-3 mt-5">Items Ready for Owner Pickup</h3>
    <div class="row g-4">
        {{-- Pastikan variabel $barangReadyForPickup dikirim dari controller --}}
        @forelse ($barangReadyForPickup as $barangItem)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg border-0 transition-all card-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-success mb-3">
                            <i class="fas fa-box me-2"></i>Item #{{ $barangItem->kode_barang ?? 'N/A' }} 
                        </h5>
                        
                        <div class="d-flex flex-column gap-2">
                            <p class="card-text mb-1"><i class="fas fa-user me-2"></i><strong>Item Owner :</strong> {{ $barangItem->transaksiPenitipan->penitip->nama_penitip ?? '-' }}</p>
                            <p class="card-text mb-1"><i class="fas fa-clock me-2"></i><strong>Pickup Deadline:</strong> {{ \Carbon\Carbon::parse($barangItem->batas_pengambilan ?? null)->format('d M Y, H:i') }}</p>
                            <p class="card-text mb-1"><i class="fas fa-info-circle me-2"></i><strong>Status:</strong> <span class="badge bg-success text-white">{{ $barangItem->status_barang ?? 'N/A' }}</span></p>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="border p-2 rounded bg-light">
                                    <h6 class="fw-bold text-dark mb-2">{{ $barangItem->nama_barang ?? 'N/A' }}</h6>
                                    @if($barangItem->gambar->isNotEmpty())
                                        @foreach ($barangItem->gambar->take(2) as $gambar)
                                            <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="gambar" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                                        @endforeach
                                    @else
                                        <p>No image available</p>
                                    @endif
                                    <p class="mb-1"><strong>Price:</strong> Rp{{ number_format($barangItem->harga_barang ?? 0, 0, ',', '.') }}</p>
                                    <p class="mb-1"><strong>Weight:</strong> {{ $barangItem->berat_barang ?? 'N/A' }} kg</p>
                                    @if ($barangItem->id_kategori == 1)
                                        <p class="mb-1"><strong>Warranty Status:</strong> {{ $barangItem->status_garansi ?? 'N/A' }}</p>
                                    @endif
                                    <p class="mb-1"><strong>Item Status:</strong> {{ $barangItem->status_barang ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            {{-- Anda perlu membuat route 'gudang.transaksi.confirmPickupBarang' dan 'gudang.barang.detail' di web.php --}}
                            <form action="{{ route('gudang.transaksi.confirmPickupBarang', ['id' => $barangItem->id_barang]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Confirm Owner Pickup
                                </button>
                            </form>
                            <a href="{{ route('gudang.barang.detail', ['id' => $barangItem->id_barang]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="fas fa-eye me-1"></i> Detail Item
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-muted small">
                        <i class="fas fa-clock me-1"></i>Last Update: {{ $barangItem->updated_at?->diffForHumans() ?? 'Just Now' }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-lg border-0 text-center p-5 bg-light rounded-4">
                    <i class="fas fa-box-open text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 fs-4">Item owner's isn't want to pick their Items.</p>
                </div>
            </div>
        @endforelse
    </div>

    <h3 class="fw-bold text-dark mb-3 mt-5">Purchase Transactions (Delivery & Buyer Pick-up)</h3>
    <div class="row g-4">
        @forelse ($transaksi as $item)
            @php
                $tanggal = \Carbon\Carbon::parse($item->waktu_pembayaran);
                $noNota = $tanggal->format('y.m') . '.' . $item->id_pembelian;
                $firstDetail = $item->detailKeranjangs->first();
                $barang = $firstDetail ? $firstDetail->itemKeranjang->barang : null;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg border-0 transition-all card-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-primary mb-3">
                            <i class="fas fa-receipt me-2"></i>Invoice #{{ $noNota }}
                        </h5>
                        
                        <div class="d-flex flex-column gap-2">
                            {{-- Perhatikan: Logika ini untuk transaksi pembelian, jadi selalu Buyer --}}
                            <p class="card-text mb-1"><i class="fas fa-user me-2"></i><strong>Buyer:</strong> {{ $item->pembeli->nama_pembeli ?? '-' }}</p>
                            <p class="card-text mb-1"><i class="fas fa-calendar-alt me-2"></i><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y, H:i') }}</p>
                            <p class="card-text mb-1"><i class="fas fa-truck me-2"></i><strong>Shipping Method:</strong> {{ ucfirst($item->metode_pengiriman) }}</p>
                            <p class="card-text mb-1"><i class="fas fa-info-circle me-2"></i><strong>Status:</strong> <span class="badge bg-warning text-dark">{{ $item->status_transaksi }}</span></p>
                            <span class="badge bg-warning text-dark">{{ $item->status_pengiriman }}</span>

                            @if($item->status_transaksi == 'In Delivery' && $item->id_kurir)
                                @php
                                    $kurir = App\Models\Pegawai::find($item->id_kurir); // Ambil nama kurir berdasarkan id_kurir
                                @endphp
                                @if($kurir)
                                    <p class="card-text mb-1"><i class="fas fa-truck me-2"></i><strong>Courier:</strong> {{ $kurir->nama_pegawai }}</p>
                                @endif
                            @endif 
                            @if($item->metode_pengiriman == 'Self Pick-Up' && !is_null($item->tanggal_pengambilan))
                                <p class="card-text mb-1"><i class="fas fa-truck me-2"></i><strong>Pick Up Date:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d M Y, H:i') }}</p>
                            @endif
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="border p-2 rounded bg-light">
                                    <h6 class="fw-bold text-dark mb-2">{{ $barang->nama_barang ?? 'N/A' }}</h6>
                                    @if($barang && $barang->gambar->isNotEmpty())
                                        @foreach ($barang->gambar->take(2) as $gambar)
                                            <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="gambar" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                                        @endforeach
                                    @else
                                        <p>No image available</p>
                                    @endif
                                    <p class="mb-1"><strong>Price:</strong> Rp{{ number_format($barang->harga_barang ?? 0, 0, ',', '.') }}</p>
                                    <p class="mb-1"><strong>Weight:</strong> {{ $barang->berat_barang ?? 'N/A' }} kg</p>
                                    @if ($barang && $barang->id_kategori == 1)
                                        <p class="mb-1"><strong>Warranty Status:</strong> {{ $barang->status_garansi ?? 'N/A' }}</p>
                                    @endif
                                    <p class="mb-1"><strong>Status:</strong> {{ $barang->status_barang ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="{{ route('gudang.transaksi.schedule', ['id' => $item->id_pembelian]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-calendar-plus me-1"></i> Schedule
                            </a>
                            @if($item->status_transaksi == 'Ready for Pickup' && $item->kurir == Null)
                                <form action="{{ route('gudang.transaksi.confirmPickup', ['id' => $item->id_pembelian]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Confirmation Pickup
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('gudang.transaksi.detail', ['id' => $item->id_pembelian]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-eye me-1"></i> Detail
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
                    <p class="text-muted mt-3 fs-4">There is no transactions</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection