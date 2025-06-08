@extends('owner.owner_layout')

@section('title', 'Laporan Barang Habis Masa Penitipan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Barang yang Masa Penitipannya Sudah Habis</h2>
            <p class="text-muted">Tanggal Hari Ini: {{ $reportDate }}</p>
        </div>
        <div>
            <a href="{{ route('owner.reports.download_expired_items') }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Kode Produk</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col">ID Penitip</th>
                        <th scope="col">Nama Penitip</th>
                        <th scope="col">Tanggal Masuk</th>
                        <th scope="col">Tanggal Akhir</th>
                        <th scope="col">Batas Ambil</th> {{-- Tetap tampilkan kolom Batas Ambil untuk informasi --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expiredItems as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->transaksiPenitipan->penitip->id_penitip ?? 'N/A' }}</td>
                            <td>{{ $item->transaksiPenitipan->penitip->nama_penitip ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->transaksiPenitipan->tanggal_penitipan)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->transaksiPenitipan->tanggal_berakhir)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->batas_pengambilan)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada barang yang masa penitipan awalnya sudah habis dan belum terjual/didonasikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
    }
    .table td {
        border-bottom: 1px solid var(--border-color);
    }
</style>
@endpush