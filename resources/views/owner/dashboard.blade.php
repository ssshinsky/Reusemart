@extends('owner.owner_layout')

@section('title', 'Dashboard Owner')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Owner</h2>
            <p class="text-muted">Pantau aktivitas donasi dan alokasi barang</p>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex flex-wrap gap-3 mb-6">
        <a href="{{ route('owner.donation.requests') }}" class="btn btn-outline-primary">
            <i class="bi bi-list-check me-2"></i> Daftar Request Donasi
        </a>
        <a href="{{ route('owner.donation.history') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-2"></i> History Donasi
        </a>
        <a href="{{ route('owner.allocate.items') }}" class="btn btn-outline-primary">
            <i class="bi bi-box-seam me-2"></i> Alokasi Barang
        </a>
        <a href="{{ route('owner.update.donation') }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil-square me-2"></i> Update Donasi
        </a>
        <a href="{{ route('owner.rewards') }}" class="btn btn-outline-primary">
            <i class="bi bi-gift me-2"></i> Poin Reward
        </a>
        <a href="{{ route('owner.reports.sales_by_category') }}" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart-line me-2"></i> Lap. Penjualan per Kategori
        </a>
        <a href="{{ route('owner.reports.expired_items') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar-x me-2"></i> Lap. Masa Penitipan Habis
        </a>
    </div>
    
    <p></p>

    <!-- Statistics Cards -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="bi bi-list-check text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                      <a href="{{ route('owner.donation.requests') }}">
                          <h3 class="fw-semibold text-dark">Request Donasi</h3>
                          <p class="text-muted mb-0" id="total-requests">{{ $totalRequests }} Request Baru</p>
                      </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="bi bi-box-seam text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <a href="{{ route('owner.donation.history') }}">
                          <h3 class="fw-semibold text-dark">Total Donasi</h3>
                          <p class="text-muted mb-0" id="total-donations">{{ $totalDonations }} Barang</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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