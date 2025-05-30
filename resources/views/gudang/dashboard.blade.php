@extends('gudang.gudang_layout')

@section('title', 'Dashboard Pegawai Gudang')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Pegawai Gudang</h2>
            <p class="text-muted">Kelola transaksi penitipan barang dengan mudah</p>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex flex-wrap gap-3 mb-6">
        <a href="{{ route('gudang.add.transaction') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-2"></i> Tambah Transaksi Barang Titipan
        </a>
        <a href="{{ route('gudang.item.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-list-check me-2"></i> Daftar Barang Titipan
        </a>
        <a href="{{ route('gudang.transaction.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-search me-2"></i> Cari Transaksi
        </a>
    </div>

    <p></p>

    <!-- Statistics Cards -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="bi bi-plus-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <a href="{{ route('gudang.add.transaction') }}">
                            <h3 class="fw-semibold text-dark">Transaksi Baru</h3>
                            <p class="text-muted mb-0" id="total-transactions">{{ $totalTransactions }} Transaksi Hari Ini</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="bi bi-list-check text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <a href="{{ route('gudang.item.list') }}">
                            <h3 class="fw-semibold text-dark">Total Barang Titipan</h3>
                            <p class="text-muted mb-0" id="total-items">{{ $totalItems }} Barang</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection