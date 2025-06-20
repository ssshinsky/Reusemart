@extends('owner.owner_layout')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan</h2>
            <p class="text-muted">Pantau laporan aktivitas ReUse Mart</p>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex flex-wrap gap-3 mb-6">
        <a href="{{ route('owner.monthly.sales.report') }}" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-text me-2"></i> Laporan Penjualan
        </a>
        <a href="{{ route('owner.warehouse.stock.report') }}" class="btn btn-outline-primary">
            <i class="bi bi-warehouse me-2"></i> Laporan Stok Gudang
        </a>
        <a href="{{ route('owner.monthly.sales.overview') }}" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart me-2"></i> Laporan Penjualan Bulanan Keseluruhan
        </a>
        <a href="{{ route('owner.reports.sales_by_category') }}" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart-line me-2"></i> Lap. Penjualan per Kategori
        </a>
        <a href="{{ route('owner.reports.expired_items') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar-x me-2"></i> Lap. Masa Penitipan Habis
        </a>
    </div>

    <!-- Statistics Cards (kosongkan kalau nggak perlu data spesifik) -->
    <div class="row g-4">
        <!-- Bisa tambah card kalau ada data laporan spesifik di masa depan -->
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-4px);
    }

    .text-success {
        color: var(--primary-color) !important;
    }
</style>
@endpush