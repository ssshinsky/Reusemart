@extends('owner.owner_layout')

@section('title', 'History Donasi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">History Donasi</h2>
            <p class="text-muted">Lihat riwayat donasi yang telah dilakukan</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
        <div class="flex-grow-1">
            <form id="filter-form" action="{{ route('owner.donation.history') }}" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-filter text-muted"></i>
                    </span>
                    <select id="organisasi-filter" name="id_organisasi" class="form-select border-start-0" style="border-radius: 0 8px 8px 0;" onchange="this.form.submit()">
                        <option value="">Semua Organisasi</option>
                        @foreach ($organisasi as $org)
                            <option value="{{ $org->id_organisasi }}" {{ request()->input('id_organisasi') == $org->id_organisasi ? 'selected' : '' }}>
                                {{ $org->nama_organisasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <a href="{{ route('owner.download.donation.pdf') }}" class="btn btn-outline-success" target="_blank">
            <i class="bi bi-download me-2"></i> Unduh PDF
        </a>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 80px;">ID Donasi</th>
                        <th scope="col" style="width: 200px;">Barang</th>
                        <th scope="col" style="width: 200px;">Organisasi</th>
                        <th scope="col" style="width: 150px;">Tanggal</th>
                        <th scope="col" style="width: 200px;">Penerima</th>
                    </tr>
                </thead>
                <tbody id="donationTableBody">
                    @forelse ($donations as $donation)
                        <tr>
                            <td class="text-center">{{ $donation->id_donasi }}</td>
                            <td>{{ $donation->barang->nama_barang ?? 'N/A' }}</td>
                            <td>{{ $donation->requestDonasi->organisasi->nama_organisasi ?? 'N/A' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($donation->tanggal_donasi)->format('Y-m-d') }}</td>
                            <td>{{ $donation->nama_penerima }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i>Belum ada history donasi.
                            </td>
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

    .input-group-text {
        border-color: var(--border-color);
    }

    .form-select {
        border-color: var(--border-color);
        border-radius: 8px;
    }

    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 177, 79, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tidak perlu fetch karena data sudah dirender oleh Blade
    });
</script>
@endpush