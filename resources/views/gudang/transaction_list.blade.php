@extends('gudang.gudang_layout')

@section('title', 'Daftar Transaksi Barang Titipan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-2">Daftar Transaksi Barang Titipan</h2>
            <p class="text-muted mb-0">Kelola transaksi barang titipan dengan mudah</p>
        </div>
        <a href="{{ route('gudang.add.transaction') }}" class="btn btn-primary btn-sm shadow-sm rounded-pill">
            <i class="fas fa-plus me-2"></i>Tambah Transaksi
        </a>
    </div>

    <!-- Notifikasi Sukses -->
    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- Search Form -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('gudang.transaction.list') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="keyword" class="form-label text-muted">Cari Transaksi</label>
                        <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari berdasarkan ID transaksi, nama penitip, nama barang, dll..." value="{{ request('keyword') }}">
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

    <!-- Transaction Cards -->
    <div class="row g-4">
        @forelse ($transactions as $transaction)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg border-0 transition-all card-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-primary mb-3">
                            <i class="fas fa-box me-2"></i>Transaksi #{{ $transaction->id_transaksi_penitipan }}
                        </h5>
                        <div class="d-flex flex-column gap-2">
                            <p class="card-text mb-1"><i class="fas fa-user me-2"></i><strong>Penitip:</strong> {{ $transaction->penitip->nama_penitip ?? 'N/A' }}</p>
                            <p class="card-text mb-3"><i class="fas fa-calendar-alt me-2"></i><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($transaction->tanggal_penitipan)->format('d M Y') }}</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('gudang.transaction.edit', $transaction->id_transaksi_penitipan) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-edit me-1"></i>Edit</a>
                            <a href="{{ route('gudang.transaction.print', $transaction->id_transaksi_penitipan) }}" class="btn btn-sm btn-outline-success rounded-pill"><i class="fas fa-print me-1"></i>Cetak</a>
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" onclick="showDetailModal('detailModal-{{ $transaction->id_transaksi_penitipan }}')"><i class="fas fa-eye me-1"></i>Detail</button>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-muted small d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-1"></i>Diperbarui: {{ $transaction->updated_at ? $transaction->updated_at->diffForHumans() : 'Belum diperbarui' }}</span>
                        <span class="badge bg-secondary text-white">{{ $transaction->barang->count() }} Item</span>
                    </div>
                </div>
            </div>

            <!-- Modal for Transaction Details -->
            <div class="custom-modal" id="detailModal-{{ $transaction->id_transaksi_penitipan }}">
                <div class="custom-modal-content">
                    <div class="custom-modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-box me-2"></i>Detail Transaksi #{{ $transaction->id_transaksi_penitipan }}
                        </h5>
                        <button type="button" class="custom-modal-close" onclick="hideModal('detailModal-{{ $transaction->id_transaksi_penitipan }}')">×</button>
                    </div>
                    <div class="custom-modal-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="custom-modal-close" onclick="this.parentElement.style.display='none'">×</button>
                            </div>
                        @endif

                        <h6 class="fw-bold mb-3 text-dark">Informasi Transaksi</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3 info-grid">
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-box me-2 text-primary"></i>
                                    <span class="label">ID Transaksi:</span>
                                    <span class="value">{{ $transaction->id_transaksi_penitipan }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    <span class="label">Penitip:</span>
                                    <span class="value">{{ $transaction->penitip->nama_penitip ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                    <span class="label">Tanggal Masuk:</span>
                                    <span class="value">{{ \Carbon\Carbon::parse($transaction->tanggal_penitipan)->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-user-check me-2 text-primary"></i>
                                    <span class="label">QC:</span>
                                    <span class="value">{{ $transaction->qc->nama_pegawai ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-truck me-2 text-primary"></i>
                                    <span class="label">Hunter:</span>
                                    <span class="value">{{ $transaction->hunter->nama_pegawai ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="info-item">
                                    <i class="fas fa-boxes me-2 text-primary"></i>
                                    <span class="label">Jumlah Barang:</span>
                                    <span class="value">{{ $transaction->barang->count() }} Item</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-dark mt-4">Daftar Barang</h6>
                        @forelse ($transaction->barang as $item)
                            @php
                                \Illuminate\Support\Facades\Log::info('Rendering barang', [
                                    'id_barang' => $item->id_barang,
                                    'nama_barang' => $item->nama_barang,
                                ]);
                            @endphp
                            @if (!$item->id_barang)
                                <p class="text-danger small mt-2">Error: ID barang tidak valid untuk {{ $item->nama_barang }}</p>
                            @endif
                            <div class="card mb-3 shadow-sm border-0">
                                <div class="card-body p-3 bg-light rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="card-title fw-bold text-dark m-0">{{ $item->nama_barang }}</h6>
                                        <span class="badge text-white 
                                            {{ strtolower($item->status_barang) == 'tersedia' ? 'bg-success' : 
                                            (strtolower($item->status_barang) == 'barang untuk donasi' ? 'bg-warning' : 
                                            (strtolower($item->status_barang) == 'selesai' ? 'bg-primary' : 
                                            (strtolower($item->status_barang) == 'sedang dikirim' ? 'bg-info' : 
                                            (strtolower($item->status_barang) == 'menunggu pengambilan' ? 'bg-secondary' : 
                                            (strtolower($item->status_barang) == 'diproses' ? 'bg-warning' : 
                                            (strtolower($item->status_barang) == 'dibatalkan' ? 'bg-danger' : 
                                            (strtolower($item->status_barang) == 'menunggu pembayaran' ? 'bg-info' : 
                                            (strtolower($item->status_barang) == 'didonasikan' ? 'bg-success' : 'bg-secondary')))))))) }}">
                                            {{ $item->status_barang }}
                                        </span>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-2 g-3">
                                        <div class="col">
                                            <div class="info-item">
                                                <i class="fas fa-barcode me-2 text-primary"></i>
                                                <span class="label">Kode Barang:</span>
                                                <span class="value">{{ $item->kode_barang }}</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-money-bill me-2 text-primary"></i>
                                                <span class="label">Harga:</span>
                                                <span class="value">Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-weight me-2 text-primary"></i>
                                                <span class="label">Berat:</span>
                                                <span class="value">{{ $item->berat_barang }} kg</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                                <span class="label">Deskripsi:</span>
                                                <span class="value">{{ $item->deskripsi_barang }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="info-item">
                                                <i class="fas fa-shield-alt me-2 text-primary"></i>
                                                <span class="label">Status Garansi:</span>
                                                <span class="value">{{ $item->status_garansi }}</span>
                                            </div>
                                            @if ($item->tanggal_garansi)
                                                <div class="info-item">
                                                    <i class="fas fa-calendar-check me-2 text-primary"></i>
                                                    <span class="label">Tanggal Garansi:</span>
                                                    <span class="value">{{ \Carbon\Carbon::parse($item->tanggal_garansi)->format('d M Y') }}</span>
                                                </div>
                                            @endif
                                            <div class="info-item">
                                                <i class="fas fa-calendar-times me-2 text-primary"></i>
                                                <span class="label">Tanggal Berakhir:</span>
                                                <span class="value">{{ $item->tanggal_berakhir ? \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-sync-alt me-2 text-primary"></i>
                                                <span class="label">Perpanjangan:</span>
                                                <span class="value">
                                                    <span class="badge {{ $item->perpanjangan > 0 ? 'bg-info' : 'bg-secondary' }} text-white">
                                                        {{ $item->perpanjangan > 0 ? 'Diperpanjang ' . $item->perpanjangan . ' kali' : 'Belum Diperpanjang' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gallery of Images -->
                                    <div class="row row-cols-2 row-cols-md-3 g-3 mt-3">
                                        @forelse ($item->gambar as $gambar)
                                            @php
                                                \Illuminate\Support\Facades\Log::info('Image path', ['path' => asset('storage/gambar/' . $gambar->gambar_barang)]);
                                            @endphp
                                            <div class="col">
                                                <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" 
                                                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'; console.log('Image load failed, using placeholder:', this.src)"
                                                     class="img-fluid rounded-3 border shadow-sm" 
                                                     alt="{{ $item->nama_barang }}" 
                                                     style="height: 120px; object-fit: cover; transition: transform 0.3s;">
                                            </div>
                                        @empty
                                            <p class="text-danger mb-0"><i class="fas fa-exclamation-circle me-2"></i>Tidak ada gambar tersedia.</p>
                                        @endforelse
                                        @if ($item->gambar->count() < 2)
                                            <p class="text-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Jumlah gambar: {{ $item->gambar->count() }} (Minimal 2 diperlukan)</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted"><i class="fas fa-info-circle me-2"></i>Tidak ada barang dalam transaksi ini.</p>
                        @endforelse
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" onclick="hideModal('detailModal-{{ $transaction->id_transaksi_penitipan }}')">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-lg border-0 text-center p-5 bg-light rounded-4">
                    <i class="fas fa-info-circle text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 fs-4">Belum ada transaksi barang titipan.</p>
                    <a href="{{ route('gudang.add.transaction') }}" class="btn btn-primary mt-2 rounded-pill"><i class="fas fa-plus me-2"></i>Tambah Transaksi</a>
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

    .btn.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .img-fluid {
        transition: transform 0.3s ease;
    }

    .img-fluid:hover {
        transform: scale(1.1);
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

    /* Gambar Styling */
    .row-cols-2 .col, .row-cols-md-3 .col {
        padding: 5px;
    }

    .img-fluid {
        border: 2px solid #e0e4e8;
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