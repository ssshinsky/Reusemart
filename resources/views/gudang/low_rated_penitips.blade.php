@extends('gudang.gudang_layout')

@section('title', 'Daftar Penitip dengan Rating Rendah')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-2">Daftar Penitip dengan Rating Rendah</h2>
            <p class="text-muted mb-0">Penitip dengan rata-rata rating ≤ 3</p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('gudang.low_rated_penitips') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="keyword" class="form-label text-muted">Cari Penitip</label>
                        <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari berdasarkan nama, email, atau nomor telepon..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label text-muted">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_selesai" class="form-label text-muted">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Penitip Cards -->
    <div class="row g-4">
        @forelse ($penitips as $penitip)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg border-0 transition-all card-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-primary mb-3">
                            <i class="fas fa-user me-2"></i>{{ $penitip->nama_penitip }}
                        </h5>
                        <div class="d-flex flex-column gap-2">
                            <p class="card-text mb-1"><i class="fas fa-star me-2"></i><strong>Rata-rata Rating:</strong> {{ $penitip->rata_rating ? number_format($penitip->rata_rating, 1) : 'Belum dirating' }}</p>
                            <p class="card-text mb-1"><i class="fas fa-envelope me-2"></i><strong>Email:</strong> {{ $penitip->email_penitip ?? 'N/A' }}</p>
                            <p class="card-text mb-3"><i class="fas fa-phone me-2"></i><strong>No. Telepon:</strong> {{ $penitip->no_telp ?? 'N/A' }}</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" onclick="showDetailModal('detailModal-{{ $penitip->id_penitip }}')">
                                <i class="fas fa-eye me-1"></i>Detail
                            </button>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-muted small d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-1"></i>Diperbarui: {{ $penitip->updated_at ? $penitip->updated_at->diffForHumans() : 'Belum diperbarui' }}</span>
                        <span class="badge bg-secondary text-white">{{ $penitip->penitipan->count() }} Transaksi</span>
                    </div>
                </div>
            </div>

            <!-- Modal for Penitip Details -->
            <div class="custom-modal" id="detailModal-{{ $penitip->id_penitip }}">
                <div class="custom-modal-content">
                    <div class="custom-modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-user me-2"></i>{{ $penitip->nama_penitip }}
                        </h5>
                        <button type="button" class="custom-modal-close" onclick="hideModal('detailModal-{{ $penitip->id_penitip }}')">×</button>
                    </div>
                    <div class="custom-modal-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">Informasi Penitip</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3 info-grid">
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    <span class="label">Nama:</span>
                                    <span class="value">{{ $penitip->nama_penitip }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-star me-2 text-primary"></i>
                                    <span class="label">Rata-rata Rating:</span>
                                    <span class="value">{{ $penitip->rata_rating ? number_format($penitip->rata_rating, 1) : 'Belum dirating' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <span class="label">Email:</span>
                                    <span class="value">{{ $penitip->email_penitip ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    <span class="label">No. Telepon:</span>
                                    <span class="value">{{ $penitip->no_telp ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <span class="label">Alamat:</span>
                                    <span class="value">{{ $penitip->alamat ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-money-bill me-2 text-primary"></i>
                                    <span class="label">Saldo:</span>
                                    <span class="value">Rp {{ number_format($penitip->saldo_penitip, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-boxes me-2 text-primary"></i>
                                    <span class="label">Jumlah Transaksi:</span>
                                    <span class="value">{{ $penitip->penitipan->count() }} Transaksi</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <span class="label">Terakhir Diperbarui:</span>
                                    <span class="value">{{ $penitip->updated_at ? \Carbon\Carbon::parse($penitip->updated_at)->format('d M Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-dark mt-4">Riwayat Transaksi</h6>
                        @if($penitip->penitipan->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ID Transaksi</th>
                                            <th>Tanggal Penitipan</th>
                                            <th>Jumlah Barang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penitip->penitipan as $index => $transaksi)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $transaksi->id_transaksi_penitipan }}</td>
                                                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_penitipan)->format('d M Y') }}</td>
                                                <td>{{ $transaksi->barang->count() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted"><i class="fas fa-info-circle me-2"></i>Belum ada transaksi.</p>
                        @endif
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" onclick="hideModal('detailModal-{{ $penitip->id_penitip }}')">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-lg border-0 text-center p-5 bg-light rounded-4">
                    <i class="fas fa-info-circle text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 fs-4">Belum ada penitip dengan rating rendah.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    body, .card, .btn, .badge, p, h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #00b4d8);
    }

    .badge {
        padding: 0.6em 1.2em;
        font-weight: 600;
        border-radius: 20px;
    }

    .btn-sm {
        border-radius: 20px;
        padding: 0.25rem 1rem;
        font-size: 0.875rem;
    }

    .bg-light {
        background-color: #f8f9fa;
    }

    /* Custom Modal Styles */
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        justify-content: center;
        align-items: center;
    }

    .custom-modal-content {
        background-color: white;
        border-radius: 15px;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .custom-modal-header {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-body {
        padding: 1.5rem;
    }

    .custom-modal-footer {
        padding: 1rem;
        text-align: right;
    }

    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: white;
        cursor: pointer;
    }

    .custom-modal-close:hover {
        color: #ddd;
    }

    /* Grid Layout for Info */
    .info-grid {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e0e4e8;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item i {
        font-size: 1.2rem;
        color: #007bff;
        margin-right: 10px;
    }

    .info-item .label {
        font-weight: 500;
        color: #333;
        margin-right: 10px;
        min-width: 130px;
    }

    .info-item .value {
        color: #555;
        font-weight: 400;
        flex: 1;
        word-break: break-word;
    }

    /* Style tambahan untuk form search */
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e4e8;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        border-radius: 8px;
    }

    /* Table Styling */
    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table th, .table td {
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5f9;
    }
</style>
@endpush

@push('scripts')
<script>
    function showDetailModal(modalId) {
        console.log('Showing detail modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            console.log('Detail modal shown successfully');
        } else {
            console.error('Detail modal not found:', modalId);
        }
    }

    function hideModal(modalId) {
        console.log('Hiding modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            console.log('Modal hidden successfully');
        } else {
            console.error('Modal not found:', modalId);
        }
    }
</script>
@endpush