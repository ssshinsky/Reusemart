@extends('layouts.main')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('penitip.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-success fw-bold mb-3 text-center">Riwayat Penjualan Anda</h4>

                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari berdasarkan nama produk, kode barang, atau ID transaksi"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i>
                                    Cari</button>
                            </div>
                        </form>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="transaction-list">
                            @forelse ($penjualan as $item)
                                <div class="border rounded p-3 mb-3">
                                    <h5 class="fw-semibold mb-1">{{ $item->nama_barang }}</h5>
                                    <p class="text-muted mb-1">Kode: {{ $item->kode_barang }}</p>
                                    <p class="mb-1">
                                        <strong>Status:</strong>
                                        <span class="text-success">Terjual</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Harga Jual:</strong>
                                        Rp{{ number_format($item->harga_barang, 0, ',', '.') }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>Pendapatan Bersih:</strong>
                                        Rp{{ number_format($item->pendapatan, 0, ',', '.') }}
                                    </p>

                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $item->id_transaksi }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>

                                    <!-- Modal Detail Transaksi -->
                                    <div class="modal fade" id="detailModal{{ $item->id_transaksi }}" tabindex="-1"
                                        aria-labelledby="detailModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailModalLabel">Detail Penjualan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Kode Barang:</strong> {{ $item->kode_barang }}</p>
                                                    <p><strong>Nama Barang:</strong> {{ $item->nama_barang }}</p>
                                                    <p><strong>Harga Jual:</strong>
                                                        Rp{{ number_format($item->harga_barang, 0, ',', '.') }}</p>
                                                    <p><strong>Komisi (20%):</strong>
                                                        Rp{{ number_format($item->harga_barang * 0.2, 0, ',', '.') }}</p>
                                                    <p><strong>Pendapatan Bersih:</strong>
                                                        Rp{{ number_format($item->pendapatan, 0, ',', '.') }}</p>
                                                    <hr>
                                                    <p><strong>Tanggal Penitipan:</strong>
                                                        {{ $item->tanggal_masuk->translatedFormat('d F Y') }}</p>
                                                    <p><strong>Tanggal Terjual:</strong>
                                                        {{ $item->tanggal_terjual->translatedFormat('d F Y') }}</p>
                                                    <p><strong>ID Transaksi:</strong> #{{ $item->id_transaksi }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Tidak ada transaksi penjualan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .text-success {
            color: var(--primary-color) !important;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #019944;
            border-color: #019944;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            font-weight: 500;
            border: none;
            border-left: 4px solid transparent;
            color: #333;
            transition: all 0.3s;
        }

        .sidebar-menu a.active {
            background-color: var(--bg-light);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-menu a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .sidebar-menu i {
            margin-right: 10px;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
        }
    </style>
@endpush