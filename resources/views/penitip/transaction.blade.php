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
                        <h4 class="text-success fw-bold mb-3 text-center">Riwayat Transaksi Anda</h4>

                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari berdasarkan nama seller, produk, atau Order ID"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i>
                                    Cari</button>
                            </div>
                        </form>

                        <div class="d-flex gap-3 justify-content-center mb-4 flex-wrap">
                            <a href="{{ route('penitip.transaction', ['filter' => 'all']) }}"
                                class="btn {{ request('filter') == 'all' || !request('filter') ? 'btn-success' : 'btn-outline-success' }}">
                                Semua
                            </a>
                            <a href="{{ route('penitip.transaction', ['filter' => 'sold']) }}"
                                class="btn {{ request('filter') == 'sold' ? 'btn-success' : 'btn-outline-success' }}">
                                Terjual
                            </a>
                            <a href="{{ route('penitip.transaction', ['filter' => 'expired']) }}"
                                class="btn {{ request('filter') == 'expired' ? 'btn-success' : 'btn-outline-success' }}">
                                Kadaluarsa
                            </a>
                            <a href="{{ route('penitip.transaction', ['filter' => 'donated']) }}"
                                class="btn {{ request('filter') == 'donated' ? 'btn-success' : 'btn-outline-success' }}">
                                Donasi
                            </a>
                        </div>

                        <div class="transaction-list">
                            @forelse ($transaksis as $transaksi)
                                <div class="border rounded p-3 mb-3">
                                    <h5 class="fw-semibold mb-1">{{ $transaksi->barang->nama_barang }}</h5>
                                    <p class="text-muted mb-1">{{ $transaksi->barang->deskripsi }}</p>
                                    <p class="mb-1">
                                        <strong>Status:</strong>
                                        @if ($transaksi->status_transaksi == 'COMPLETED')
                                            <span class="text-success">Terjual</span>
                                        @elseif ($transaksi->status_transaksi == 'EXPIRED')
                                            <span class="text-danger">Kadaluarsa</span>
                                        @elseif ($transaksi->status_transaksi == 'DONATED')
                                            <span class="text-primary">Didonasikan</span>
                                        @else
                                            <span class="text-secondary">{{ $transaksi->status_transaksi }}</span>
                                        @endif
                                    </p>
                                    <p class="mb-2"><strong>Total:</strong>
                                        Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>

                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $transaksi->id }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>

                                    <!-- Modal Detail Transaksi -->
                                    <div class="modal fade" id="detailModal{{ $transaksi->id }}" tabindex="-1"
                                        aria-labelledby="detailModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Nama Barang:</strong> {{ $transaksi->barang->nama_barang }}
                                                    </p>
                                                    <p><strong>Deskripsi:</strong> {{ $transaksi->barang->deskripsi }}</p>
                                                    <p><strong>Seller:</strong> {{ $transaksi->barang->penitip->nama }}</p>
                                                    <hr>
                                                    <p><strong>No. Order:</strong> #{{ $transaksi->id }}</p>
                                                    <p><strong>Status:</strong> {{ $transaksi->status_transaksi }}</p>
                                                    <p><strong>Tanggal Pembelian:</strong>
                                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->translatedFormat('d F Y') }}
                                                    </p>
                                                    <p><strong>Waktu Pembayaran:</strong>
                                                        {{ $transaksi->waktu_pembayaran }}</p>
                                                    <p><strong>No Resi:</strong> {{ $transaksi->no_resi ?? '-' }}</p>
                                                    <p><strong>Bukti Transfer:</strong><br>
                                                        @if ($transaksi->bukti_tf)
                                                            <img src="{{ asset('storage/' . $transaksi->bukti_tf) }}"
                                                                alt="Bukti Transfer" class="img-fluid rounded"
                                                                style="max-height: 200px;">
                                                        @else
                                                            <span class="text-muted">Belum tersedia</span>
                                                        @endif
                                                    </p>
                                                    <hr>
                                                    <p><strong>Metode Pengiriman:</strong>
                                                        {{ $transaksi->metode_pengiriman }}</p>
                                                    <p><strong>Ongkir:</strong>
                                                        Rp{{ number_format($transaksi->ongkir, 0, ',', '.') }}</p>
                                                    <p><strong>Tanggal Ambil:</strong>
                                                        {{ $transaksi->tanggal_ambil ?? '-' }}</p>
                                                    <p><strong>Tanggal Pengiriman:</strong>
                                                        {{ $transaksi->tanggal_pengiriman ?? '-' }}</p>
                                                    <hr>
                                                    <p><strong>Total Harga Barang:</strong>
                                                        Rp{{ number_format($transaksi->total_harga_barang, 0, ',', '.') }}
                                                    </p>
                                                    <p><strong>Total Harga (Barang + Ongkir):</strong>
                                                        Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">Tidak ada transaksi pada kategori ini.</p>
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
