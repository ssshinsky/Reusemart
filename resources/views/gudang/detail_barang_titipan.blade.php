@extends('gudang.gudang_layout')

@section('title', 'Detail Barang Titipan')

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-3 text-dark">
        <i class="fas fa-box me-2"></i> Detail Item: {{ $barang->nama_barang ?? 'N/A' }}
    </h2>

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('gudang.transaksi.pengiriman') }}" class="btn btn-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Back to Delivery & Pickup List
        </a>
    </div>

    <div class="row g-4">
        {{-- Kolom Informasi Barang --}}
        <div class="col-md-7">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-primary">Item Details</h5>
                    <p class="mb-2"><strong>Kode Barang:</strong> {{ $barang->kode_barang ?? '-' }}</p>
                    <p class="mb-2"><strong>Nama Barang:</strong> {{ $barang->nama_barang ?? '-' }}</p>
                    <p class="mb-2"><strong>Kategori:</strong> {{ $barang->kategori->nama_kategori ?? '-' }}</p>
                    <p class="mb-2"><strong>Harga:</strong> Rp{{ number_format($barang->harga_barang ?? 0, 0, ',', '.') }}</p>
                    <p class="mb-2"><strong>Berat:</strong> {{ $barang->berat_barang ?? '-' }} kg</p>
                    <p class="mb-2"><strong>Deskripsi:</strong> {{ $barang->deskripsi_barang ?? '-' }}</p>
                    <p class="mb-2"><strong>Status Garansi:</strong> {{ $barang->status_garansi ?? '-' }}</p>
                    @if ($barang->status_garansi == 'Warranty' && $barang->tanggal_garansi)
                        <p class="mb-2"><strong>Tanggal Garansi:</strong> {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->translatedFormat('d F Y') }}</p>
                    @endif
                    <p class="mb-2"><strong>Status Barang:</strong> 
                        <span class="badge bg-info text-dark">{{ $barang->status_barang ?? '-' }}</span>
                    </p>
                    <p class="mb-2"><strong>Batas Pengambilan:</strong> 
                        @if($barang->batas_pengambilan)
                            {{ \Carbon\Carbon::parse($barang->batas_pengambilan)->translatedFormat('d F Y, H:i') }}
                            @if($barang->batas_pengambilan < \Carbon\Carbon::now() && $barang->status_barang == 'Ready for Pickup')
                                <span class="badge bg-danger ms-2">Expired</span>
                            @endif
                        @else
                            -
                        @endif
                    </p>
                    @if($barang->tanggal_konfirmasi_pengambilan)
                        <p class="mb-2"><strong>Tanggal Konfirmasi Pengambilan:</strong> {{ \Carbon\Carbon::parse($barang->tanggal_konfirmasi_pengambilan)->translatedFormat('d F Y, H:i') }}</p>
                    @endif
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-primary">Item Images</h5>
                    <div class="row g-2">
                        @forelse ($barang->gambar as $gambar)
                            <div class="col-6">
                                <img src="{{ asset('storage/gambar_barang/' . $gambar->gambar_barang) }}" class="img-fluid rounded shadow-sm" alt="Gambar {{ $barang->nama_barang }}" style="height: 120px; object-fit: cover;">
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No images available for this item.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-muted small">
                    <i class="fas fa-clock me-1"></i>Last Updated: {{ $barang->updated_at?->diffForHumans() ?? 'Just Now' }}
                </div>
            </div>
        </div>

        {{-- Kolom Informasi Pemilik dan Gambar --}}
        <div class="col-md-5">
            <div class="card shadow-lg border-0 h-100 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-primary">Owner (Penitip) Information</h5>
                    @if($barang->transaksiPenitipan && $barang->transaksiPenitipan->penitip)
                        <p class="mb-2"><strong>Nama Penitip:</strong> {{ $barang->transaksiPenitipan->penitip->nama_penitip ?? '-' }}</p>
                        <p class="mb-2"><strong>Email Penitip:</strong> {{ $barang->transaksiPenitipan->penitip->email_penitip ?? '-' }}</p>
                        <p class="mb-2"><strong>No. Telepon:</strong> {{ $barang->transaksiPenitipan->penitip->no_telp ?? '-' }}</p>
                        <p class="mb-2"><strong>Alamat:</strong> {{ $barang->transaksiPenitipan->penitip->alamat ?? '-' }}</p>
                        <p class="mb-2"><strong>Tanggal Penitipan:</strong> {{ \Carbon\Carbon::parse($barang->transaksiPenitipan->tanggal_penitipan ?? null)->translatedFormat('d F Y, H:i') }}</p>
                    @else
                        <p class="text-muted">Owner information not available or not a consigned item.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection